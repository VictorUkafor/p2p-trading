<?php

namespace App\Http\Controllers\API;

use App\Model\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Notifications\EmailConfirmation;

class UserController extends Controller {

    public function signup(Request $request) { 
        try{

            $user = new User;

            $user->email = $request->email;
            $user->activation_token = str_random(60);
            $user->save();
            
            $user->notify(new EmailConfirmation());                 
    
            return response()->json([
                'successMessage' => 'A confirmation mail has been sent to your email',
            ]   , 201);   

        } catch(Exception $e) {

            return response()->json([
                'errorMessage' => $e->getMessage(),
            ]   , 500); 

        }
    }


    public function login(Request $request){

        $user = User::where('email', $request->email)->first();

        $credentials = [
            'email' => $request->email, 
            'password' => $request->password, 
            'active' => 1
        ];
        
        $token = JWTAuth::attempt($credentials);
    
        try {
    
            if (!$user || !$token) {
                return response()->json([
                    'errorMessage' => 'Invalid email or password'
                ], 401);
            } 
            
    
        } catch (JWTException $e) {
    
            return response()->json([
                'errorMessage' => 'Internal server error'
            ], 500);
        }
    
        return response()->json([
            'token' => $token
        ], 201);
    
    }
    
}
