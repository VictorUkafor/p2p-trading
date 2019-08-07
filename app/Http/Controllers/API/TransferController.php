<?php

namespace App\Http\Controllers\API;

use App\Model\User;
use App\Model\Fee;
use App\Model\Transfer;
use App\Model\WalletAddress;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Notifications\SendCoin;
use App\Notifications\ReceiveCoin;

class TransferController extends Controller {

    public function generateAddress(Request $request) {
        
        $address = new WalletAddress;
        $address->wallet_id = $request->user->wallet->id;
        $address->coin = $request->coin;
        $address->balance = '0.0000';
        $address->address = bin2hex(random_bytes(16));

        if($address->save()){
            return response()->json([
                'wallet_address' => $address,
            ], 201);
        }

        return response()->json([
            'errorMessage' => 'Internal server error',
        ], 500);
    }


    public function addresses(Request $request) {
        
        $addresses = $request->user->wallet->addresses;

        if(!count($addresses)){
            return response()->json([
                'errorMessage' => 'No address found',
            ], 404);            
        }

        if(count($addresses)){
            return response()->json([
                'walletAddresses' => $addresses,
            ], 200);            
        }

        return response()->json([
            'errorMessage' => 'Internal server error',
        ], 500);

    }


    public function address($address) {
        
        $address = WalletAddress::where('address', $address)
        ->first();

        if(!$address){
            return response()->json([
                'errorMessage' => 'Invalid address',
            ], 404);            
        }
        
        return response()->json([
            'walletAddress' => $address,
        ], 200);            

    }


    public function fundWithUsername(Request $request) {

        $total_coin = $request->amount + ($request->amount * 0.4);

        $senderWallet = $request->user->wallet;

        if($total_coin > $senderWallet[$request->coin]){
            return response()->json([
                'errorMessage' => 'Insufficient fund'
            ], 401);
        }


        $fee = new Fee;
        $fee->amount = $request->amount * 0.4;
        $fee->status = 'completed';
        $fee->save();

        $receiver = User::where('email', $request->username)->first();
        $receiverWallet = $receiver->wallet;

        $senderWallet[$request->coin] -= $total_coin;
        $receiverWallet[$request->coin] += $request->amount;

        $transfer = new Transfer;
        $transfer->fee_id = $fee->id;
        $transfer->method = 'username';
        $transfer->amount = $request->amount;
        $transfer->coin = $request->coin;
        $transfer->sender_wallet_id = $senderWallet->id;
        $transfer->receiver_wallet_id = $receiverWallet->id;


        if($senderWallet->save() && $receiverWallet->save() &&
         $transfer->save()){

            if(!$request->user->notifications || 
            $request->user->notifications->email_notification){
                $request->user->notify(new SendCoin($transfer, $fee)); 
            }

            if(!$receiverWallet->user->notifications || 
            $receiverWallet->user->notifications->email_notification){
                $receiverWallet->user->notify(new ReceiveCoin($transfer, $fee)); 
            }

            return response()->json([
                'successMessage' => 'Wallet funded successfully',
            ], 201);
        }

        return response()->json([
            'errorMessage' => 'Internal server error',
        ], 500);

    }


    public function fundWithAddress(Request $request) {

        $total_coin = $request->amount + ($request->amount * 0.4);

        $senderWallet = $request->user->wallet;

        if($total_coin > $senderWallet[$request->coin]){
            return response()->json([
                'errorMessage' => 'Insufficient fund'
            ], 401);
        }


        $address = WalletAddress::where('address', $request->address)
        ->first();

        if(!$address || $address->coin !== $request->coin || 
        ($request->user->id === $address->wallet->user->id)){
            return response()->json([
                'errorMessage' => 'Invalid address'
            ], 401);
        }
        
        $fee = new Fee;
        $fee->amount = $request->amount * 0.4;
        $fee->status = 'completed';
        $fee->save();

        $address->balance += $request->amount;
        $address->wallet[$request->coin] += $request->amount;
        $senderWallet[$request->coin] -= $total_coin;

        $transfer = new Transfer;
        $transfer->fee_id = $fee->id;
        $transfer->method = 'address';
        $transfer->amount = $request->amount;
        $transfer->coin = $request->coin;
        $transfer->sender_wallet_id = $senderWallet->id;
        $transfer->receiver_wallet_id = $address->wallet->id;


        if($address->save() && $address->wallet->save() &&
        $senderWallet->save() && $transfer->save()){

            if(!$request->user->notifications || 
            $request->user->notifications->email_notification){
                $request->user->notify(new SendCoin($transfer, $fee)); 
            }

            if(!$address->wallet->user->notifications || 
            $address->wallet->user->notifications->email_notification){
                $address->wallet->user->notify(new ReceiveCoin($transfer, $fee)); 
            }

            return response()->json([
                'successMessage' => 'Wallet funded successfully',
            ], 201);
        }

        return response()->json([
            'errorMessage' => 'Internal server error',
        ], 500);

    }



}
