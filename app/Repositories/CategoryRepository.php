<?php

namespace App\Repositories;

//use App\Repositories\RepositoryInterface;
use App\Models\Category;

class CategoryRepository extends EloquentRepository
{

    public function getModel()
    {
        // TODO: Implement getModel() method.
        return Category::class;
    }

    public function getCategoryById($id)
    {
        return $this->_model
            ->select(Category::_DESCRIPTION, Category::_NAME)
            ->where(Category::_ID, $id)
            ->first();
    }

    public function updateById($id, $dataUpdate)
    {
        return $this->_model
            ->where(Category::_ID, $id)
            ->update($dataUpdate);
    }

    public function getListCategory()
    {

        $query = $this->_model->all();
        return $query;
    }
}
