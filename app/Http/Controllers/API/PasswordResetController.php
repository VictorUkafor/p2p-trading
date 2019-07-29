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

    public function create(Request $request)
    {
        $user = $request->user;

        $passwordReset = new PasswordReset;
        $passwordReset->email = $user->email;
        $passwordReset->token = str_random(60);
        
        if ($user && $passwordReset->save()){
            $user->notify(new PasswordResetRequest($passwordReset->token));
            
            return response()->json([
                'successMessage' => 
                'We have e-mailed your password reset link!'
                ]);
            }
            
            return response()->json([
                'errorMessage' => 'Internal server error'
            ], 500);
        
        }


    /**
     * Find token password reset
     *
     * @param  [string] $token
     * @return [string] message
     * @return [json] passwordReset object
     */
    public function find($token)
    {
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



    public function reset(Request $request, $token)
    {
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