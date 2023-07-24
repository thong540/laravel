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

    public function getListCategory(Request $request)
    {
        $limit = $request->input('limit');
        $page = $request->input('page');;
        $parentId = $request->input('parentId');
        $categoryId = $request->input('categoryId');
        $categoryName = $request->input('categoryName');
        if (!isset($limit) && !isset($page) && !isset($categoryName) && !isset($categoryId) && !isset($parentId) ) {

            $this->status = 'success';
            $this->message = 'get All Customers';
            $data['data'] = $this->categoryRepo->getAll();
            $data['total'] = $this->categoryRepo->getAll()->count();
        } else {
            $this->status = 'success';
            $this->message = 'get List Categories';
            $data['data'] = $this->categoryRepo->getListCategory($page, $limit, $categoryId, $categoryName, $parentId);
            $data['total'] = $this->categoryRepo->getTotal();
        }



        return $this->responseData($data);

    }

    public function createCategory(Request $request)
    {
        $request->validate(
            [
                'name' => ['required'],
                'parent_id' => ['required'],
                'description' => ['required']
            ]
        );
        $userInfor = (array)$request->attributes->get('user')->data;
        if (!$this->checkPermissionCategory($userInfor['role']->role_id, [User::ADMIN, User::MANAGER])) {
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

        $request->validate(
            [
                'name' => ['required'],
                'parent_id' => ['required'],
                'description' => ['required']
            ]
        );
        $userInfor = (array)$request->attributes->get('user')->data;
        if (!$this->checkPermissionCategory($userInfor['role']->role_id, [User::ADMIN, User::MANAGER])) {
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
        if (!$this->checkPermissionCategory($userInfor['role']->role_id, [User::ADMIN, User::MANAGER])) {
            $this->message = 'user no permission';
            goto next;
        };
        $check = $this->categoryRepo->delete($id);
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
