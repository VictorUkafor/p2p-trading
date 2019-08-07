<?php

namespace App\Http\Controllers\API;

use App\Model\Ad;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class AdController extends Controller
{

    public function createSellAd(Request $request) { 

        $user = $request->user;

        $sellAd = new Ad;
        $sellAd->user_id = $user->id;
        $sellAd->type = 'Sell';
        $sellAd->referenceNo = '#'.mt_rand((int)100000000, (int)999999999);
        $sellAd->coin = $request->coin;
        $sellAd->price_type = $request->price_type;
        $sellAd->price = $request->price;
        $sellAd->min = $request->min;
        $sellAd->max = $request->max;
        $sellAd->deadline = $request->deadline;

        $user->wallet[$request->coin] -= $request->max;

        if($sellAd->save() && $user->wallet->save()){
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
        $buyAd->referenceNo = '#'.mt_rand((int)1000000000, (int)9999999999);
        $buyAd->type = 'Buy';
        $buyAd->coin = $request->coin;
        $buyAd->price_type = $request->price_type;
        $buyAd->price = $request->price;
        $buyAd->min = $request->min;
        $buyAd->max = $request->max;
        $buyAd->deadline = $request->deadline;

        $request->account->balance -= $request->max * $request->price;

        if($buyAd->save() && $request->account->save()){
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

        $ads = Ad::where('state', 'public')->get();

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

        if($ad->state !== 'public' && $ad->state !== 'inactive'){
            return response()->json([
                'errorMessage' => 'Unauthorized',
            ], 401); 
        }

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

        if($ad->state !== 'public' && $ad->state !== 'inactive'){
            return response()->json([
                'errorMessage' => 'Unauthorized',
            ], 401); 
        }

        $remove = Ad::destroy($ad->id);
        $request->account->balance += ($ad->max * $ad->price);

        if($remove && $request->account->save()){
            return response()->json([
                'successMessage' => 'Ad removed successfully',
            ], 200);            
        }

        return response()->json([
            'errorMessage' => 'Internal server error',
        ], 500); 

    } 


}
