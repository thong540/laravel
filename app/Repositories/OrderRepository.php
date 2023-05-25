<?php
namespace App\Repositories;

//use App\Repositories\RepositoryInterface;
use App\Models\Order;
class OrderRepository extends EloquentRepository
{

    public function getModel()
    {
        // TODO: Implement getModel() method.
        return Order::class;
    }

}
