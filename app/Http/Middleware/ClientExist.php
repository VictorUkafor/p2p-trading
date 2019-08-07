<?php

namespace App\Http\Middleware;

use Closure;

class ClientExist
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
        ->where('id', $id)->first();

        if(!$client){
            return response()->json([
                'errorMessage' => 'Unauthorized',
            ], 404); 
        }

        $request->client = $client;
        return $next($request);
    }
}
