<?php

namespace App\Http\Middleware;

use App\Model\Bank;
use Closure;
use Validator;

class ValidateCard
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
            'card' => 'required|numeric',
        ]);

        if ($validator->fails()) {
            $errors = $validator->errors();
            return response()->json([
                'errors' => $errors
            ], 400);
        } 

        $account = Bank::where('card', $request->card)->first();

        if(!$account){
            return response()->json([
                'errorMessage' => 'Invalid card details'
            ], 404);
        }

        
        $request->account = $account;
        return $next($request);
    }
}
