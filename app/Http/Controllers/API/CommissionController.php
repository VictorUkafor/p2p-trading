<?php

namespace App\Http\Controllers\API;

use App\Model\Commission;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class CommissionController extends Controller
{
    public function allCommissions(){
        $commissions = Commission::all();

        if(!count($commissions)){
            return response()->json([
                'errorMessage' => 'No commission found',
            ], 404); 
        }

        if(count($commissions)){
            return response()->json([
                'commissions' => $commissions,
            ], 200); 
        }


        return response()->json([
            'errorMessage' => 'Internal server error',
        ], 500); 

    }


    public function commissions(Request $request){
        $commissions = [];
        
        $sales = $request->user->wallet->sales;
        $transfers = $request->user->wallet->transfers;

        foreach($sales as $sale){
            $commissions[] = $sale->commission;
        }

        foreach($transfers as $transfer){
            $commissions[] = $transfer->commission;
        }

        if(!count($commissions)){
            return response()->json([
                'errorMessage' => 'No commission found',
            ], 404); 
        }

        if(count($commissions)){
            return response()->json([
                'commissions' => $commissions,
            ], 200); 
        }


        return response()->json([
            'errorMessage' => 'Internal server error',
        ], 500); 

    }


    public function commission(Request $request){
        $commission = $request->commission;

        if($commission){
            return response()->json([
                'commission' => $commission,
            ], 200); 
        }

        return response()->json([
            'errorMessage' => 'Internal server error',
        ], 500); 

    }



}
