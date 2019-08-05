<?php

namespace App\Http\Middleware;

use Closure;

class myAd
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

        if($ad->state !== 'public'){
            return response()->json([
                'errorMessage' => 'Unauthorized',
            ], 401); 
        }

        $request->ad = $ad;
        return $next($request);
    }
}
