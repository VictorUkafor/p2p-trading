<?php

namespace App\Http\Middleware;

use Closure;
use Validator;

class ValidateBuy
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
            'amount_in_naira' => 'required|numeric',
            'payment_method' => 'required|in:bank,card',
            'method_details' => 'required',
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
