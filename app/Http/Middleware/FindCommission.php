<?php

namespace App\Http\Middleware;

use Closure;
use App\Model\Commission;

class FindCommission
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
        $commissionIds = [];
        
        $sales = $request->user->wallet->sales;
        $transfers = $request->user->wallet->transfers;

        foreach($sales as $sale){
            $commissionIds[] = $sale->commission->id;
        }

        foreach($transfers as $transfer){
            $commissionIds[] = $transfer->commission->id;
        }

        $id = $request->route('commissionId');
        $adminEmail = config('p2p.admin_email');

        $commission = Commission::find($id);         
        

        if(in_array($id, $commissionIds) || $request->user->email === $adminEmail){
            $request->commission = $commission;
            return $next($request);; 
        }


        return response()->json([
            'errorMessage' => 'Commission not found',
        ], 404);

    }
}
