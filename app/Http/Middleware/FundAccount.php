<?php

namespace App\Http\Middleware;

use App\Model\Bank;
use Closure;
use Validator;

class FundAccount
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
            'amount' => 'required|numeric',
        ]);

        if ($validator->fails()) {
            $errors = $validator->errors();
            return response()->json([
                'errors' => $errors
            ], 400);
        } 

        $accountNumber = $request->route('accountNumber');

        $account = Bank::where('account_number', $accountNumber)->first();

        if(!$account){
            return response()->json([
                'errorMessage' => 'Account can not be found',
            ]   , 404); 
        }

        $request->account = $account;
        return $next($request);
    }
}
