<?php

namespace App\Http\Controllers\API;

use App\Model\Bvn;
use App\Model\BankAccount;
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
        
        $code = mt_rand(1000000, 9999999);

        $bvn = new Bvn;
        $bvn->user_id = $user->id;
        $bvn->bvn_number = $request->bvn_number;
        $bvn->otp_code = $code;

        $user->phone = $request->phone;

        if($bvn->save() && $user->save()){
            return response()->json([
                'successMessage' => 'BVN added successfull',
                'bvn' => $bvn,
            ], 201);            
        }
        
        return response()->json([
            'errorMessage' => 'Internal server error',
        ], 500); 
        
    }


    public function bvnUpdate(Request $request) { 

        $user = $request->user;

        if(!$user->bvn || $user->bvn->verified){
            return response()->json([
                'errorMessage' => 'Unauthorized action'
            ], 401);
        }


        $code = mt_rand(1000000, 9999999);

        $bvn = $user->bvn;
        $bvn->bvn_number = $request->bvn_number;
        $bvn->otp_code = $code;

        $user->phone = $request->phone;

        if($bvn->save() && $user->save()){
            return response()->json([
                'successMessage' => 'BVN updated successfull',
                'bvn' => $bvn,
            ], 201);            
        }

        return response()->json([
            'errorMessage' => 'Internal server error',
        ], 500); 
        
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


    public function addAccount(Request $request){

        $account = $request->user->bankAccounts()
        ->where('account_number', $request->account_number)
        ->first();

        if($account){
            return response()->json([
                'errorMessage' => 'Account has been added already',
            ], 404); 
        }

        $account = new BankAccount;
        $account->user_id = $request->user->id;
        $account->account_name = $request->account->account_name;
        $account->account_number = $request->account->account_number;
        $account->bank = $request->account->bank;
        $account->internet_banking = $request->internet_banking;

        if($account->save()){
            return response()->json([
                'successMessage' => 'Your account has added successfully',
                'account' => $account
            ]   , 201);
        }
        
        return response()->json([
            'errorMessage' => 'Internal server error',
        ]   , 500); 

    }


    public function updateAccount(Request $request){

        $account = $request->account;

        $account->internet_banking = !$account->internet_banking;

        if($account->save()){
            return response()->json([
                'successMessage' => 'Your account has been updated successfully',
                'account' => $account
            ]   , 201);
        }
        
        return response()->json([
            'errorMessage' => 'Internal server error',
        ]   , 500); 

    }


    public function accounts(Request $request){

        $accounts = $request->user->bankAccounts;

        if(!count($accounts)){
            return response()->json([
                'errorMessage' => 'Accounts can not be found',
            ], 404);
        }

        if(count($accounts)){
            return response()->json([
                'accounts' => $accounts,
            ], 200);
        }
        
        return response()->json([
            'errorMessage' => 'Internal server error',
        ], 500); 

    }


    public function account(Request $request){

        return response()->json([
            'account' => $request->account,
        ], 200);        

    }

}
