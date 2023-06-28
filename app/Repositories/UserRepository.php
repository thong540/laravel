<?php
namespace App\Repositories;

//use App\Repositories\RepositoryInterface;
use App\Models\User;
use http\Env\Request;

class UserRepository extends EloquentRepository
{

    public function getModel()
    {
        // TODO: Implement getModel() method.
        return User::class;
    }

    public function getUserByEmail($email) {
        return $this->_model
            ->select(
                User::_ID,
                User::_FULLNAME,
                User::_PHONENUMBER,
                User::_EMAIL,
                User::_PASSWORD
            )->where(User::_EMAIL, $email)
            ->first();
    }
    public function listUser(Request $request)
    {
        $limit = $request->input('limit');
        $page = $request->input('page');

    }
}
