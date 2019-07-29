<?php

namespace App\Http\Middleware;

use App\Model\User;
use Closure;
use Validator;

class EmailExist
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
            'email' => 'required|email|exists:users,email',
        ]);

        if ($validator->fails()) {
            $errors = $validator->errors();
            return response()->json([
                'errors' => $errors
            ], 400);
        } 

        $user = User::where('email', $request->email)->first();

        $request->user = $user;
        return $next($request);
    }
}
