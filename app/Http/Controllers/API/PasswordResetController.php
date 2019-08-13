<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Notifications\PasswordResetRequest;
use App\Notifications\PasswordResetSuccess;
use Illuminate\Support\Facades\Hash;
use App\Model\User;
use App\Model\PasswordReset;

class PasswordResetController extends Controller
{

    /**
     * @SWG\POST(
     *     path="/api/v1/auth/password-reset/request",
     *     summary="Request for password reset",
     *     description="Sends link to user for password reset",
     *     operationId="create",
     *     tags={"password reset"},
     *     @SWG\Parameter(
     *         name="email",
     *         in="query",
     *         description="The email of the user",
     *         required=true,
     *         type="string"
     *     ),
     *     @SWG\Response(
     *         response="201",
     *         description="Operation successfull"
     *     ),
     *     @SWG\Response(
     *         response="500",
     *         description="Internal server error"
     *     ),
     *     @SWG\Response(
     *         response="400",
     *         description="Invalid input field"
     *     ),
     * )
     */ 

    public function create(Request $request){
        
        $user = $request->user;

        $passwordReset = new PasswordReset;
        $passwordReset->email = $user->email;
        $passwordReset->token = str_random(60);
        
        if ($passwordReset->save()){
            $user->notify(new PasswordResetRequest($passwordReset->token));
            
            return response()->json([
                'successMessage' => 
                'We have e-mailed your password reset link!'
                ], 201);
            }
            
            return response()->json([
                'errorMessage' => 'Internal server error'
            ], 500);
        
        }


    /**
     * @SWG\GET(
     *     path="/api/v1/auth/password-reset/find/{token}",
     *     summary="Checks if a reset token is valid",
     *     description="Checks if a reset token is valid for password reset",
     *     operationId="find",
     *     tags={"password reset"},
     *     @SWG\Response(
     *         response="200",
     *         description="Operation successfull"
     *     ),
     *     @SWG\Response(
     *         response="500",
     *         description="Internal server error"
     *     ),
     * )
     */ 


    public function find($token) {
        $passwordReset = PasswordReset::where('token', $token)->first();
        
        if (!$passwordReset)
            return response()->json([
                'errorMessage' => 'This password reset token is invalid.'
            ], 404);

        if (Carbon::parse($passwordReset->updated_at)->addMinutes(720)->isPast()) {
            $passwordReset->delete();
            return response()->json([
                'errorMessage' => 'This password reset token is invalid.'
            ], 404);
        }

        return response()->json($passwordReset, 200);
    }


    /**
     * @SWG\POST(
     *     path="/api/v1/auth/password-reset/reset/{token}",
     *     summary="Resets a password",
     *     description="Resets a password",
     *     operationId="reset",
     *     tags={"password reset"},
     *     @SWG\Parameter(
     *         name="password",
     *         in="query",
     *         description="The new password",
     *         required=true,
     *         type="string"
     *     ),
     *     @SWG\Parameter(
     *         name="password_confirmation",
     *         in="query",
     *         description="The new password confirmation",
     *         required=true,
     *         type="string"
     *     ),
     *     @SWG\Response(
     *         response="201",
     *         description="Operation successfull"
     *     ),
     *     @SWG\Response(
     *         response="500",
     *         description="Internal server error"
     *     ),
     *     @SWG\Response(
     *         response="400",
     *         description="Invalid input field"
     *     ),
     * )
     */ 


    public function reset(Request $request, $token){
        $passwordReset = PasswordReset::where('token', $token)->first();

        if (!$passwordReset)
            return response()->json([
                'errorMessage' => 'This password reset token is invalid.'
            ], 404);

        $user = User::where('email', $passwordReset->email)->first();

        if (!$user)
            return response()->json([
                'errorMessage' => "User can not be found."
            ], 404);

        $user->password = Hash::make($request->password);
        $user->save();

        $passwordReset->delete();

        $user->notify(new PasswordResetSuccess($passwordReset));

        return response()->json([
            'successMessage' => "Password reset successfull. You can login now"
        ], 201);
    }
}