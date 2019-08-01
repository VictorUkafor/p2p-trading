<?php

namespace App\Http\Middleware;

use Closure;

class FindAdmin
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
        $adminEmail = config('p2p.admin_email');

        if($user->email !== $adminEmail){
            return response()->json([
                'errorMessage' => 'Unauthorized',
            ], 401); 
        }
            
        $request->admin = $user;
        return $next($request);
    }
}
