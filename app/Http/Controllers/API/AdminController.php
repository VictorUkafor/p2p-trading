<?php

namespace App\Http\Controllers\API;

use App\Model\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;


class AdminController extends Controller {

    public function signup() { 

        $email = config('p2p.admin_email');
        $password = config('p2p.admin_password');
        
        $adminSet = User::where('email', $email)->first();

        if($adminSet){
            return response()->json([
                'errorMessage' => 'Admin already exist',
            ], 401); 
        }

        $admin = new User;
        $admin->active = true;
        $admin->activation_token = '';
        $admin->first_name = 'admin';
        $admin->last_name = 'admin';
        $admin->date_of_birth = '1999-01-01';
        $admin->email = $email;
        $admin->password = Hash::make($password);
    
        if($admin->save()){
            return response()->json([
                'successMessage' => 'Admin created successfully',
            ], 201);   
        }

        return response()->json([
            'errorMessage' => 'Internal server error',
        ], 500); 

    }


    public function profile(Request $request) { 

        $admin = $request->admin;
    
        if($admin){
            return response()->json([
                'admin' => $admin,
            ], 200);   
        }

        return response()->json([
            'errorMessage' => 'Internal server error',
        ], 500); 

    }




    
}
