<?php
namespace App\Repositories;

//use App\Repositories\RepositoryInterface;
use App\Models\OrderProduct;
class OrderProductRepository extends EloquentRepository
{

    public function getModel()
    {
        // TODO: Implement getModel() method.
        return OrderProduct::class;
    }
    public function getInformationProductByOrderId($orderId)
    {

    }

}
