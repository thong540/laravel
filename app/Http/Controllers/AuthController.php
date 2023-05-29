<?php

namespace App\Http\Controllers;

use App\Repositories\CustomerRepository;
use App\Repositories\UserRepository;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class AuthController extends Controller
{
    private $userRepository, $customerRepository;

    function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;

    }

    function login(Request $request)
    {
        $request->validate([
            'email' => ['required|email|ends_with:@gmail.com'],
            'password' => ['required', 'string', Password::min(8)->mixedCase()->numbers()->symbols()->uncompromised(), 'confirmed']
        ]);
        $email = $request->input('email');
        $password = $request->input('password');

        try {
            $user = $this->userRepository->findOneField(User::_EMAIL, $email);
            if (!$user) {
                $this->message = 'user is incorrect';
                goto next;
            }
            $check = Hash::check($password, $user[0]['password']);
            if (!$check) {
                $this->message = 'password is incorrect';
                goto next;
            }
            $token = '';
            $dataResponse = [
                'access_token' => $token,
                'name' => $user[User::_FULLNAME],
                'email' => $user[User::_EMAIL],
                'user_id' => $user[User::_ID],
            ];


        } catch (\Exception $e) {
            $this->message = $e->getMessage();
            goto next;
        }

        $credentials = $request->only(['email', 'password']);
        if (!$token = Auth::attempt($credentials)) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }
        //            return $this->respondWithToken($token);
        $token = $this->respondWithToken($token);
        $this->message = 'login success';
        $this->status = 'success';
        next:
        return $this->responseData($dataResponse ?? []);
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
        try {
            $user = $this->userRepository->findOneField(User::_EMAIL, $email);
            if (!$user) {
                $this->message = '';
                $dataResponse = [
                    'access_token' => $token,
                    'name' => $user[User::_FULLNAME],
                    'email' => $user[User::_EMAIL],
                    'user_id' => $user[User::_ID],
                ];
            }

        } catch (\Exception $e) {

        }


        return $this->responseData();


    }
}
