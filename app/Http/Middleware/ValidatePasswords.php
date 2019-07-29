<?php

namespace App\Http\Middleware;

use Closure;
use Validator;

class ValidatePasswords
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
            'password' => 'required|confirmed|min:7|alpha_num',
            'password_confirmation' => 'required|same:password'
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
