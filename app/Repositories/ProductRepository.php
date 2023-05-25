<?php
namespace App\Repositories;

//use App\Repositories\RepositoryInterface;
use App\Models\Product;
class ProductRepository extends EloquentRepository
{

    public function getModel()
    {
        // TODO: Implement getModel() method.
        return Product::class;
    }

}
