<?php

namespace App\Http\Controllers\API;

use App\Model\Fee;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class FeeController extends Controller
{
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
