<?php

namespace App\Http\Middleware;

use Closure;

class FindBuy
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
        $id = $request->route('buyId');

        $buyCrypto = $request->user->buyCryptos()
        ->where('id', $id)->first();

        if(!$buyCrypto){
            return response()->json([
                'errorMessage' => 'Transaction not found',
            ]   , 404); 
        }

        $request->buyCrypto = $buyCrypto;
        return $next($request);
    }
}
