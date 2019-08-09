<?php

namespace App\Http\Controllers\API;

use App\Model\Bank;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Notifications\CreditAlert;

class BankController extends Controller {


    /**
     * @SWG\POST(
     *     path="/api/v1/banks",
     *     tags={"fake bank accounts"},
     *     summary="Creates a fake bank account",
     *     description="Creates a fake bank account",
     *     operationId="create",
     *     @SWG\Parameter(
     *         name="bank",
     *         in="query",
     *         description="The name of the bank",
     *         required=true,
     *         type="string"
     *     ),
     *     @SWG\Parameter(
     *         name="phone",
     *         in="query",
     *         description="The phone of the account owner",
     *         required=false,
     *         type="string"
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


    public function create(Request $request){

        if(!count($request->user->banks) && !$request->phone){
            return response()->json([
                'errorMessage' => 'Your phone is required',
            ], 400);
        } 


        $bvn = mt_rand((int)1111111111, (int)9999999999);

        if(count($request->user->banks)){
            $bvn = $request->user->banks()->first()->bvn;
        }

        $phone = null;
        if(count($request->user->banks)){
            $phone = $request->user->banks()->first()->phone;
        } else {
            $phone = $request->phone;
        }

        $account = new Bank;
        $account->user_id = $request->user->id;
        $account->bank = $request->bank;
        $account->account_name = $request->user->first_name.' '.$request->user->last_name;
        $account->date_of_birth = $request->user->date_of_birth;
        $account->account_number = mt_rand((int)1111111111, (int)9999999999);
        $account->card = mt_rand((int)50000000000, (int)9999999999);
        $account->bvn = $bvn;
        $account->phone = $phone;
        $account->balance = '0.00';

        if($account->save()){
            return response()->json([
                'successMessage' => 'Account created successfully',
                'account' => $account,
            ], 201);   
        }

        return response()->json([
            'errorMessage' => 'Internal server error',
        ], 500); 

    }


    /**
     * @SWG\GET(
     *     path="/api/v1/banks",
     *     tags={"fake bank accounts"},
     *     summary="Fetches all fake bank accounts of a user",
     *     description="Fetches all fake bank accounts of a user",
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

        $accounts = $request->user->banks;

        if(!count($accounts)){
            return response()->json([
                'errorMessage' => 'No account found!',
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
     * @SWG\GET(
     *     path="/api/v1/banks/{accountNumber}",
     *     tags={"fake bank accounts"},
     *     summary="Fetches a single bank account of a user",
     *     description="Fetches a single bank account of a user",
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

        $account = $request->account;

        if($account){
            return response()->json([
                'account' => $account,
            ], 200); 
        }

        return response()->json([
            'errorMessage' => 'Internal server error',
        ], 500); 

    }


    /**
     * @SWG\POST(
     *     path="/api/v1/banks/{accountNumber}",
     *     tags={"fake bank accounts"},
     *     summary="Funds an account",
     *     description="Funds an account",
     *     operationId="fund",
     *     @SWG\Parameter(
     *         name="amount",
     *         in="query",
     *         description="Amount to be funded",
     *         required=true,
     *         type="string"
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

    public function fund(Request $request){

        $account = $request->account;
        $account->balance += $request->amount;

        if($account->save()){

            if(!$account->user->notifications || 
            $account->user->notifications->email_notification){
                $account->user->notify(new CreditAlert($account, $request->amount)); 
            }

            return response()->json([
                'successMessage' => 'Account credited successfully',
                'account' => $account,
            ], 201);   
        }

        return response()->json([
            'errorMessage' => 'Internal server error',
        ], 500); 

    }


}
