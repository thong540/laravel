<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class CheckToken
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
//        if (!isset($request->age) || $request->age <= 200) {
//            dd($request);
//            $data= [
//                'message'=> 'permission denied'
//            ];
//            return response()->json($data, 401);
//            //return $data;
//        }
//        return $next($request);
        try {
            $token = $request->header('access_token');

            if ($token ){
                $decode = JWT::decode($token, new Key(env('JWT_KEY'), 'HS512'));
                dd($decode);
            } else if ($request->input('access_token')){
                $token = $request->input('access_token');
                $decode = JWT::decode($token, new Key(env('JWT_KEY'), 'HS512'));
            }
            if($decode) {

                $request->attributes = (array) $decode;
                return $next($request);
        }
        } catch(\Exception $e) {

        }


    }
}
