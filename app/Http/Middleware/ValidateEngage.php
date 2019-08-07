<?php

namespace App\Http\Middleware;

use App\Model\Bank;
use Closure;
use Validator;

class ValidateEngage
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
        if($request->ad->state !== 'public' || 
        $request->ad->user_id === $request->user->id){
            return response()->json([
                'errorMessage' => 'Unauthorized',
            ], 401);
        }


        if($request->amount_in_cash < ($request->ad->min * $request->ad->price)){
            return response()->json([
                'errorMessage' => 'Transaction lower than Min',
            ], 401); 
        }


        if($request->amount_in_cash > ($request->ad->max * $request->ad->price)){
            return response()->json([
                'errorMessage' => 'Transaction higher than Max',
            ], 401); 
        }

        $validator = Validator::make($request->all(), [
            'amount_in_coin' => 'required|numeric',
            'amount_in_cash' => 'required|numeric',
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
