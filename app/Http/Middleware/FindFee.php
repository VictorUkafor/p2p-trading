<?php

namespace App\Http\Middleware;

use Closure;
use App\Model\Fee;

class FindFee
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $feeIds = [];
        
        $clients = $request->user->clients;
        $transfers = $request->user->wallet->transfers;

        foreach($clients as $client){
            if($client->transaction->fee){
               $feeIds[] = $client->transaction->fee->id; 
            }
            
        }

        foreach($transfers as $transfer){
            $feeIds[] = $transfer->fee->id;
        }

        $id = $request->route('feeId');
        $adminEmail = config('p2p.admin_email');

        $fee = Fee::find($id);         
        

        if(in_array($id, $feeIds) || $request->user->email === $adminEmail){
            $request->fee = $fee;
            return $next($request);; 
        }


        return response()->json([
            'errorMessage' => 'Fee not found',
        ], 404);

    }
}
