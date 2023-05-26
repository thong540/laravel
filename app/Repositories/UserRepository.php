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


}
