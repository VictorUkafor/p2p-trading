<?php

namespace App\Http\Controllers\API;

use App\Model\Bank;
use App\Model\BankAccount;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Notifications\PaymentMade;
use App\Notifications\PaymentDecline;
use App\Notifications\PaymentConfirm;
use App\Notifications\PaymentRefund;
use App\Notifications\TransactionComplete;


class BuyerClientController extends Controller{

    /**
     * @SWG\POST(
     *     path="/api/v1/ads/{adId}/make-payment/{clientId}",
     *     summary="Make payment for buying coin",
     *     description="Make payment for buying coin",
     *     operationId="makePayment",
     *     tags={"Selling coin"},
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


    public function makePayment(Request $request) { 

        $ad = $request->ad;
        $client = $request->client;

        if($client->status !== 'approved' || $ad->type !== 'Sell'){
            return response()->json([
                'errorMessage' => 'Unauthorized',
            ], 401);
        }


        $approveTime = strtotime($client->transaction->approved_time)/60;
        $timeNow = strtotime(date("Y-m-d h:i:s"))/60;

        if(($approveTime - $timeNow) > $ad->deadline){
            return response()->json([
                'errorMessage' => 'You have exceeded the deadline for this transaction',
            ], 401);
        }

        $transaction = $client->transaction;

        $request->account->balance -= $transaction->amount_in_cash;

        $client->transaction->status = 'paid';
        $transaction->fee->status = 'paid';
        $client->status = 'paid';

        if($request->account->save() && $client->save() &&
        $client->transaction->save() && $transaction->fee->save()){

            if(!$ad->creator->notifications || $ad->creator->notifications->email_notification){
                $ad->creator->notify(new PaymentMade($ad, $client)); 
            }

            return response()->json([
                'successMessage' => 'Payment made successfully',
            ], 201); 
        }

        return response()->json([
            'errorMessage' => 'Internal server error',
        ], 500); 

    } 


    /**
     * @SWG\POST(
     *     path="/api/v1/ads/{adId}/reject-payment/{clientId}",
     *     summary="Rejects the payment for a transaction",
     *     description="Rejects the payment for a transaction",
     *     operationId="declinePayment",
     *     tags={"Selling coin"},
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


    public function declinePayment(Request $request) { 

        $ad = $request->ad;
        $client = $request->client;

        if($ad->state === 'public' || $client->status !== 'paid' ||
        $ad->type !== 'Sell'){
            return response()->json([
                'errorMessage' => 'Unauthorized',
            ], 401);
        }


        $ad->creator->wallet[$ad->coin] += $ad->max;

        $client->transaction->status = 'declined';
        $client->transaction->fee->status = 'declined';
        $client->status = 'declined';
        $ad->state = 'public';

        if($ad->creator->wallet->save() && $client->transaction->save() &&
        $client->transaction->fee->save() && $client->save() && $ad->save()){

            if(!$client->user->notifications || $client->user->notifications->email_notification){
                $client->user->notify(new PaymentDecline($ad, $client)); 
            }

            return response()->json([
                'successMessage' => 'Payment or deposit declined successfully',
            ], 201); 
        }

        return response()->json([
            'errorMessage' => 'Internal server error',
        ], 500); 

    } 


    /**
     * @SWG\POST(
     *     path="/api/v1/ads/{adId}/confirm-payment/{clientId}",
     *     summary="Confirms the payment for a transaction",
     *     description="Confirms the payment for a transaction",
     *     operationId="confirmPayment",
     *     tags={"Selling coin"},
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

    public function confirmPayment(Request $request) { 

        $ad = $request->ad;
        $client = $request->client;

        if($ad->state === 'public' || $client->status !== 'paid'){
            return response()->json([
                'errorMessage' => 'Unauthorized',
            ], 401);
        }

        $transaction = $client->transaction;

        $account = BankAccount::find($ad->bank_account_id);

        $bank = Bank::where('account_number', $account->account_number)->first();

        $bank->balance += $transaction->amount_in_cash;

        $client->user->wallet[$transaction->coin] += $transaction->amount_in_coin;

        $ad->creator->wallet[$ad->coin] += ($ad->max - $transaction->amount_in_coin);

        $client->transaction->status = 'completed';
        $transaction->fee->status = 'completed';
        $client->status = 'completed';

        if($bank->save() && $ad->creator->wallet->save() && 
        $client->user->wallet->save() && $client->transaction->save() &&
        $transaction->fee->save() && $client->save()){

            if(!$ad->creator->notifications || $ad->creator->notifications->email_notification){
                $ad->creator->notify(new TransactionComplete($ad, $client)); 
            }

            if(!$client->user->notifications || $client->user->notifications->email_notification){
                $client->user->notify(new PaymentConfirm($ad, $client)); 
            }

            return response()->json([
                'successMessage' => 'Payment confirmed successfully',
            ], 201); 
        }

        return response()->json([
            'errorMessage' => 'Internal server error',
        ], 500); 

    } 


    /**
     * @SWG\POST(
     *     path="/api/v1/ads/{adId}/refund-payment/{clientId}",
     *     summary="Refunds the payment of a rejected transaction",
     *     description="Refunds the payment of a rejected transaction",
     *     operationId="refundPayment",
     *     tags={"Selling coin"},
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

    public function refundPayment(Request $request) { 

        $ad = $request->ad;
        $client = $request->client;

        if($ad->state !== 'public' || $client->status !== 'declined'){
            return response()->json([
                'errorMessage' => 'Unauthorized',
            ], 401);
        }


        $request->account->balance += $client->amount_in_cash;
        $client->status = 'refund';

        if($request->account->save() && $client->save()){

            if(!$client->user->notifications || $client->user->notifications->email_notification){
                $client->user->notify(new PaymentRefund($ad, $client)); 
            }

            return response()->json([
                'successMessage' => 'Payment refund successfully',
            ], 201); 
        }

        return response()->json([
            'errorMessage' => 'Internal server error',
        ], 500); 

    }  
    

}
