<?php

namespace App\Http\Controllers\API;

use App\Model\BuyCrypto;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Notifications\BuyingCrypto;
use App\Notifications\BuyingCryptoSuccess;
use App\Notifications\BuyingCryptoCancel;

class BuyCryptoController extends Controller
{

    public function buyCrypto(Request $request) { 

        $user = $request->user;
        $buyingRate = null;

        if($request->cryptocurrency === 'BTC'){
            $buyingRate = config('p2p.btc_buying_rate');
        }

        if($request->cryptocurrency === 'LTC'){
            $buyingRate = config('p2p.ltc_buying_rate');
        }
        
        if($request->cryptocurrency === 'ETH'){
            $buyingRate = config('p2p.eth_buying_rate');
        }

        $cryptocurrency = $request->amount_in_naira / $buyingRate;

        $buy = new BuyCrypto;
        $buy->user_id = $user->id;
        $buy->cryptocurrency = $request->cryptocurrency;
        $buy->amount = $request->amount_in_naira;
        $buy->value = $cryptocurrency;
        $buy->payment_method = $request->payment_method;
        $buy->method_details = $request->method_details;

        if($buy->save()){

            if(!$user->notifications || $user->notifications->email_notification){
                $user->notify(new BuyingCrypto($user, $buy)); 
            }

            return response()->json([
                'transaction' => $buy,
            ], 201); 
        }

        return response()->json([
            'errorMessage' => 'Internal server error',
        ], 500); 

    } 
    
    
    public function allBuys() { 

        $buys = BuyCrypto::all();

        if(!count($buys)){
            return response()->json([
                'errorMessage' => 'Transactions can not be found',
            ], 404); 
        }

        if(count($buys)){
            return response()->json([
                'transactions' => $buys,
            ], 200); 
        }

        return response()->json([
            'errorMessage' => 'Internal server error',
        ], 500); 

    }     


    public function buys(Request $request) { 

        $buys = $request->user->buyCryptos;

        if(!count($buys)){
            return response()->json([
                'errorMessage' => 'Transactions can not be found',
            ], 404); 
        }

        if(count($buys)){
            return response()->json([
                'transactions' => $buys,
            ], 200); 
        }

        return response()->json([
            'errorMessage' => 'Internal server error',
        ], 500); 

    } 


    public function buy(Request $request) { 

        $buy = $request->buyCrypto;

        if($buy){
            return response()->json([
                'transaction' => $buy,
            ], 200); 
        }

        return response()->json([
            'errorMessage' => 'Internal server error',
        ], 500); 

    }  


    public function cancel(Request $request) { 
        
        $buy = $request->buyCrypto;
        $user = $buy->user;

        if($buy->status !== 'pending'){
            return response()->json([
                'errorMessage' => 'Unauthorized',
            ], 401); 
        }

        $buy->status = 'cancelled';

        if($buy->save()){

            if(!$user->notifications || $user->notifications->email_notification){
                $user->notify(new BuyingCryptoCancel($user, $buy)); 
            }

            return response()->json([
                'successMessage' => 'Transaction cancelled successfully',
                'transaction' => $buy,
            ], 201); 
        }

        return response()->json([
            'errorMessage' => 'Internal server error',
        ], 500); 

    }


    public function complete(Request $request) {   

        $buy = $request->buyCrypto;
        $user = $buy->user;

        if($buy->status !== 'pending'){
            return response()->json([
                'errorMessage' => 'Unauthorized',
            ], 401); 
        }

        $buy->status = 'completed';
        $user->wallet[$buy->cryptocurrency] += $buy->value;


        if($buy->save() && $user->wallet->save()){

            if(!$user->notifications || $user->notifications->email_notification){
                $user->notify(new BuyingCryptoSuccess($user, $buy)); 
            }

            return response()->json([
                'successMessage' => 'Transaction completed successfully',
                'transaction' => $buy,
            ], 201); 
        }

        return response()->json([
            'errorMessage' => 'Internal server error',
        ], 500); 

    }


}
