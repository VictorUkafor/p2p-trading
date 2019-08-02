<?php

namespace App\Http\Middleware;

use Closure;
use App\Model\Commission;

class FindCommission
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
        $id = $request->route('commissionId');
        $adminEmail = config('p2p.admin_email');

        $commission = $request->user->commissions()
        ->where('id', $id)->first();

        if($request->user->email === $adminEmail){
            $commission = Commission::find($id);   
        }

        if(!$commission){
            return response()->json([
                'errorMessage' => 'Commission not found',
            ], 404); 
        }

        $request->commission = $commission;
        return $next($request);
    }
}
