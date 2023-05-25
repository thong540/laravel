<?php
namespace App\Repositories;

//use App\Repositories\RepositoryInterface;
use App\Models\Role;
class RoleRepository extends EloquentRepository
{

    public function getModel()
    {
        // TODO: Implement getModel() method.
        return Role::class;
    }

}
