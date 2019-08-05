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
            'account_number' => 'required|numeric|digits:10',
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

        if($request->max > $request->user->wallet[$request->coin]){
            return response()->json([
                'errorMessage' => 'Insufficient balance'
            ], 404); 
        }

        
        $account = $request->user->bankAccounts()
        ->where('account_number', $request->account_number)->first();

        if(!$account){
            return response()->json([
                'errorMessage' => 'The selected bank account could not be found'
            ], 404);
        }

        $request->account = $account;
        return $next($request);
    }
}
