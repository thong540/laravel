<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\User;
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

    private function checkPermissionCategory($user, $roleExecute)
    {
        //dd($user, $roleExecute);
        return in_array($user, $roleExecute);
    }

    public function getAllCategories()
    {
        $this->status = 'success';
        $this->message = 'get All Categories';
        $categories = $this->categoryRepo->getAll();
        return $this->responseData($categories);

    }

    public function createCategory(Request $request)
    {
//      $request->validate(
//          [
//              'name' => ['required'],
//              'parent_id' => ['required'],
//              'description' => ['required']
//          ]
//      );
        $userInfor = (array)$request->attributes->get('user')->data;
        if (!$this->checkPermissionCategory($userInfor['role'], [User::ADMIN, User::MANAGER])) {
            $this->message = 'user no permission';
            goto next;
        };

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
            $this->message = 'No create new user';
            goto next;
        }
        $this->status = 'success';
        $this->message = 'created new user';
        next:
        return $this->responseData();
    }

    public function updateCategory(Request $request)
    {

//        $request->validate(
//            [
//                'name' => ['required', 'min:3'],
//                'parent_id' => ['required'],
//                'description' => ['required']
//            ]
//        );
        $userInfor = (array)$request->attributes->get('user')->data;
        if (!$this->checkPermissionCategory($userInfor['role'], [User::ADMIN, User::MANAGER])) {
            $this->message = 'user no permission';
            goto next;
        };
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
        $this->message = 'updated category';
        next:
        return $this->responseData();
    }

    public function deleteCategory(Request $request)
    {

        $id = $request->input('id');
        $userInfor = (array)$request->attributes->get('user')->data;
        if (!$this->checkPermissionCategory($userInfor['role'], [User::ADMIN, User::MANAGER])) {
            $this->message = 'user no permission';
            goto next;
        };

        $check = $this->customerRepo->delete($id);
        if (!$check) {
            $this->message = 'No delete category';
            goto next;
        }
        $this->status = 'success';
        $this->message = 'deleted category';
        next:
        return $this->responseData();
    }


}
