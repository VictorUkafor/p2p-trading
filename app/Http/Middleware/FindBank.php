<?php

namespace App\Http\Middleware;

use Closure;
use Validator;

class FindBank
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
        $accountNumber = $request->route('accountNumber');

        $account = $request->user->banks()
        ->where('account_number', $accountNumber)->first();

        if(!$account){
            return response()->json([
                'errorMessage' => 'Account can not be found',
            ]   , 404); 
        }

        $request->account = $account;
        return $next($request);
    }
}
