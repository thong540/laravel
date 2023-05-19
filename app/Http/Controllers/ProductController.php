<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
//use Illuminate\Http\Response;
class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public function getAllProducts()
    {
//       $allProducts =json(Product::all());
//       $this->status = 'success';
//       $this->message = 'got all products';
//       return $this->responseData($allProducts);
        return response()->json(Product::all());
    }

    public function addNewProduct(Request $request)
    {
//        dd($request->all());
        //$this->validate($request, ['name' =>'required', 'category_id' =>'required', 'price' => 'required', 'status_product' => 'required', 'created_by' => 'required', 'updated_by' => 'required']);
        $product = Product::create($request->all());

        $this->status = 'success';
        $this->message = 'added new product';
        //$product->save();
        return $this->responseData($product);


    }

    public function updateProduct(Request $request, $id)
    {
        //$product = Product::findOrFail($id)
        //dd($request->all());
        $product = Product::find($id);
        $product->update($request->all());
        $this->status = 'success';
        $this->message = 'updated product';
        //dd($product);
        return $this->responseData($product);
    }

//    public function deleteProduct($id)
//    {
//        Product::findOrFail($id)->delete();
//        $this->status = 'success';
//        $this->message = 'deleted user';
//
//        return $this->responseData();
//    }

}
