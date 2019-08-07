<?php

namespace App\Http\Controllers\API;

use App\Model\Client;
use App\Model\Fee;
use App\Model\Transaction;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Notifications\EngageAd;
use App\Notifications\TradeApprove;
use App\Notifications\TradeDecline;


class TransactionController extends Controller
{

    public function engageAd(Request $request) { 

        $user = $request->user;
        $ad = $request->ad;

        $client = new Client;
        $client->user_id = $user->id;
        $client->ad_id = $ad->id;

        $fee = new Fee;
        $fee->amount = $request->amount_in_cash * 0.4;

        $saveTransaction =  false;
        if($fee->save() && $client->save()){
            $transaction = new Transaction;
            $transaction->client_id = $client->id;
            $transaction->fee_id = $fee->id;
            $transaction->coin = $ad->coin;
            $transaction->amount_in_cash = $request->amount_in_cash;
            $transaction->amount_in_coin = $request->amount_in_coin; 
            $saveTransaction = $transaction->save();
        }

        $ad->state = 'inactive';

        if($saveTransaction && $ad->save()){

            if(!$ad->creator->notifications || $ad->creator->notifications->email_notification){
                $ad->creator->notify(new EngageAd($ad, $client)); 
            }

            return response()->json([
                'successMessage' => 'Ad engaged successfully',
            ], 201); 
        }

        return response()->json([
            'errorMessage' => 'Internal server error',
        ], 500); 

    } 

    
    public function approveTrade(Request $request) { 

        $ad = $request->ad;
        $client = $request->client;

        if($ad->state === 'public' || $client->status !== 'pending'){
            return response()->json([
                'errorMessage' => 'Unauthorized',
            ], 401);
        }

        $client->status = 'approved';
        $client->transaction->approved_time = date("Y-m-d h:i:sa");

        if($client->save() && $client->transaction->save()){

            if(!$client->user->notifications || $client->user->notifications->email_notification){
                $client->user->notify(new TradeApprove($ad, $client)); 
            }

            return response()->json([
                'successMessage' => 'Engaged trade approved successfully',
            ], 201); 
        }

        return response()->json([
            'errorMessage' => 'Internal server error',
        ], 500); 

    } 


    public function declineTrade(Request $request) { 

        $ad = $request->ad;
        $client = $request->client;

        if($ad->state === 'public' || $client->status !== 'pending'){
            return response()->json([
                'errorMessage' => 'Unauthorized',
            ], 401);
        }

        $client->status = 'cancelled';
        $ad->state = 'public';

        if($client->save() && $ad->save()){

            if(!$client->user->notifications || $client->user->notifications->email_notification){
                $client->user->notify(new TradeDecline($ad, $client)); 
            }

            return response()->json([
                'successMessage' => 'Engaged trade declined successfully',
            ], 201); 
        }

        return response()->json([
            'errorMessage' => 'Internal server error',
        ], 500); 

    } 


}
