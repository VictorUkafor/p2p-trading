<?php

namespace App\Http\Controllers\API;

use App\Model\Ad;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
// use App\Notifications\BuyingCrypto;
// use App\Notifications\BuyingCryptoSuccess;
// use App\Notifications\BuyingCryptoCancel;

class AdController extends Controller
{

    public function createSellAd(Request $request) { 

        $sellAd = new Ad;
        $sellAd->user_id = $request->user->id;
        $sellAd->type = 'sell';
        $sellAd->account_id = $request->account->id;
        $sellAd->coin = $request->coin;
        $sellAd->price_type = $request->price_type;
        $sellAd->price = $request->price;
        $sellAd->min = $request->min;
        $sellAd->max = $request->max;
        $sellAd->deadline = $request->deadline;

        if($sellAd->save()){
            return response()->json([
                'successMessage' => 'Sell trade ad created successfully',
                'ad' => $sellAd
            ], 201); 
        }

        return response()->json([
            'errorMessage' => 'Internal server error',
        ], 500); 

    } 


    public function createBuyAd(Request $request) { 

        $buyAd = new Ad;
        $buyAd->user_id = $request->user->id;
        $buyAd->type = 'buy';
        $buyAd->coin = $request->coin;
        $buyAd->price_type = $request->price_type;
        $buyAd->price = $request->price;
        $buyAd->min = $request->min;
        $buyAd->max = $request->max;
        $buyAd->deadline = $request->deadline;

        if($buyAd->save()){
            return response()->json([
                'successMessage' => 'Buy trade ad created successfully',
                'ad' => $buyAd
            ], 201); 
        }

        return response()->json([
            'errorMessage' => 'Internal server error',
        ], 500); 

    } 


    public function allAds(Request $request) { 

        $ads = Ad::all();

        if(!count($ads)){
            return response()->json([
                'errorMessage' => 'No trade ad found',
            ], 404);            
        }

        if(count($ads)){
            return response()->json([
                'trades' => $ads,
            ], 200);            
        }

        return response()->json([
            'errorMessage' => 'Internal server error',
        ], 500); 

    }  
    

    public function myAds(Request $request) { 

        $ads = $request->user->ads;

        if(!count($ads)){
            return response()->json([
                'errorMessage' => 'No trade ad found',
            ], 404);            
        }

        if(count($ads)){
            return response()->json([
                'trades' => $ads,
            ], 200);            
        }

        return response()->json([
            'errorMessage' => 'Internal server error',
        ], 500); 

    } 


    public function ad(Request $request) { 

        $ad = $request->ad;

        if(!$ad){
            return response()->json([
                'errorMessage' => 'No trade ad found',
            ], 404);            
        }

        if($ad){
            return response()->json([
                'trade' => $ad,
            ], 200);            
        }

        return response()->json([
            'errorMessage' => 'Internal server error',
        ], 500); 

    } 


    public function updateAd(Request $request) { 

        $ad = $request->ad;

        $ad->min = $request->min;
        $ad->max = $request->max;
        $ad->deadline = $request->deadline;
        $ad->state = $request->state;

        if($ad->save()){
            return response()->json([
                'successMessage' => 'Ad updated successfully',
                'ad' => $ad,
            ], 201);            
        }

        return response()->json([
            'errorMessage' => 'Internal server error',
        ], 500); 

    } 

    public function removeAd(Request $request) { 

        $ad = $request->ad;

        if(Ad::destroy($ad->id)){
            return response()->json([
                'successMessage' => 'Ad removed successfully',
            ], 200);            
        }

        return response()->json([
            'errorMessage' => 'Internal server error',
        ], 500); 

    } 


}
