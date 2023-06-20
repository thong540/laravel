<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\User;
use App\Repositories\ProductRepository;
use Illuminate\Http\Request;

//use Illuminate\Http\Response;
class ProductController extends Controller
{
    private $productRepo;

    public function __construct(ProductRepository $productRepo)
    {
        $this->productRepo = $productRepo;
    }

    private function checkPermissionCustomer($user, $roleExecute = [])
    {

        return in_array($user, $roleExecute);
    }
    function getAllProducts()
    {
        $this->status = 'success';
        $this->message = 'get All Products';
        $products =$this->productRepo->getAllProduct()->toArray();
        return $this->responseData($products);
    }

    function createProduct(Request $request)
    {
        $request->validate(
            [
                'name' => ['required'],
                'category_id' =>['required'],
                'image' => 'required',
                'description' => ['required'],
                'price' => ['required']
            ]
        );
        $userInfor = $request->attributes->get('user')->data;

        if(!$this->checkPermissionCustomer($userInfor->role, [User::ADMIN, User::MANAGER, User::STAFF])) {
            $this->message = 'User no permission';
            goto next;
        }
        $name = $request->input('name');
        $category_id = $request->input('category_id');
        $image = $request->input('image');
        $description = $request->input('description');
        $price = $request->input('price');
        $dataInsert = [
            Product::_NAME => $name,
            Product::_CATEGORY_ID => $category_id,
            Product::_IMAGE => $image,
            Product::_DESCRIPTION => $description,
            Product::_PRICE => $price,
            Product::_CREATED_AT => time(),
            Product::_UPDATED_AT => time()
        ];
        $check = $this->productRepo->insert($dataInsert);
        if (!$check) {
            $this->message = 'No create new product';
            goto next;
        }
        $this->messgage = 'created new product';
        $this->status = 'success';
        next:
        return $this->responseData($dataInsert);
    }

    function updateProduct(Request $request)
    {

        $request->validate(
            [
                'name' => ['required'],
                'category_id' =>['required'],
                'image' => 'required',
                'description' => ['required'],
                'price' => ['required']
            ]
        );
        $userInfor = $request->attributes->get('user')->data;

        if(!$this->checkPermissionCustomer($userInfor->role, [User::ADMIN, User::MANAGER, User::STAFF])) {
            $this->message = 'User no permission';
            goto next;
        }
        $id = $request->input('id');
        $name = $request->input('name');
        $categoryId = $request->input('category_id');
        $image = $request->input('image');
        $description = $request->input('description');
        $price = $request->input('price');
        $dataUpdate = [
            Product::_NAME => $name,
            Product::_CATEGORY_ID => $categoryId,
            Product::_IMAGE => $image,
            Product::_DESCRIPTION => $description,
            Product::_PRICE => $price,
            Product::_UPDATED_AT => time()
        ];
        $check = $this->productRepo->update($id, $dataUpdate);
        if (!$check) {
            $this->message = 'No update product';
            goto next;
        }
        $this->messgage = 'updated product';
        $this->status = 'success';
        next:
        return $this->responseData($dataUpdate);
    }

    function deleteProduct(Request $request)
    {
        $userInfor = $request->attributes->get('user')->data;
        if(!$this->checkPermissionCustomer($userInfor->role->role_id, [User::ADMIN, User::MANAGER, User::STAFF])) {
            $this->message = 'User no permission';
            goto next;
        }
        $id = $request->input('id');
        $check = $this->productRepo->delete($id);
        if (!$check) {
            $this->message = 'No delete product';
            goto next;
        }
        $this->message = 'deleted product';
        $this->status = 'success';
        next:
        return $this->responseData();
    }
//    /**
//     * Display a listing of the resource.
//     *
//     * @return \Illuminate\Http\Response
//     */
//    public function index()
//    {
//        //
//    }
//
//    /**
//     * Show the form for creating a new resource.
//     *
//     * @return \Illuminate\Http\Response
//     */
//    public function create()
//    {
//        //
//    }
//
//    /**
//     * Store a newly created resource in storage.
//     *
//     * @param \Illuminate\Http\Request $request
//     * @return \Illuminate\Http\Response
//     */
//    public function store(Request $request)
//    {
//        //
//    }
//
//    /**
//     * Display the specified resource.
//     *
//     * @param int $id
//     * @return \Illuminate\Http\Response
//     */
//    public function show($id)
//    {
//        //
//    }
//
//    /**
//     * Show the form for editing the specified resource.
//     *
//     * @param int $id
//     * @return \Illuminate\Http\Response
//     */
//    public function edit($id)
//    {
//        //
//    }
//
//    /**
//     * Update the specified resource in storage.
//     *
//     * @param \Illuminate\Http\Request $request
//     * @param int $id
//     * @return \Illuminate\Http\Response
//     */
//    public function update(Request $request, $id)
//    {
//        //
//    }
//
//    /**
//     * Remove the specified resource from storage.
//     *
//     * @param int $id
//     * @return \Illuminate\Http\Response
//     */
//    public function destroy($id)
//    {
//        //
//    }
//
//    public function getAllProducts()
//    {
////       $allProducts =json(Product::all());
////       $this->status = 'success';
////       $this->message = 'got all products';
////       return $this->responseData($allProducts);
//        return response()->json(Product::all());
//    }
//
//    public function addNewProduct(Request $request)
//    {
////        dd($request->all());
//        //$this->validate($request, ['name' =>'required', 'category_id' =>'required', 'price' => 'required', 'status_product' => 'required', 'created_by' => 'required', 'updated_by' => 'required']);
//        $product = Product::create($request->all());
//
//        $this->status = 'success';
//        $this->message = 'added new product';
//        //$product->save();
//        return $this->responseData($product);
//
//
//    }
//
//    public function updateProduct(Request $request, $id)
//    {
//        //$product = Product::findOrFail($id)
//        //dd($request->all());
//        $product = Product::find($id);
//        $product->update($request->all());
//        $this->status = 'success';
//        $this->message = 'updated product';
//        //dd($product);
//        return $this->responseData($product);
//    }

//    public function deleteProduct($id)
//    {
//        Product::findOrFail($id)->delete();
//        $this->status = 'success';
//        $this->message = 'deleted user';
//
//        return $this->responseData();
//    }


}
