<?php
namespace App\Repositories;

//use App\Repositories\RepositoryInterface;
use App\Models\Category;
use App\Models\Product;
class ProductRepository extends EloquentRepository
{

    public function getModel()
    {
        // TODO: Implement getModel() method.
        return Product::class;
    }

    public function getAllProduct() {
        return $this->_model->select(Product::TABLE . '.*', Category::TABLE . '.' . Category::_NAME . ' as category_name')
            ->join(Category::TABLE, Category::TABLE . '.' . Category::_ID, Product::TABLE . '.' . Product::_CATEGORY_ID)
            ->get();
    }


}
