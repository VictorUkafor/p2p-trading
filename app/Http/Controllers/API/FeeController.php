<?php

namespace App\Http\Controllers\API;

use App\Model\Fee;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class FeeController extends Controller{

    /**
     * @SWG\Get(
     *     path="/api/v1/fees/all",
     *     summary="Gets all the fees paid by all users",
     *     description="Gets all the fees paid by all users",
     *     operationId="allFees",
     *     tags={"fees"},
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

    public function allFees(){
        $fees = Fee::all();

        if(!count($fees)){
            return response()->json([
                'errorMessage' => 'No fee found',
            ], 404); 
        }

        if(count($fees)){
            return response()->json([
                'fees' => $fees,
            ], 200); 
        }


        return response()->json([
            'errorMessage' => 'Internal server error',
        ], 500); 

    }


    /**
     * @SWG\GET(
     *     path="/api/v1/fees",
     *     summary="Gets all the fees paid by a user",
     *     description="Gets all the fees paid by a user",
     *     operationId="fees",
     *     tags={"fees"},
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

    public function fees(Request $request){
        $fees = [];
        
        $clients = $request->user->clients;
        $transfers = $request->user->wallet->transfers;

        foreach($clients as $client){
                $fees[] = $client->transaction->fee;
        }

        foreach($transfers as $transfer){
            $fees[] = $transfer->fee;
        }

        if(!count($fees)){
            return response()->json([
                'errorMessage' => 'No fee found',
            ], 404); 
        }

        if(count($fees)){
            return response()->json([
                'fees' => $fees,
            ], 200); 
        }


        return response()->json([
            'errorMessage' => 'Internal server error',
        ], 500); 

    }


    /**
     * @SWG\GET(
     *     path="/api/v1/fees/{feeId}",
     *     summary="Gets a fee paid by a user",
     *     description="Gets a fee paid by a user",
     *     operationId="fee",
     *     tags={"fees"},
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


    public function fee(Request $request){
        $fee = $request->fee;

        if($fee){
            return response()->json([
                'fee' => $fee,
            ], 200); 
        }

        return response()->json([
            'errorMessage' => 'Internal server error',
        ], 500); 

    }



}
