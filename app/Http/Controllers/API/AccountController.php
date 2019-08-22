<?php

namespace App\Http\Controllers\API;

use App\Model\Bvn;
use App\Model\BankAccount;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use therealsmat\Ebulksms\EbulkSMS;
use App\Notifications\sendOTP;

class AccountController extends Controller{


    /**
     * @SWG\Post(
     *     path="/api/v1/bvn",
     *     tags={"BVN Verification"},
     *     summary="Adds BVN",
     *     description="Add the BVN details of a user",
     *      operationId="bvn",
     *     @SWG\Parameter(
     *         name="bvn_number",
     *         in="query",
     *         description="The BVN number of the user",
     *         required=true,
     *         type="integer"
     *     ), 
     *     @SWG\Response(
     *         response="201",
     *         description="Operation successfull"
     *     ),
     *     @SWG\Response(
     *         response="400",
     *         description="Invalid input field"
     *     ),
     *     @SWG\Response(
     *         response="500",
     *         description="Internal server error"
     *     ),
     * )
     */  

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
            'errorMessage' => 'Internal server error333',
        ], 500); 
        
    }

    /**
     * @SWG\Put(
     *     path="/api/v1/bvn",
     *     tags={"BVN Verification"},
     *     summary="Update a user's BVN number",
     *     description="Updates wrong or invalid BVN number",
     *     operationId="bvnUpdate",
     *     @SWG\Parameter(
     *         name="bvn_number",
     *         in="query",
     *         description="The BVN number of the user",
     *         required=true,
     *         type="integer"
     *     ), 
     *     @SWG\Response(
     *         response="201",
     *         description="Operation successfull"
     *     ),
     *     @SWG\Response(
     *         response="400",
     *         description="Invalid input field"
     *     ),
     *     @SWG\Response(
     *         response="500",
     *         description="Internal server error"
     *     ),
     * )
     */ 

    public function bvnUpdate(Request $request) { 

        $user = $request->user;

        if(!$user->bvn || $user->bvn->verified){
            return response()->json([
                'errorMessage' => 'Unauthorized action'
            ], 401);
        }


        $code = mt_rand(100000, 999999);

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


    /**
     * @SWG\Get(
     *     path="/api/v1/bvn/otp-verification",
     *     tags={"BVN Verification"},
     *     summary="Validates OTP",
     *     description="Verifies the user's bvn provided",
     *     operationId="sendOTP",
     *     @SWG\Response(
     *         response="201",
     *         description="Operation successfull"
     *     ),
     *     @SWG\Response(
     *         response="500",
     *         description="Internal server error"
     *     ),
     * )
     */ 
    

    public function sendOTP(Request $request, EbulkSMS $sms) { 

        $user = $request->user;

        if(!$user->bvn){
            return response()->json([
                'errorMessage' => 'Unauthorized actionii'
            ], 401);
        }
            
        if($sms->getBalance() > 5){
            $sms->fromSender('P2P TRADING')
            ->composeMessage($user->bvn->otp_code." is your BVN verification code")
            ->addRecipients($user->phone)->send();
        } else {
            $user->notify(new SendOTP($user->bvn->otp_code));   
        }
        
        
        if($user->bvn->otp_code){
            return response()->json([
                'successMessage' => 'A verification code has been code sent to your phone',
            ], 200); 
        }
        
        return response()->json([
            'errorMessage' => 'Internal server error',
        ], 500); 
        
    }


    /**
     * @SWG\Post(
     *     path="/api/v1/bvn/verify-otp",
     *     tags={"BVN Verification"},
     *     summary="Sends OTP for BVN verification",
     *     description="Sends OTP to user's phone for verification",
     *     operationId="OTPVerification",
     *     @SWG\Parameter(
     *         name="otp",
     *         in="query",
     *         description="The OTP code sent to user's phone",
     *         required=true,
     *         type="integer"
     *     ), 
     *     @SWG\Response(
     *         response="201",
     *         description="Operation successfull"
     *     ),
     *     @SWG\Response(
     *         response="400",
     *         description="Invalid input"
     *     ),
     *     @SWG\Response(
     *         response="500",
     *         description="Internal server error"
     *     ),
     * )
     */ 

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


    /**
     * @SWG\Post(
     *     path="/api/v1/bank-accounts",
     *     tags={"Bank Accounts"},
     *     summary="Adds bank account to user's account",
     *     description="Adds bank account to user's account",
     *     operationId="addAccount",
     *     @SWG\Parameter(
     *         name="bank",
     *         in="query",
     *         description="The name of the bank",
     *         required=true,
     *         type="integer"
     *     ), 
     *     @SWG\Parameter(
     *         name="account_number",
     *         in="query",
     *         description="The bank account number to be added",
     *         required=true,
     *         type="integer"
     *     ), 
     *     @SWG\Parameter(
     *         name="internet_banking",
     *         in="query",
     *         description="The internet banking option",
     *         required=true,
     *         type="boolean"
     *     ), 
     *     @SWG\Response(
     *         response="201",
     *         description="Operation successfull"
     *     ),
     *     @SWG\Response(
     *         response="400",
     *         description="Invalid fields"
     *     ),
     *     @SWG\Response(
     *         response="500",
     *         description="Internal server error"
     *     ),
     * )
     */ 

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


    /**
     * @SWG\Put(
     *     path="/api/v1/bank-accounts/{accountId}",
     *     tags={"Bank Accounts"},
     *     summary="Updates a bank account details",
     *     description="Updates a bank account details",
     *     operationId="updateAccount",
     *     @SWG\Parameter(
     *         name="internet_banking",
     *         in="query",
     *         description="The internet banking option",
     *         required=true,
     *         type="boolean"
     *     ), 
     *     @SWG\Response(
     *         response="201",
     *         description="Operation successfull"
     *     ),
     *     @SWG\Response(
     *         response="400",
     *         description="Invalid fields"
     *     ),
     *     @SWG\Response(
     *         response="500",
     *         description="Internal server error"
     *     ),
     * )
     */ 

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


    /**
     * @SWG\Get(
     *     path="/api/v1/bank-accounts",
     *     tags={"Bank Accounts"},
     *     summary="Fetches all bank accounts entered by a user for transactions",
     *     description="Fetches all bank accounts entered by a user for transactions",
     *     operationId="accounts",
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


    /**
     * @SWG\Get(
     *     path="/api/v1/bank-accounts/{accountId}",
     *     tags={"Bank Accounts"},
     *     summary="Displays a single bank account details",
     *     description="Displays a single bank account details",
     *     operationId="account",
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

    public function account(Request $request){

        return response()->json([
            'account' => $request->account,
        ], 200);        

    }

}
