<?php


namespace App\Http\Controllers;

use App\Http\Services\HistoryActivityService;
use App\Repositories\CustomerRepository;
use App\Repositories\UserRepository;
use App\Models\User;
use App\Repositories\UserRoleRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Firebase\JWT\JWT;
use App\Repositories\LoggerRepository;
use App\Models\Logger;

class AuthController extends Controller
{
    private $userRepository;
    private $historyActivityService;
    private  $userRoleRepo;

    function __construct(
        UserRepository $userRepository,
        UserRoleRepository $userRoleRepo,
        HistoryActivityService $historyActivityService
    )
    {
        $this->userRepository = $userRepository;
        $this->userRoleRepo = $userRoleRepo;
        $this->historyActivityService = $historyActivityService;
//        $this->loggerRepository = $loggerRepository;

    }

    function generateToken($user, $roleId = [])
    {
        $secretKey = env('JWT_KEY');
//        $tokenId = base64_encode(random_bytes(16));
//        $issuedAt   = new DateTimeImmutable();
//        $expire     = $issuedAt->modify('+6 minutes')->getTimestamp();
        //       $serverName = "server.name";
//        dd($user);
//        $userID = $user['id'];
        $dataInsert = [];
        // Create the token as an array
        if (!isset($roleId)) {
            $dataInsert = [
                'userId' => $user[User::_ID],
                'name' => $user[User::_FULLNAME],
                'email' => $user[User::_EMAIL],

            ];
        } else {
            $dataInsert = [
                'userId' => $user[User::_ID],
                'name' => $user[User::_FULLNAME],
                'email' => $user[User::_EMAIL],
                'role' => $roleId,

            ];
        }
        $data = [
            'exp' => time() + 7*24 * 60 * 60,
            'data' => $dataInsert,
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
//        $this->historyActivityService->logger();
//        dd(2123);
        $request->validate([
            'email' => ['required'],
            'password' => ['required']
        ]);
        $email = $request->input('email');
        $checkUser = $this->userRepository->getUserByEmail($email);
        if (!$checkUser) {
            $this->message = 'email is incorrect';
            goto next;
        }

        $roleUser = $this->userRoleRepo->getRoleByUserId($checkUser['id']);

        $token = $this->generateToken($checkUser, $roleUser);

        $data = [
            'access_token' => $token,
        ];
        $this->message = 'login success';
        $this->status = 'success';
        $this->historyActivityService->logger([
            Logger::_USER_ID => $checkUser['id'],
            Logger::_ACTION => 'Login',
            Logger::_TIME => time()]);
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


        ];

        $this->userRoleRepo->insert(
            [
                'user_id' => $getUserId,
                'role_id' => $role,
                'created_at' => time(),
                'updated_at' => time(),
            ]);
        $token = $this->generateToken($dataEncode, $role);
        $this->status = 'success';
        $this->message = 'Register success';
        $this->historyActivityService->logger([
            Logger::_USER_ID => $getUserId,
            Logger::_ACTION => 'Register',
            Logger::_TIME => time()
        ]);
        $data = [
            'access_token' => $token,
        ];
        next:
        return $this->responseData($data);


    }
}
