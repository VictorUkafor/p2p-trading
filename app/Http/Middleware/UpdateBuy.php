<?php

namespace App\Http\Middleware;

use Closure;
use Validator;

class UpdateBuy
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
            'min' => 'numeric',
            'max' => 'numeric',
            'deadline' => 'numeric',
        ]);

        if ($validator->fails()) {
            $errors = $validator->errors();
            return response()->json([
                'errors' => $errors
            ], 400);
        } 

        if($request->max < $request->min){
            return response()->json([
                'errorMessage' => 'Max must be greater than Min'
            ], 400);
        }


        return $next($request);
    }
}
