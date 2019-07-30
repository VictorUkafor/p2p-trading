<?php

namespace App\Http\Middleware;

use Closure;

class findAccount
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
        $id = $request->route('accountId');

        $account = $request->user->bankAccounts()
        ->where('id', $id)->first();

        if(!$account){
            return response()->json([
                'errorMessage' => 'Account can not be found',
            ]   , 404); 
        }

        $request->account = $account;
        return $next($request);
    }
}
