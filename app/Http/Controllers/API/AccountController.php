<?php

namespace App\Http\Controllers\API;

use App\Model\Bvn;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use therealsmat\Ebulksms\EbulkSMS;

class AccountController extends Controller
{
    public function bvn(Request $request) { 

        $user = $request->user;

        if($user->bvn){
            return response()->json([
                'errorMessage' => 'Unauthorized action'
            ], 401);
        }

        try{

            $code = mt_rand(1000000, 9999999);

            $bvn = new Bvn;
            $bvn->user_id = $user->id;
            $bvn->bvn_number = $request->bvn_number;
            $bvn->first_name = $request->first_name;
            $bvn->last_name = $request->last_name;
            $bvn->middle_name = $request->middle_name;
            $bvn->phone = $request->phone;
            $bvn->otp_code = $code;
            $bvn->save();

            $user->phone = $request->phone;
            $user->save();

            return response()->json([
                'successMessage' => 'BVN added successfull',
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

            $code = mt_rand(1000000, 9999999);

            $bvn = $user->bvn;
            $bvn->bvn_number = $request->bvn_number;
            $bvn->first_name = $request->first_name;
            $bvn->last_name = $request->last_name;
            $bvn->middle_name = $request->middle_name;
            $bvn->phone = $request->phone;
            $bvn->otp_code = $code;
            $bvn->save();

            $user->phone = $request->phone;
            $user->save();

            return response()->json([
                'successMessage' => 'BVN added successfull',
                'bvn' => $bvn,
            ], 200);

        } catch(Exception $e) {

            return response()->json([
                'errorMessage' => $e->getMessage(),
            ]   , 500); 

        }
        
    }


    public function sendOTP(Request $request, EbulkSMS $sms) { 

        $user = $request->user;

        if(!$user->bvn){
            return response()->json([
                'errorMessage' => 'Unauthorized action'
            ], 401);
        }

        try{
            
            $sms->fromSender('P2P TRADING')
            ->composeMessage($user->bvn->otp_code." is your BVN verification code")
            ->addRecipients($user->phone)->send();
            
            return response()->json([
            'successMessage' => 'A verification code has been code sent to your phone',
            'otp_code' => $user->bvn->otp_code
           ]   , 200); 

        } catch(Exception $e) {

            return response()->json([
                'errorMessage' => $e->getMessage(),
            ]   , 500); 

        }
        
    }


    public function OTPVerification(Request $request) { 

        $user = $request->user;

        if($user->bvn->otp_code == $request->otp){
            $user->bvn->otp_code = null;
            $user->bvn->verified = true;

            if($user->bvn->save()){
                return response()->json([
                    'successMessage' => 'Your BVN has been verified successfully'
                ], 200);
            }

            return response()->json([
                'errorMessage' => 'Internal server error',
            ]   , 500); 

        }
        
        return response()->json([
            'errorMessage' => 'Invalid OTP',
        ]   , 400); 

    }


}
