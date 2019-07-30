<?php

namespace App\Http\Controllers\API;

use App\Model\Bvn;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class AccountController extends Controller
{
    public function bvnVerification(Request $request) { 

        $user = $request->user;

        if($user->bvn){
            return response()->json([
                'errorMessage' => 'Unauthorized action'
            ], 401);
        }

        try{

            $bvn = new Bvn;
            $bvn->user_id = $user->id;
            $bvn->bvn_number = $request->bvn_number;
            $bvn->first_name = $request->first_name;
            $bvn->last_name = $request->last_name;
            $bvn->middle_name = $request->middle_name;
            $bvn->phone = $request->phone;
            $bvn->save();

            $user->phone = $request->phone;
            $user->save();

            return response()->json([
                'successMessage' => 'BVN verification successfull',
                'bvn' => $bvn,
            ], 200);

        } catch(Exception $e) {

            return response()->json([
                'errorMessage' => $e->getMessage(),
            ]   , 500); 

        }
        
    }


    public function bvnUpdate(Request $request) { 

        $user = $request->user;

        if(!$user->bvn){
            return response()->json([
                'errorMessage' => 'Unauthorized action'
            ], 401);
        }

        
        try{

            $bvn = $user->bvn;
            $bvn->bvn_number = $request->bvn_number;
            $bvn->first_name = $request->first_name;
            $bvn->last_name = $request->last_name;
            $bvn->middle_name = $request->middle_name;
            $bvn->phone = $request->phone;
            $bvn->save();

            $user->phone = $request->phone;
            $user->save();

            return response()->json([
                'successMessage' => 'BVN verification successfull',
                'bvn' => $bvn,
            ], 200);

        } catch(Exception $e) {

            return response()->json([
                'errorMessage' => $e->getMessage(),
            ]   , 500); 

        }
        
    }

}
