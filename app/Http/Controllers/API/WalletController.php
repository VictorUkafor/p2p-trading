<?php

namespace App\Http\Controllers\API;

use App\Model\Wallet;
use App\Model\BuyCrypto;
use App\Model\Commission;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class WalletController extends Controller
{
    public function wallet(Request $request) { 

        $wallet = $request->user->wallet;

        if(!$wallet){
            $wallet = new Wallet;
            $wallet->user_id = $request->user->id;
            $wallet->BTC = '0.0';
            $wallet->LTC = '0.0';
            $wallet->ETH = '0.0';
            $wallet->save();
        }

        if($wallet){
            return response()->json([
                'wallet' => $wallet,
            ], 200); 
        }

        return response()->json([
            'errorMessage' => 'Internal server error',
        ], 500); 

    }


    public function buyCrypto(Request $request) { 

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
        $buy->user_id = $request->user->id;
        $buy->cryptocurrency = $request->cryptocurrency;
        $buy->amount = $request->amount_in_naira;
        $buy->value = $cryptocurrency;
        $buy->payment_method = $request->payment_method;
        $buy->method_details = $request->method_details;

        if($buy->save()){
            return response()->json([
                'transaction' => $buy,
            ], 201); 
        }

        return response()->json([
            'errorMessage' => 'Internal server error',
        ], 500); 

    }    


    public function buys(Request $request) { 

        $buys = $request->user->buyCryptos;

        if(!count($buys)){
            return response()->json([
                'transaction' => 'Transactions can not be found',
            ], 404); 
        }

        if(count($buys)){
            return response()->json([
                'transaction' => $buys,
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


    public function cancelBuy(Request $request) { 

        $buy = $request->buyCrypto;
        $buy->status = 'cancelled';

        if($buy->save()){
            return response()->json([
                'successMessage' => 'Transaction cancelled successfully',
                'transaction' => $buy,
            ], 201); 
        }

        return response()->json([
            'errorMessage' => 'Internal server error',
        ], 500); 

    }


    public function completeBuy(Request $request) { 

        $buy = $request->buyCrypto;
        $buy->status = 'completed';

        if($buy->save()){
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
