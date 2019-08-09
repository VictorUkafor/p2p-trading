<?php

namespace App\Http\Controllers\API;

use App\Model\Ad;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class AdController extends Controller {

    /**
     * @SWG\POST(
     *     path="/api/v1/ads/sell",
     *     tags={"trade ads"},
     *     summary="Create a sell trade ad",
     *     description="Create a sell trade ad",
     *     operationId="createSellAd",
     *     @SWG\Parameter(
     *         name="coin",
     *         in="query",
     *         description="The cryptocurrency",
     *         required=true,
     *         type="string"
     *     ),
     *     @SWG\Parameter(
     *         name="price_type",
     *         in="query",
     *         description="Static or Dynamic price",
     *         required=true,
     *         type="string"
     *     ),
     *     @SWG\Parameter(
     *         name="price",
     *         in="query",
     *         description="The price for a coin",
     *         required=true,
     *         type="string"
     *     ),
     *     @SWG\Parameter(
     *         name="min",
     *         in="query",
     *         description="The minimum number of coin to be sold",
     *         required=true,
     *         type="string"
     *     ),
     *     @SWG\Parameter(
     *         name="max",
     *         in="query",
     *         description="The maximum number of coin to be sold",
     *         required=true,
     *         type="string"
     *     ),
     *     @SWG\Parameter(
     *         name="deadline",
     *         in="query",
     *         description="The deadline for the transaction after approval",
     *         required=true,
     *         type="string"
     *     ),
     *     @SWG\Response(
     *         response="201",
     *         description="Operation successfull"
     *     ),
     *     @SWG\Response(
     *         response="500",
     *         description="Internal server error"
     *     ),
     *     @SWG\Response(
     *         response="400",
     *         description="Invalid email field"
     *     ),
     * )
     */

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


    /**
     * @SWG\POST(
     *     path="/api/v1/ads/buy",
     *     tags={"trade ads"},
     *     summary="Create a buy trade ad",
     *     description="Create a buy trade ad",
     *     operationId="createBuyAd",
     *     @SWG\Parameter(
     *         name="coin",
     *         in="query",
     *         description="The cryptocurrency",
     *         required=true,
     *         type="string"
     *     ),
     *     @SWG\Parameter(
     *         name="price_type",
     *         in="query",
     *         description="Static or Dynamic price",
     *         required=true,
     *         type="string"
     *     ),
     *     @SWG\Parameter(
     *         name="price",
     *         in="query",
     *         description="The price for a coin",
     *         required=true,
     *         type="string"
     *     ),
     *     @SWG\Parameter(
     *         name="min",
     *         in="query",
     *         description="The minimum number of coin to be sold",
     *         required=true,
     *         type="string"
     *     ),
     *     @SWG\Parameter(
     *         name="max",
     *         in="query",
     *         description="The maximum number of coin to be sold",
     *         required=true,
     *         type="string"
     *     ),
     *     @SWG\Parameter(
     *         name="deadline",
     *         in="query",
     *         description="The deadline for the transaction after approval",
     *         required=true,
     *         type="string"
     *     ),
     *     @SWG\Response(
     *         response="201",
     *         description="Operation successfull"
     *     ),
     *     @SWG\Response(
     *         response="500",
     *         description="Internal server error"
     *     ),
     *     @SWG\Response(
     *         response="400",
     *         description="Invalid email field"
     *     ),
     * )
     */    

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


    /**
     * @SWG\GET(
     *     path="/api/v1/ads/all/trade",
     *     tags={"trade ads"},
     *     summary="View all public trade ads",
     *     description="View all public trade ads",
     *     operationId="allAds",
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

    public function allAds() { 

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
    
    /**
     * @SWG\GET(
     *     path="/api/v1/ads",
     *     tags={"trade ads"},
     *     summary="View all user's trade ads",
     *     description="View all user's trade ads",
     *     operationId="myAds",
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


    /**
     * @SWG\GET(
     *     path="/api/v1/ads/{adId}",
     *     tags={"trade ads"},
     *     summary="View a trade ads",
     *     description="View a single trade ads",
     *     operationId="ad",
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


    /**
     * @SWG\PUT(
     *     path="/api/v1/ads/{adId}",
     *     tags={"trade ads"},
     *     summary="Updates a trade ads",
     *     description="Updates a trade ads",
     *     operationId="updateAd",
     *     @SWG\Parameter(
     *         name="min",
     *         in="query",
     *         description="The minimum number of coin to be sold",
     *         required=false,
     *         type="string"
     *     ),
     *     @SWG\Parameter(
     *         name="max",
     *         in="query",
     *         description="The maximum number of coin to be sold",
     *         required=false,
     *         type="string"
     *     ),
     *     @SWG\Parameter(
     *         name="deadline",
     *         in="query",
     *         description="The deadline for the transaction after approval",
     *         required=false,
     *         type="string"
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


    /**
     * @SWG\POST(
     *     path="/api/v1/ads/{adId}/remove",
     *     tags={"trade ads"},
     *     summary="Removes a trade ads",
     *     description="Removes a trade ads",
     *     operationId="removeAd",
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
