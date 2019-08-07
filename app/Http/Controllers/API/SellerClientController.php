<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Notifications\PaymentConfirm;
use App\Notifications\RefundCoin;
use App\Notifications\TransactionComplete;
use App\Notifications\CoinDeposit;
use App\Notifications\DepositDecline;
use App\Notifications\BalanceRefund;

class SellerClientController extends Controller {

    public function depositCoin(Request $request) { 

        $ad = $request->ad;
        $client = $request->client;

        if($client->status !== 'approved' || $ad->type !== 'Buy'){
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

        if($client->transaction->amount_in_coin > $client->user->wallet[$ad->coin]){
            return response()->json([
                'successMessage' => 'Please fund your wallet',
            ], 201);
        }

        $ad->creator->wallet[$ad->coin] -= $client->transaction->amount_in_coin;

        $client->transaction->status = 'paid';
        $client->transaction->fee->status = 'paid';
        $client->status = 'paid';

        if($ad->creator->wallet->save() && $client->save() &&
        $client->transaction->save() && $client->transaction->fee->save()){

            if(!$ad->creator->notifications || $ad->creator->notifications->email_notification){
                $ad->creator->notify(new CoinDeposit($ad, $client)); 
            }

            return response()->json([
                'successMessage' => 'Coin deposited successfully',
            ], 201); 
        }

        return response()->json([
            'errorMessage' => 'Internal server error',
        ], 500); 

    } 


    public function declineCoin(Request $request) { 

        $ad = $request->ad;
        $client = $request->client;

        if($ad->state === 'public' || $client->status !== 'paid' ||
        $ad->type !== 'Buy'){
            return response()->json([
                'errorMessage' => 'Unauthorized',
            ], 401);
        }


        $request->account->balance += ($ad->max * $ad->price);
        
        $client->transaction->status = 'declined';
        $client->transaction->fee->status = 'declined';
        $client->status = 'declined';
        $ad->state = 'public';

        if($request->account->save() && $client->transaction->save() &&
        $client->transaction->fee->save() && $client->save() && $ad->save()){

            if(!$client->user->notifications || $client->user->notifications->email_notification){
                $client->user->notify(new DepositDecline($ad, $client));
            }

            return response()->json([
                'successMessage' => 'Payment or deposit declined successfully',
            ], 201); 
        }

        return response()->json([
            'errorMessage' => 'Internal server error',
        ], 500); 

    } 


    public function confirmDeposit(Request $request) { 

        $ad = $request->ad;
        $client = $request->client;

        if($ad->state === 'public' || $client->status !== 'paid'){
            return response()->json([
                'errorMessage' => 'Unauthorized',
            ], 401);
        }

        $transaction = $client->transaction;

        $ad->creator->wallet[$ad->coin] += $transaction->amount_in_coin;

        $request->account->balance += $transaction->amount_in_cash;

        $client->transaction->status = 'completed';
        $client->transaction->fee->status = 'completed';
        $client->status = 'completed';

        if($request->account->save() && $ad->creator->wallet->save() && 
        $client->save() && $client->transaction->save() && $client->transaction->fee->save()){

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


    public function refundCoin(Request $request) { 

        $ad = $request->ad;
        $client = $request->client;

        if($ad->state !== 'public' || $client->status !== 'declined'){
            return response()->json([
                'errorMessage' => 'Unauthorized',
            ], 401);
        }


        $client->user->wallet[$ad->coin] += $client->amount_in_coin;
        $client->status = 'refund';

        if($client->user->wallet->save() && $client->save()){

            if(!$client->user->notifications || $client->user->notifications->email_notification){
                $client->user->notify(new RefundCoin($ad, $client)); 
            }

            return response()->json([
                'successMessage' => 'Payment refund successfully',
            ], 201); 
        }

        return response()->json([
            'errorMessage' => 'Internal server error',
        ], 500); 

    }     


    public function refundBalance(Request $request) { 

        $ad = $request->ad;
        $client = $request->client;

        if($ad->state !== 'inactive' || $client->transaction->status !== 'completed'){
            return response()->json([
                'errorMessage' => 'Unauthorized',
            ], 401);
        }


        $transaction = $client->transaction;

        $balance = ($ad->max * $ad->price) - $transaction->amount_in_cash;

        if(!$balance){
            return response()->json([
                'errorMessage' => 'You have no refund',
            ], 401);
        }

        $request->account->balance += $balance;
        $transaction->status = 'refund balance';

        if($request->account->save() && $transaction->save()){

            if(!$ad->creator->notifications || $ad->creator->notifications->email_notification){
                $ad->creator->notify(new BalanceRefund($ad, $client)); 
            }

            return response()->json([
                'successMessage' => 'Balance refunded successfully',
            ], 201); 
        }

        return response()->json([
            'errorMessage' => 'Internal server error',
        ], 500); 

    }     


}
