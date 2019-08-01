<?php

namespace App\Http\Controllers\API;

use App\Model\Wallet;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;


class WalletController extends Controller
{
    public function wallet(Request $request) { 

        $wallet = $request->user->wallet;

        if(!$wallet){
            $wallet = new Wallet;
            $wallet->user_id = $request->user->id;
            $wallet->BTC = '0.00000';
            $wallet->LTC = '0.00000';
            $wallet->ETH = '0.00000';
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


}
