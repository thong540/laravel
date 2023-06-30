<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Repositories\UserRepository;
use App\Repositories\UserRoleRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
class UserController extends Controller
{
    private $userRepo;
    private $userRoleRepo;
    public function __construct(UserRepository $userRepo, UserRoleRepository $userRoleRepo)
    {
        $this->userRepo = $userRepo;
        $this->userRoleRepo = $userRoleRepo;
    }
    private function checkPermissionUser($user, $roleExecutes)
    {
        if(!is_array($user)) {
            return in_array($user, $roleExecutes);
        }
        foreach ($roleExecutes as $roleExecute) {
            //dd($user);
           if ($user == $roleExecute) {
              // dd($user['role_id'], $roleExecute);
               return true;
           }
       }

        return false;
    }

    function getListUser(Request $request)
    {
        $limit = $request->input('limit', 5);
        $page = $request->input('page',1);
        $id = $request->input('id');
        $email = $request->input('email');
        $fullName = $request->input('fullName');
        $phoneNumber = $request->input('phoneNumber');
        $address = $request->input('address');
        $role = $request->input('role');
        $listUser = $this->userRepo->listUser($page, $limit, $id ,$email, $fullName, $phoneNumber, $address, $role);
        if (!$listUser) {
            $this->message = 'Error';
        }
        $data['data'] = $listUser;
        $data['total'] = $this->userRepo->getTotal();
        $this->status = 'success';
        $this->message = ' List user';
        return $this->responseData($data);
    }
    function createUser(Request $request)
    {
        $userInfo = (array)$request->attributes->get('user')->data;

        $checkPermission = $this->checkPermissionUser($userInfo['role']->role_id, [User::ADMIN,User::MANAGER, User::STAFF, User::USER]);
        if (!$checkPermission) {
            $this->message = 'User is not permission';
            goto next;
        }

//        $request->validate([
//            'email' => ['required|email|ends_with:@gmail.com' ],
//            'password' => ['required', 'string', Password::min(8)->mixedCase()->numbers()->symbols()->uncompromised(), 'confirmed'],
//            'fullName' => ['required','min:3','max:20'],
//            'address' => ['required','min:3','max:20'],
//            'phoneNumber' => ['required|min:10|regex:/(01)[0-9]{9}/']
//
//
//        ]);
        $request->validate([
           'email' => 'required',
            'password' => 'required',
            'fullName' => 'required',
            'address' => 'required',
            'phoneNumber' => 'required',
        ]);
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

        $newUserId = $this->userRepo->insertGetId($dataInsert);
        if (!$newUserId) {
            $this->message = 'No create a new user';
            goto next;
        }
//        dd($newUserId, intval($role));
        $checkCreateUserRole = $this->userRoleRepo->insert([
           'user_id' =>  $newUserId,
            'role_id' => $role,
            'created_at' => time(),
            'updated_at' => time(),
        ]);
        if(!$checkCreateUserRole) {
            $this->message = 'No create a new user';
            goto next;
        }
        $this->status = 'success';
        $this->message = 'message';
        next:
        return $this->responseData();

    }
    function updateUser(Request $request)
    {
//        $request->validate([
//            'email' => ['required|email|ends_with:@gmail.com' ],
//            'password' => ['required', 'string', Password::min(8)->mixedCase()->numbers()->symbols()->uncompromised(), 'confirmed'],
//            'fullName' => ['required','min:3','max:20'],
//            'address' => ['required','min:3','max:20'],
//            'phoneNumber' => ['required|min:10|regex:/(01)[0-9]{9}/']
//
//
//        ]);

        $request->validate([
            'email' => ['required' ],
            'fullName' => ['required'],
            'address' => ['required'],
            'phoneNumber' => ['required']


        ]);
        $userInfo = (array)$request->attributes->get('user')->data;

        $checkPermission = $this->checkPermissionUser($userInfo['role']->role_id, [User::ADMIN,User::MANAGER, User::STAFF, User::USER]);
        if (!$checkPermission) {
            $this->message = 'User is not permission';
            goto next;
        }

        $id = $request->input('id');
        $email = $request->input('email');
        $password = $request->input('password');
        $fullName = $request->input('fullName');
        $address = $request->input('address');
        $phoneNumber = $request->input('phoneNumber');
        $role = $request->input('role');

        $userId = $userInfo['userId'];
        $userRole = $userInfo['role']->role_id;

        if ($userId != $id && $userRole == User::USER) {
            $this->message = 'User is not permission';
            goto next;
        }

        $dataUpdate = [
            User::_EMAIL => $email,
            User::_PASSWORD => Hash::make($password),
            User::_FULLNAME => $fullName,
            User::_ADDRESS => $address,
            User::_PHONENUMBER => $phoneNumber,
            User::UPDATED_AT => time()

        ];
        $check = $this->userRepo->update($id, $dataUpdate);
        $chekUpdate = $this->userRoleRepo->updateRoleByUserIdAndRoleId($id, $role);
        if (!$check || !$chekUpdate ) {
            $this->message = 'No update user';
            goto next;
        }

        $this->status = 'success';
        $this->message = 'updated success';
        next:
        return $this->responseData();

    }
    function deleteUser(Request $request)
    {

        $userInfo = (array)$request->attributes->get('user')->data;
        $checkPermission = $this->checkPermissionUser($userInfo['role']->role_id, [User::ADMIN,User::MANAGER, User::STAFF]);
        if (!$checkPermission) {
            $this->message = 'user is not permisson';
            goto next;
        }
        $id = $request->input('id');

        $check = $this->userRepo->delete($id);
        if (!$check) {
            $this->message = 'No delete user';
            goto next;
        }
        $this->status = 'success';
        $this->message = 'message';
        next:
        return $this->responseData();
    }
}
