<?php

namespace App\Http\Controllers;

use App\Repositories\CustomerRepository;
use App\Repositories\UserRepository;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Firebase\JWT\JWT;
class Auth1Controller extends Controller
{
    private $userRepository;

    function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;

    }
    function GenerateToken ($user)
    {
        $secretKey  = env('JWT_KEY');
        $tokenId    = base64_encode(random_bytes(16));
//        $issuedAt   = new DateTimeImmutable();
//        $expire     = $issuedAt->modify('+6 minutes')->getTimestamp();
        $serverName = "your.server.name";
//        dd($user);
        $userID   = $user['id'];

        // Create the token as an array
        $data = [
//            'iat'  => $issuedAt->getTimestamp(),
            'jti'  => $tokenId,
            'iss'  => $serverName,
//            'nbf'  => $issuedAt->getTimestamp(),
            'exp'  => 1685413406,
            'data' => [
                'userID' => $userID,
                'name' => $user[User::_FULLNAME] ,
                'email' => $user[User::_EMAIL],

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


        return $this->responseData();


    }
}
