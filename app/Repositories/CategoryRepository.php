<?php

namespace App\Repositories;

//use App\Repositories\RepositoryInterface;
use App\Models\Category;

class CategoryRepository extends EloquentRepository
{
    private $total;

    public function getModel()
    {
        // TODO: Implement getModel() method.
        return Category::class;
    }

    function setTotal($total)
    {
        $this->total = $total;
    }

    function getTotal()
    {
        return $this->total;
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

    public function getListCategory($page = null, $limit = null, $categoryId = null, $categoryName = null, $parentId = null)
    {
        $query = $this->_model;
        if ($categoryId) {

            $query = $query->where(Category::TABLE . '.' . Category::_ID, $categoryId);
        }
        if (isset($parentId) && $parentId >= 0) {
            $query = $query->where(Category::TABLE . '.' . Category::_PARENT_ID, $parentId);
        }
        if ($categoryName) {
            $query = $query->where(Category::TABLE . '.' . Category::_NAME, 'LIKE', '%' . $categoryName . '%');
        }

        $this->setTotal($query->count());
        if ($page && $limit) {

            $query = $query->limit($limit)->offset(($page - 1) * $limit);

        }
        return $query->get()->toArray();
    }

    public function getListCategoryDetail($page = null, $limit = null, $categoryId = null, $categoryName = null, $parentId = null)
    {

        $query = $this->_model->select(
            Category::TABLE . '.*' ,
            'a.name as parentName',
        )
            ->leftJoin(Category::TABLE . ' as a', 'a.id', Category::TABLE . '.' . Category::_PARENT_ID);
        if ($categoryId) {

            $query = $query->where(Category::TABLE . '.' . Category::_ID, $categoryId);
        }
        if (isset($parentId) && $parentId >= 0) {
            $query = $query->where(Category::TABLE . '.' . Category::_PARENT_ID, $parentId);
        }
        if ($categoryName) {
            $query = $query->where(Category::TABLE . '.' . Category::_NAME, 'LIKE', '%' . $categoryName . '%');
        }

        $this->setTotal($query->count());
        if ($page && $limit) {

            $query = $query->limit($limit)->offset(($page - 1) * $limit);

        }

        return $query->get()->toArray();
    }
}
