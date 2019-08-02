<?php

namespace App\Http\Middleware;

use Closure;
use App\Model\SellCrypto;

class FindSale
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
        $id = $request->route('saleId');
        $adminEmail = config('p2p.admin_email');

        $sellCrypto = $request->user->sellCryptos()
        ->where('id', $id)->first();

        if($request->user->email === $adminEmail){
            $sellCrypto = SellCrypto::find($id);   
        }

        if(!$sellCrypto){
            return response()->json([
                'errorMessage' => 'Transaction not found',
            ]   , 404); 
        }

        $request->sellCrypto = $sellCrypto;
        return $next($request);
    }
}
