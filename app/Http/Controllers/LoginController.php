<?php

namespace App\Http\Controllers;

use App\Repositories\CustomerRepository;
use App\Repositories\UserRepository;
use http\Env\Response;
use Illuminate\Http\Request;

class LoginController extends Controller
{
    private $userRepository, $customerRepository;
    function __construct(UserRepository $userRepository, CustomerRepository $customerRepository)
    {
        $this->userRepository = $userRepository;

    }

    function login(Request $request)
    {
        $email = $request->input('email');
        $password = $request->input('password');

        if ($this->userRepository->login($email, $password))

    }

}
