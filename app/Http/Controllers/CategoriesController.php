<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;

class CategoriesController extends Controller
{

    public function getAllCategory()
    {
        return response()->json(Category::all());

    }

    public function createCategory(Request $request)
    {
        //$this->validate($request, ['name' => 'required', 'parent_id' => 'required', 'created_by' => 'required', 'updated_by' => 'required']);
        $category = Category::create($request->all());
        $this->status = 'success';
        $this->message = 'message';
        return $this->responseData($category);
    }

    public function updateCategory(Request $request, $id)
    {
        $category = Category::findOrFail($id);
        dd($request);
        //$category = Category::find($id);
        $category->update($request->all());
        $this->status = 'success';
        $this->message = 'message';
        return $this->responseData($category);
    }

    public function deleteCategory($id)
    {
        Category::findOrFail($id)->delete();
        $this->status = 'success';
        $this->message = 'message';
        return $this->responseData();
    }


}
