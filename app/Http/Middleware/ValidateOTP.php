<?php

namespace App\Http\Middleware;

use Closure;
use Validator;

class ValidateOTP
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
        $user = $request->user;

        if($user && !$user->bvn){
            return response()->json([
                'errorMessage' => 'Unauthorized action'
            ], 401);
        }

        $validator = Validator::make($request->all(), [
            'otp' => 'required|numeric',
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
