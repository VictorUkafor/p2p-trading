<?php

namespace App\Http\Middleware;

use Closure;
use Validator;

class ValidateFundUsername
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
        $email = $request->user->email;

        $validator = Validator::make($request->all(), [
            'username' => 'required|email|exists:users,email|notIn:'.$email,
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
