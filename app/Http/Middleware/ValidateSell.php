<?php

namespace App\Http\Middleware;

use Closure;
use Validator;

class ValidateSell
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
        $validator = Validator::make($request->all(), [
            'cryptocurrency' => 'required|in:BTC,LTC,ETH',
            'bank_account_id' => 'required|numeric',
            'crypto_amount' => 'required|numeric',
        ]);

        if ($validator->fails()) {
            $errors = $validator->errors();
            return response()->json([
                'errors' => $errors
            ], 400);
        }         
        
        
        $bank = $request->user->bankAccounts()
        ->where('id', $request->bank_account_id)->first();

        if(!$bank){
            return response()->json([
                'errorMessage' => 'The selected bank account could not be found'
            ], 404);
        }

        return $next($request);
    }
}
