<?php

namespace App\Http\Middleware;

use App\Model\Bank;
use Closure;
use Validator;

class ValidateBankAccount
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
        if(!$request->user->bvn || !$request->user->bvn->verified){
            return response()->json([
                'errorMessage' => 'Please verify your BVN'
            ], 400);
        }

        $validator = Validator::make($request->all(), [
            'account_number' => 'required|numeric|digits:10|exists:banks,account_number',
            'internet_banking' => 'required'
        ]);

        if ($validator->fails()) {
            $errors = $validator->errors();
            return response()->json([
                'errors' => $errors
            ], 400);
        } 

        // get the user BVN
        $bvn = $request->user->bvn->bvn_number;

        // get the bank accounts of the user using their bvn
        $accountIds = Bank::where('bvn', $bvn)->pluck('id')->toArray();

        $account = Bank::where('account_number', $request->account_number)->first();

        if(!in_array($account->id, $accountIds)){
            return response()->json([
                'errorMessage' => 'This account does not belong to you'
            ], 400);
        }

        $request->account = $account;
        return $next($request);
    }
}
