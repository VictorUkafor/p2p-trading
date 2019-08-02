<?php

namespace App\Http\Controllers\API;

use App\Model\SellCrypto;
use App\Model\Commission;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Notifications\SellingCrypto;
use App\Notifications\SellingCryptoSuccess;
use App\Notifications\SellingCryptoCancel;

class SellCryptoController extends Controller
{
    public function sellCrypto(Request $request) { 

        $user = $request->user;
        $sellingRate = null;
        $commissionRate = null;

        switch ($request->cryptocurrency) {
            case 'BTC':
            $sellingRate = config('p2p.btc_selling_rate');
            $commissionRate  = config('p2p.btc_selling_commission_rate');
            break;
            case 'LTC':
            $sellingRate = config('p2p.ltc_selling_rate');
            $commissionRate = config('p2p.ltc_selling_commission_rate');
            break;
            case 'ETH':
            $sellingRate = config('p2p.eth_selling_rate');
            $commissionRate  = config('p2p.eth_selling_commission_rate');
            break;
            default:
            $sellingRate = null;
            $commissionRate = null;
        } 
        
        
        $crypto_commission = $request->crypto_amount * $commissionRate;
        
        $totalCrypto = $request->crypto_amount + $crypto_commission;

        if($totalCrypto > $user->wallet[$request->cryptocurrency]){
            return response()->json([
                'errorMessage' => 'Insufficient balance',
            ], 401);
        }


        $crypto_in_naira = $request->crypto_amount * $sellingRate;
        $commission_in_naira = $crypto_commission * $sellingRate;

        $commission = new Commission;
        $commission->user_id = $user->id;
        $commission->amount = $commission_in_naira;
        $commission->value = $crypto_commission;

        $saved = false;
        if($commission->save()){
            $sell = new SellCrypto;
            $sell->user_id = $user->id;
            $sell->commission_id = $commission->id;
            $sell->bank_account_id = $request->bank_account_id;
            $sell->cryptocurrency = $request->cryptocurrency;
            $sell->amount = $crypto_in_naira;
            $sell->value = $request->crypto_amount;
            $saved = $sell->save();
        }


        $user->wallet[$request->cryptocurrency] -= $totalCrypto;
        
        if($saved && $user->wallet->save()){

            if(!$user->notifications || $user->notifications->email_notification){
                $user->notify(new SellingCrypto($user, $sell)); 
            }

            return response()->json([
                'successMessage' => 'Transaction saved successfully',
            ], 201); 
        }

        return response()->json([
            'errorMessage' => 'Internal server error',
        ], 500); 

    } 


    public function allSales() { 

        $sales = SellCrypto::all();

        if(!count($sales)){
            return response()->json([
                'errorMessage' => 'Transactions can not be found',
            ], 404); 
        }

        if(count($sales)){
            return response()->json([
                'transactions' => $sales,
            ], 200); 
        }

        return response()->json([
            'errorMessage' => 'Internal server error',
        ], 500); 

    } 


    public function sales(Request $request) { 

        $sales = $request->user->sellCryptos;

        if(!count($sales)){
            return response()->json([
                'errorMessage' => 'Transactions can not be found',
            ], 404); 
        }

        if(count($sales)){
            return response()->json([
                'transactions' => $sales,
            ], 200); 
        }

        return response()->json([
            'errorMessage' => 'Internal server error',
        ], 500); 

    } 


    public function sale(Request $request) { 

        $sale = $request->sellCrypto;

        if($sale){
            return response()->json([
                'transaction' => $sale,
            ], 200); 
        }

        return response()->json([
            'errorMessage' => 'Internal server error',
        ], 500); 

    }  


    public function cancel(Request $request) { 
        
        $sale = $request->sellCrypto;
        $user = $sale->user;

        if($sale->status !== 'pending'){
            return response()->json([
                'errorMessage' => 'Unauthorized',
            ], 401); 
        }

        $sale->status = 'cancelled';
        $user->wallet[$sale->cryptocurrency] += ($sale->value + $sale->commission->value);

        if($sale->save() && $user->wallet->save()){

            if(!$user->notifications || $user->notifications->email_notification){
                $user->notify(new SellingCryptoCancel($user, $sale)); 
            }

            return response()->json([
                'successMessage' => 'Transaction cancelled successfully',
                'transaction' => $sale,
            ], 201); 
        }

        return response()->json([
            'errorMessage' => 'Internal server error',
        ], 500); 

    }


    public function complete(Request $request) {   

        $sale = $request->sellCrypto;
        $user = $sale->user;

        if($sale->status !== 'pending'){
            return response()->json([
                'errorMessage' => 'Unauthorized',
            ], 401); 
        }

        $sale->status = 'completed';
        $sale->commission->status = 'completed';

        if($sale->save() && $sale->commission->save()){

            if(!$user->notifications || $user->notifications->email_notification){
                $user->notify(new SellingCryptoSuccess($user, $sale)); 
            }

            return response()->json([
                'successMessage' => 'Transaction completed successfully',
                'transaction' => $sale,
            ], 201); 
        }

        return response()->json([
            'errorMessage' => 'Internal server error',
        ], 500); 

    }


}
