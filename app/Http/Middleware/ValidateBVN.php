<?php

namespace App\Http\Middleware;

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
            'bvn_number' => 'required|numeric|digits:11',
            'first_name' => 'required',
            'last_name' => 'required',
            'phone' => 'required|numeric|digits:11',
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
