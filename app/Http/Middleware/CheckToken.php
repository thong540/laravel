<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Firebase\JWT\SignatureInvalidException;
use Firebase\JWT\BeforeValidException;
use Firebase\JWT\ExpiredException;
use DomainException;
use InvalidArgumentException;
use UnexpectedValueException;

class CheckToken
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
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
        $token = $request->header('access_token');
        if (!$token) {
            $token = $request->input('access_token');
        }
        if (!$token) {
            return response()->json('Permission denied', 401);
        }
        try {
            $decoded = JWT::decode($token, new Key(env('JWT_KEY'), 'HS512'));

            $request->attributes->add(['user' => $decoded]);
            //dd($request->attributes);
            return $next($request);
        } catch (\Exception $e) {
            return response()->json('Permission denied', 401);
        }

//        try {
//            $token = $request->header('access_token');
//            if (isset($token)) {
//
//                $decoded = JWT::decode($token, new Key(env('JWT_KEY'), 'HS512'));
//
//
//            } else {
//                $token = $request->input('access_token');
//                if (isset($token)) {
//
//                    $decoded = JWT::decode($token, new Key(env('JWT_KEY'), 'HS512'));
//                }
//            }
//
//            if (isset($decoded)) {
//
//                $decoded_array = (array) $decoded;
//                $request->attributes->add(['user' => $decoded_array]);
////                dd($request->attributes);
//                return $next($request);
//            } else {
//                return response()->json('Permission denied', 401);
//            }
//        } catch (InvalidArgumentException $e) {
//            // provided key/key-array is empty or malformed.
//        } catch (DomainException $e) {
//            // provided algorithm is unsupported OR
//            // provided key is invalid OR
//            // unknown error thrown in openSSL or libsodium OR
//            // libsodium is required but not available.
//        } catch (SignatureInvalidException $e) {
//            // provided JWT signature verification failed.
//        } catch (BeforeValidException $e) {
//            // provided JWT is trying to be used before "nbf" claim OR
//            // provided JWT is trying to be used before "iat" claim.
//        } catch (ExpiredException $e) {
//            // provided JWT is trying to be used after "exp" claim.
//        } catch (UnexpectedValueException $e) {
//            // provided JWT is malformed OR
//            // provided JWT is missing an algorithm / using an unsupported algorithm OR
//            // provided JWT algorithm does not match provided key OR
//            // provided key ID in key/key-array is empty or invalid.
//        }

    }
}
