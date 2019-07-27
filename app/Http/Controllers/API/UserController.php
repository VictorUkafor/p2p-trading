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
    
}
