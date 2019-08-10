<?php

namespace App\Http\Controllers\API;

use App\Model\Wallet;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;


class WalletController extends Controller
{

    /**
     * @SWG\GET(
     *     path="/api/v1/wallet",
     *     tags={"wallet"},
     *     summary="displays a user wallet",
     *     description="Displays the content of a user wallet",   
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
