<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Repositories\UserRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class Auth1Controller extends Controller
{
    private $userRepository, $customerRepository;

    function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;

    }

    function login(Request $request)
    {

        $request->validate([
            'email' => ['required'],
            'password' => ['required']
        ]);
//        $email = $request->input('email');
//        $password = $request->input('password');

        try {
            $credentials = $request->only(['email', 'password']);
            if (!$token = Auth::attempt($credentials)) {
                return response()->json(['error' => 'Unauthorized'], 401);
            }

        } catch (\Exception $e) {
            $this->message = $e->getMessage();
            goto next;
        }
//        try {
//            // attempt to verify the credentials and create a token for the user
//            //$token = JWTAuth::getToken();
//            $token = JWTAuth::getToken();
//            dd($token);
//
//            $apy = JWTAuth::getPayload($token)->toArray();
//
//        } catch (\Tymon\JWTAuth\Exceptions\TokenExpiredException $e) {
//
//            return response()->json(['token_expired'], 500);
//
//        } catch (\Tymon\JWTAuth\Exceptions\TokenInvalidException $e) {
//
//            return response()->json(['token_invalid'], 500);
//
//        } catch (\Tymon\JWTAuth\Exceptions\JWTException $e) {
//
//            return response()->json(['token_absent' => $e->getMessage()], 500);
//
//        }

        $data = [
            'access_token' => $token,
//            'decode' => $apy
//            'name' => $user[0][User::_FULLNAME],
//            'email' => $user[0][User::_EMAIL],
//            'user_id' => $user[0][User::_ID],
        ];
        $this->message = 'login success';
        $this->status = 'success';
        next:
        return $this->responseData($data ?? []);

    }

    function register(Request $request)
    {
        $request->validate([
            'email' => ['required|email|ends_with:@gmail.com'],
            'password' => ['required', 'string', Password::min(8)->mixedCase()->numbers()->symbols()->uncompromised(), 'confirmed'],
            'fullName' => ['required', 'min:3', 'max:20'],
            'address' => ['required', 'min:3', 'max:20'],
            'phoneNumber' => ['required|min:10|regex:/(01)[0-9]{9}/']


        ]);
        $email = $request->input('email');
        $password = $request->input('password');
        $fullName = $request->input('fullName');
        $address = $request->input('address');
        $phoneNumber = $request->input('phoneNumber');
        $dataInsert = [
            User::_EMAIL => $email,
            User::_PASSWORD => Hash::make($password),
            User::_FULLNAME => $fullName,
            User::_ADDRESS => $address,
            User::_PHONENUMBER => $phoneNumber,
            User::_CREATED_AT => time(),
            User::_UPDATED_AT => time(),
        ];
        $check = $this->userRepository->insert($dataInsert);
        $credentials = $request->only(['email', 'password']);
        $token = Auth::attempt($credentials);
        if (!$check) {
            $this->message = 'No register';
            goto next;
        }

        $this->status = 'success';
        $this->message = 'Register success';
        next:
        $token = $this->respondWithToken($token);

//
//                $dataResponse = [
//                    'access_token' => $token,
//                    'name' => $fullName,
//                    'email' => $email,
//                    'user_id' => ,
//                ];


        return $this->responseData();


    }
}
