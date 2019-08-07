<?php

namespace App\Http\Middleware;

use Closure;

class Findclient
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

        $id = $request->route('clientId');
        
        $client = $request->ad->clients()
        ->where([
            'user_id' => $request->user->id,
            'id' => $id
            ])->first();

        if(!$client){
            return response()->json([
                'errorMessage' => 'Unauthorized',
            ], 401); 
        }

        $request->client = $client;
        return $next($request);
    }
}
