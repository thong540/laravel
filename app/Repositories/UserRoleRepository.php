<?php

namespace App\Repositories;

//use App\Repositories\RepositoryInterface;
use App\Models\UserRole;

class UserRoleRepository extends EloquentRepository
{

    public function getModel()
    {
        // TODO: Implement getModel() method.
        return UserRole::class;
    }

    public function findRole($user_id)
    {
        $roleUser = $this->findOneField('user_id', $user_id);
        return $roleUser[0]['role_id'];
    }

    public function getRoleByUserId($userId)
    {
        return $this->_model
            ->select(
//                UserRole::_USER_ID,
                UserRole::_ROLE_ID,)
            ->where(UserRole::_USER_ID, $userId)
            ->first();
    }

    public function updateRoleByUserIdAndRoleId($userId, $roleIdUpdate)
    {
        $roleId = $this->_model->where(UserRole::_USER_ID, $userId)->first()->toArray();
        $query = $this->_model->where(UserRole::_USER_ID, $userId)->where(UserRole::_ROLE_ID, $roleId['role_id'])->update([
                'user_id' => $userId,
                'role_id' => $roleIdUpdate,
                'updated_at' => time(),
            ]);
        return $query;
    }
}


