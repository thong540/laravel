<?php

namespace App\Http\Controllers;

use App\Repositories\UserRoleRepository;
use Illuminate\Http\Request;

class UserRoleController extends Controller
{
    private $userRoleRepo;

    public function __construct(UserRoleRepository $userRoleRepo)
    {
        $this->userRoleRepo = $userRoleRepo;
    }
    function findRole($user_id)
    {
        return $this->userRoleRepo->findOneField('user_id', $user_id);
    }
}
