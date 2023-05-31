<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Repositories\OrderRepository;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    //
    private $orderRepo;

    function __construct(OrderRepository $orderRepo)
    {
        $this->orderRepo = $orderRepo;
    }
    private function checkPermissionOrder($user, $roleExecute = [])
    {
        return in_array($user, $roleExecute);
    }
    function getAllOrders()
    {
        $this->status = 'success';
        $this->message = 'get All Orders';
        $order = $this->orderRepo->getAll();;;
        return $this->responseData($order);
    }

    function createOrder(Request $request)
    {
//        $request->validate(
//            [
//                'name' => 'required',
//                'user_id' => 'required',
//                'customer_id' => 'required',
//                'status' => 'required'
//            ]
//        );
        $userInfor = $request->attributes->get('user')->data;
        if(!$this->checkPermissionOrder($userInfor->role, [Order::ADMIN, Order::MANAGER, Order::STAFF])) {
            $this->message='user no permission';
            goto next;
        }
        $name = $request->input('name');
        $user_id = $request->input('user_id');
        $customer_id = $request->input('customer_id');
        $status = $request->input('status');

        $dataInsert = [
            Order::_NAME => $name,
            Order::_USER_ID => $user_id,
            Order::_CUSTOMER_ID => $customer_id,
            Order::_STATUS => $status,
            Order::_CREATED_AT => time(),
            Order::_UPDATED_AT => time()
        ];
        $check = $this->orderRepo->insert($dataInsert);
        if (!$check) {
            $this->message = 'No create new order';
            goto next;
        }
        $this->status = 'success';
        $this->message = 'created new order';
        next:
        return $this->responseData($dataInsert);

    }

    function updateOrder(Request $request)
    {

//        $request->validate(
//            [
//                'name' => ['required', 'min:3'],
//                'user_id' => 'required',
//                'customer_id' => 'required',
//                'status' => 'required'
//            ]
//        );
        $userInfor = $request->attributes->get('user')->data;
        if(!$this->checkPermissionOrder($userInfor->role, [Order::ADMIN, Order::MANAGER, Order::STAFF])) {
            $this->message='user no permission';
            goto next;
        }
        $id = $request->input('id');
        $name = $request->input('name');
        $user_id = $request->input('user_id');
        $customer_id = $request->input('customer_id');
        $status = $request->input('status');

        $dataUpdate = [
            Order::_NAME => $name,
            Order::_USER_ID => $user_id,
            Order::_CUSTOMER_ID => $customer_id,
            Order::_STATUS => $status,
            Order::_UPDATED_AT => time()
        ];
        $check = $this->orderRepo->update($id, $dataUpdate);
        if (!$check) {
            $this->message = 'No update order';
            goto next;
        }
        $this->status = 'success';
        $this->message = 'updated order';
        next:
        return $this->responseData($dataUpdate);

    }

    function deleteOrder(Request $request)
    {
        $userInfor = $request->attributes->get('user')->data;
        if(!$this->checkPermissionOrder($userInfor->role, [Order::ADMIN, Order::MANAGER, Order::STAFF])) {
            $this->message='user no permission';
            goto next;
        }
        $id = $request->input('id');
        $check = $this->orderRepo->delete($id);
        if (!$check) {
            $this->message = 'No delete Order';
            goto next;
        }
        $this->status = 'success';
        $this->message = 'deleted order';
        next:
        return $this->responseData();
    }
    public function getDetailOrder()
    {

    }

}

