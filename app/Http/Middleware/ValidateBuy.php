<?php

namespace App\Http\Middleware;

use App\Model\Bank;
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
            'card' => 'required|numeric',
        ]);

        if ($validator->fails()) {
            $errors = $validator->errors();
            return response()->json([
                'errors' => $errors
            ], 400);
        } 

        if($request->max < $request->min){
            return response()->json([
                'errorMessage' => 'Max must be greater than Min'
            ], 400);
        }

        $account = Bank::where('card', $request->card)->first();

        if(!$account){
            return response()->json([
                'errorMessage' => 'Invalid card details'
            ], 404);
        }

        if(($request->max * $request->price) > $account->balance){
            return response()->json([
                'errorMessage' => 'Insufficient fund'
            ], 401);
        }

        $request->account = $account;
        return $next($request);
    }
}
