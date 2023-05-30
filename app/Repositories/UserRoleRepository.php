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


}
