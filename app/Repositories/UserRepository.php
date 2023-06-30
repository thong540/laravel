<?php

namespace App\Repositories;

//use App\Repositories\RepositoryInterface;
use App\Models\Role;
use App\Models\User;
use App\Models\UserRole;
use http\Env\Request;
use Illuminate\Validation\UnauthorizedException;

class UserRepository extends EloquentRepository
{

    private $total;
    public function getModel()
    {
        // TODO: Implement getModel() method.
        return User::class;
    }
    function setTotal($total)
    {
        $this->total = $total;
    }

    function getTotal()
    {
        return $this->total;
    }


    public function getUserByEmail($email)
    {
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

    public function listUser($page, $limit, $id ,$email = null, $fullName = null, $phoneNumber = null, $address = null, $role = null)
    {
        $query = $this->_model->select(User::TABLE . '.*', UserRole::_ROLE_ID . ' as role', Role::_NAME . ' as role_name')
            ->join(UserRole::TABLE, User::TABLE . '.' . User::_ID, UserRole::TABLE . '.' . UserRole::_USER_ID)
            ->join(Role::TABLE , UserRole::TABLE . '.' . UserRole::_ROLE_ID, Role::TABLE. '.' . Role::_ID);
        if($id) {
            $query = $query->where(User::TABLE . '.' . User::_ID, $id);
        }

        if ($email) {
            $query = $query->where(User::TABLE . '.' . User::_EMAIL,'LIKE','%'. $email . '%');
        }
        if ($fullName) {
            $query = $query->where(User::TABLE . '.' . User::_FULLNAME,'LIKE' ,'%' . $fullName . '%');
        }
        if ($phoneNumber) {
            $query = $query->where(User::TABLE . '.' . User::_PHONENUMBER,'LIKE' , '%' . $phoneNumber . '%');
        }
        if ($address) {
            $query = $query->where(User::TABLE . '.' . User::_ADDRESS,'LIKE' ,'%' . $address . "%");
        }
        if ($role) {
            $query = $query->where(UserRole::TABLE . '.' . UserRole::_ROLE_ID, $role);
        }
        $this->setTotal($query->count());
        $query = $query->limit($limit)->offset(($page - 1) * $limit);
        return $query->get()->toArray();

    }
}
