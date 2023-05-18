<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckAge
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
       // dd($request->age);
        if (!isset($request->age) || $request->age <= 200) {
            dd($request);
            $data= [
                'message'=> 'permission denied'
            ];
            return response()->json($data, 401);
            //return $data;
        }
        return $next($request);
    }
}
