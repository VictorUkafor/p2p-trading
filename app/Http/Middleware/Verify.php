<?php

namespace App\Http\Middleware;

use Closure;

class Verify
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
                'errorMessage' => 'Please verify your account'
            ], 401);
        }


        return $next($request);
    }
}
