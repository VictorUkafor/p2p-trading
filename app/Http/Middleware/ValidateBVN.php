<?php

namespace App\Http\Middleware;

use App\Model\Bank;
use Closure;
use Validator;

class ValidateBVN
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
            'bvn_number' => 'required|numeric|exists:banks,bvn',
        ]);

        if ($validator->fails()) {
            $errors = $validator->errors();
            return response()->json([
                'errors' => $errors
            ], 400);
        } 


        $bvn = Bank::where('bvn', $request->bvn_number)->first();

        if(!$bvn){
            return response()->json([
                'errorMessage' => 'Invalid bvn'
            ], 400); 
        }

        $request->phone = $bvn->phone;
        return $next($request);
    }
}
