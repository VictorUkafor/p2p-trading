<?php

namespace App\Http\Middleware;

use Closure;
use App\Model\Ad;

class FindAd
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

        $ad = Ad::find($id);

        if(!$ad){
            return response()->json([
                'errorMessage' => 'Ad could not be found',
            ], 404); 
        }

        $request->ad = $ad;
        return $next($request);
    }
}
