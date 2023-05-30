<?php

namespace App\Http\Controllers;

use App\Repositories\CustomerRepository;
use App\Repositories\UserRepository;
use App\Models\User;
use App\Repositories\UserRoleRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Firebase\JWT\JWT;

class Auth1Controller extends Controller
{
    private $userRepository, $userRoleRepo;

    function __construct(UserRepository $userRepository, UserRoleRepository $userRoleRepo)
    {
        $this->userRepository = $userRepository;
        $this->userRoleRepo = $userRoleRepo;

    }

    function GenerateToken($user)
    {
        $secretKey = env('JWT_KEY');
        $tokenId = base64_encode(random_bytes(16));
//        $issuedAt   = new DateTimeImmutable();
//        $expire     = $issuedAt->modify('+6 minutes')->getTimestamp();
        $serverName = "server.name";
//        dd($user);
        $userID = $user['id'];

        // Create the token as an array
        $data = [
//            'iat'  => $issuedAt->getTimestamp(),
            'jti' => $tokenId,
            'iss' => $serverName,
//            'nbf'  => $issuedAt->getTimestamp(),
            'exp' => 1688123760,
            'data' => [
                'userID' => $userID,
                'name' => $user[User::_FULLNAME],
                'email' => $user[User::_EMAIL],
                'role' => $user['role'],

            ]
        ];

        // Encode the array to a JWT string.
        $token = JWT::encode(
            $data,
            $secretKey,
            'HS512'
        );
        return $token;
    }

    function login(Request $request)
    {

        $request->validate([
            'email' => ['required'],
            'password' => ['required']
        ]);
        $check = $this->userRepository->findOneField('email', $request->input('email'));
        $getRole = $this->userRoleRepo->findRole();
        $token = $this->GenerateToken($check[0]);

        $data = [
            'access_token' => $token,

        ];
        $this->message = 'login success';
        $this->status = 'success';
        next:
        return $this->responseData($data ?? []);


    }

    function register(Request $request)
    {
        $request->validate([
            'email' => ['required'],
            'password' => ['required'],
            'fullName' => ['required'],
            'address' => ['required'],
            'phoneNumber' => ['required'],
            'role' => ['required']


        ]);

        $data = [];
        $email = $request->input('email');
        $password = $request->input('password');
        $fullName = $request->input('fullName');
        $address = $request->input('address');
        $phoneNumber = $request->input('phoneNumber');
        $role = $request->input('role');
        $dataInsert = [
            User::_EMAIL => $email,
            User::_PASSWORD => Hash::make($password),
            User::_FULLNAME => $fullName,
            User::_ADDRESS => $address,
            User::_PHONENUMBER => $phoneNumber,
            User::_CREATED_AT => time(),
            User::_UPDATED_AT => time(),
        ];
        $checkUser = $this->userRepository->findOneField(User::_EMAIL, $email);
//        $credentials = $request->only(['email', 'password']);
//        $token = Auth::attempt($credentials);
        if ($checkUser) {
            $this->message = 'Email is exist';
            $this->status = 'failure';
            goto next;
        }
        $checkDataInsert = $this->userRepository->insertGetId($dataInsert);

        if (!$checkDataInsert) {
            $this->message = 'No register';
            $this->status = 'failure';
            goto next;
        }

        $getUserId = $checkDataInsert;
        $dataEncode = [
            User::_ID => $getUserId,
            User::_EMAIL => $email,
            User::_FULLNAME => $fullName,
            'role' => $role,

        ];
        $token = $this->GenerateToken($dataEncode);
        $this->status = 'success';
        $this->message = 'Register success';

        $data = [
            'access_token' => $token,
        ];
        next:
        return $this->responseData($data);


    }
}