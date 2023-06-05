<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Auth;
use Tymon\JWTAuth\Facades\JWTAuth;
class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    protected $message = '';
    protected $status = 'failure';

    protected function responseData($data = [])
    {
        return [
            'status' => $this->status,
            'data' => $data,
            'message' => $this->message,
            'code' => 200
        ];
    }
//    protected function respondWithToken($token)
//    {
//        return response()->json([
//            'token' => $token,
//            'token_type' => 'bearer',
//            'expires_in' => Auth::factory()->getTTL() * 60
//        ], 200);
//    }
//    protected function verifyToken($request)
//    {
//        $header = $request->header('Authorization');
//        return $header;
//    }
}
