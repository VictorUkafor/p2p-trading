<?php

namespace App\Http\Middleware;

use Closure;
use Validator;

class ValidateFundAddress
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
            'address' => 'required|exists:wallet_addresses,address',
            'coin' => 'required|in:BTC,LTC,ETH',
            'amount' => 'required|numeric',
        ]);

        if ($validator->fails()) {
            $errors = $validator->errors();
            return response()->json([
                'errors' => $errors
            ], 400);
        } 

        return $next($request);
    }
}
