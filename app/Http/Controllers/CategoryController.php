<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Repositories\CategoryRepository;
use Illuminate\Http\Request;

class CategoryController extends Controller
{

    private $categoryRepo;

    public function __construct(
        CategoryRepository $categoryRepo
    )
    {
        $this->categoryRepo = $categoryRepo;
    }

    public function getAllCategories()
    {
        return $this->categoryRepo->getAll();

    }

    public function createCategory(Request $request)
    {
        //$this->validate($request, ['name' => 'required', 'parent_id' => 'required', 'created_by' => 'required', 'updated_by' => 'required']);
        $name = $request->input('name');
        $parentId = $request->input('parent_id', 0);
        $description = $request->input('description', 0);

        $dataInsert = [
            Category::_NAME => $name,
            Category::_PARENT_ID => $parentId,
            Category::_DESCRIPTION => $description,
            Category::_CREATED_AT => time(),
            Category::_UPDATED_AT => time(),
        ];

        $check = $this->categoryRepo->insert($dataInsert);
        if (!$check) {
            $this->message = 'tạo mới thất bại';
            goto next;
        }
        $this->status = 'success';
        $this->message = 'message';
        next:
        return $this->responseData();
    }

    public function updateCategory(Request $request)
    {
        //dd($request->input());
        $id = $request->input('id');
        $name = $request->input('name');
        $parentId = $request->input('parent_id', 0);
        $description = $request->input('description', 0);

        $dataUpdate = [
            Category::_NAME => $name,
            Category::_PARENT_ID => $parentId,
            Category::_DESCRIPTION => $description,
            Category::_UPDATED_AT => time(),
        ];

        $check = $this->categoryRepo->updateById($id, $dataUpdate);
        if (!$check) {
            $this->message = 'No update category';
            goto next;
        }
        $this->status = 'success';
        $this->message = 'message';
        next:
        return $this->responseData();
    }

    public function deleteCategory(Request $request)
    {

        $id = $request->input('id');

        $check = $this->customerRepo->delete($id);
        if (!$check) {
            $this->message = 'No delete category';
            goto next;
        }
        $this->status = 'success';
        $this->message = 'message';
        next:
        return $this->responseData();
    }


}
