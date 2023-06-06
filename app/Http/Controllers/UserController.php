<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Repositories\UserRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
class UserController extends Controller
{
    private $userRepo;
    public function __construct(UserRepository $userRepo)
    {
        $this->userRepo = $userRepo;
    }
    private function checkPermissionUser($user, $roleExecutes)
    {
        if(!is_array($user)) {
            return in_array($user, $roleExecutes);
        }
        foreach ($roleExecutes as $roleExecute) {
            //dd($user);
           if ($user[0]->role_id == $roleExecute) {
              // dd($user['role_id'], $roleExecute);
               return true;
           }
       }

        return false;
    }

    function getAllusers()
    {
        $this->status = 'success';
        $this->message = 'get All Users';
        $users =$this->userRepo->getAll();
        return $this->responseData($users);
    }
    function createUser(Request $request)
    {
        $checkPermission = $this->checkPermissionUser($request->attributes->get('user'), [User::ADMIN,User::MANAGER, User::STAFF]);

        if (!$checkPermission) {
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

        $dataInsert = [
            User::_EMAIL => $email,
            User::_PASSWORD => Hash::make($password),
            User::_FULLNAME => $fullName,
            User::_ADDRESS => $address,
            User::_PHONENUMBER => $phoneNumber,
            User::_CREATED_AT => time(),
            User::_UPDATED_AT => time(),
        ];

        $check = $this->userRepo->insert($dataInsert);
        if (!$check) {
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
            'password' => ['required'],
            'fullName' => ['required'],
            'address' => ['required'],
            'phoneNumber' => ['required']


        ]);
        $userInfo = (array)$request->attributes->get('user')->data;
        $checkPermission = $this->checkPermissionUser($userInfo['role'], [User::ADMIN,User::MANAGER, User::STAFF, User::USER]);

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

        $userId = $userInfo['userId'];
        $userRole = $userInfo['role'][0]->role_id;

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
        if (!$check) {
            $this->message = 'No update user';
            goto next;
        }
        $this->status = 'success';
        $this->message = 'message';
        next:
        return $this->responseData();

    }
    function deleteUser(Request $request)
    {

        $userInfo = (array)$request->attributes->get('user')->data;
       // dd($userInfo);
        $checkPermission = $this->checkPermissionUser($userInfo['role'], [User::ADMIN,User::MANAGER, User::STAFF]);
        if (!$checkPermission) {
            $this->message = 'user is not permisson';
            goto next;
        }

        $id = $request->input('id');

        $check = $this->userRepo->delete($id);
//        dd($check);
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
