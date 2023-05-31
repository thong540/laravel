<?php
namespace App\Repositories;

//use App\Repositories\RepositoryInterface;
use App\Models\User;
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
                User::_EMAIL
            )->where(User::_EMAIL, $email)
            ->first();
    }
}
