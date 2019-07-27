<?php

namespace App\Http\Middleware;

use App\Model\User;
use Closure;
use Validator;

class ValidateSignup
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
        $user = User::where('activation_token', $request->route('token'))
        ->first();
            
        if (!$user) {
            return response()->json([
                'errorMessage' => 'This activation token is invalid.'
            ], 404);
        }
        
        $validator = Validator::make($request->all(), [
            'first_name' => 'required',
            'last_name' => 'required',
            'date_of_birth' => 'required|date',
            'password' => 'required|confirmed|min:7|alpha_num',
            'password_confirmation' => 'required|same:password'
        ]);

        if ($validator->fails()) {
            $errors = $validator->errors();
            return response()->json([
                'errors' => $errors
            ], 400);
        } 

        $request->user = $user;
        return $next($request);
    }
}
