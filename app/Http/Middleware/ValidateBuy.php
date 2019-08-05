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
            'coin' => 'required|in:BTC,LTC,ETH',
            'price_type' => 'required|in:static,dynamic',
            'price' => 'required|numeric',
            'min' => 'required|numeric',
            'max' => 'required|numeric',
            'deadline' => 'required|numeric',
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
