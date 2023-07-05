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

    private $total;

    public function getAllProduct()
    {
        return $this->_model->select(Product::TABLE . '.*', Category::TABLE . '.' . Category::_NAME . ' as category_name')
            ->join(Category::TABLE, Category::TABLE . '.' . Category::_ID, Product::TABLE . '.' . Product::_CATEGORY_ID)
            ->get();
    }

    public function getListProduct($page = null , $limit= null, $id = null, $name = null, $category = null)
    {
//        dd(123);
//        dd($category, $name, $id, $limit, $page);
        $query = $this->_model->select(Product::TABLE . '.*', Category::TABLE . '.' . Category::_NAME . ' as category_name')
            ->join(Category::TABLE, Category::TABLE . '.' . Category::_ID, Product::TABLE . '.' . Product::_CATEGORY_ID);
        if ($id) {
            $query = $query->where(Product::TABLE . '.' . Product::_ID, $id);
        }
        if ($name) {
            $query = $query->where(Product::TABLE . '.' . Product::_NAME, 'LIKE', '%' . $name . '%');
        }
        if ($category) {
            $query = $query->where(Category::TABLE . '.' . Category::_ID, $category);
        }
//        dd($query->get()->toArray());
//        if ($limit < count($query->get()->toArray())) {
//        dd($query->count());
        if ($page && $limit) {
            $this->setTotal($query->count());
            $query = $query->limit($limit)->offset(($page - 1) * $limit);

        }


        //       }

        return $query->get()->toArray();
    }

    function setTotal($total)
    {
        $this->total = $total;
    }

    function getTotal()
    {
        return $this->total;
    }
}
