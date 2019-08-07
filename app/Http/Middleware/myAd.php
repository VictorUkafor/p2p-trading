<?php

namespace App\Http\Middleware;

use Closure;

class MyAd
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
        $id = $request->route('adId');

        $ad = $request->user->ads()->where('id', $id)->first();

        if(!$ad){
            return response()->json([
                'errorMessage' => 'Ad could not be found',
            ], 404); 
        }

        
        $request->ad = $ad;
        return $next($request);
    }
}
