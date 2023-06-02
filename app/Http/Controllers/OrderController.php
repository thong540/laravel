<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Order;
use App\Repositories\OrderRepository;
use Illuminate\Http\Request;

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
        $request->validate(
            [
                'name' => 'required',
                'user_id' => 'required',
                'customer_id' => 'required',
                'status' => 'required'
            ]
        );
        $userInfor = $request->attributes->get('user')->data;
        if (!$this->checkPermissionOrder($userInfor->role, [Order::ADMIN, Order::MANAGER, Order::STAFF])) {
            $this->message = 'user no permission';
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

        $request->validate(
            [
                'name' => 'required',
                'user_id' => 'required',
                'customer_id' => 'required',
                'status' => 'required'
            ]
        );
        $userInfor = $request->attributes->get('user')->data;
        if (!$this->checkPermissionOrder($userInfor->role, [Order::ADMIN, Order::MANAGER, Order::STAFF])) {
            $this->message = 'user no permission';
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
        if (!$this->checkPermissionOrder($userInfor->role, [Order::ADMIN, Order::MANAGER, Order::STAFF])) {
            $this->message = 'user no permission';
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

    public function getDetailOrder(Request $request)
    {

//        $request->validate([
//            'order_id' => 'required'
//        ]);
        $orderId = $request->input('order_id');

        if (!isset($orderId)) {
            $this->message = 'OrderId is required';
            $this->status = 'failure';
            goto next;
        }
        $orderDetail = $this->orderRepo->getDetailOrderById($orderId);

        if (!$orderDetail) {
            $this->message = 'Not found by OrderId';
            $this->status = 'failure';
            goto next;
        }

        $totalPrice = 0;

        foreach ($orderDetail as $product) {
            $totalPrice += $product['price'] * $product['quantity'];
        }

        $data['information_order'] = $orderDetail;
        $data['total_price'] = $totalPrice;
        $this->message = 'get order';
        $this->status = 'success';
        // dd($data);
        next:
        if (!isset($data)) $data = [];
        return $this->responseData($data);
        // query lay thong tin don hang - order + customer + user
        // lay danh sach sp order_product => tinh ra tong gia tien don hang

    }

    public function updateStatusOrder(Request $request)
    {
        $status = $request->input('status');
        $id = $request->input('id');

        if (!$id || !$status) {
            $this->message = 'no update status in the order';
            $this->status = 'failure';

            goto next;
        }
        $dataUpdate = [Order::_STATUS => $status, Order::_UPDATED_AT => time()];
        $statusUpdate = $this->orderRepo->update($id, $dataUpdate);
        if (!$statusUpdate) {
            $this->message = 'no update status in the order';
            $this->status = 'failure';
            goto next;
        }
        $this->message = 'updated status';
        $this->status = 'success';
        next:
        if (!isset($dataUpdate)) {
            $dataUpdate = [];
        }
//        return $this->orderRepo->updateOneFieldById(Order::_STATUS, $id, $currentStatus);
        return $this->responseData($dataUpdate);


    }

    public function getStatusOrder(Request $request)
    {
        $id = $request->input('id');
        if (!isset($OrderId)) return false;
        $result = $this->orderRepo->getStatusOrder($id);
        if (!$result) {
            $this->message = 'not found order';
            $this->status = 'failure';

        } else {
            $this->message = 'get status';
            $this->status = 'success';
        }
        $data = !$result ? [] : ['status' => $result[0]['status']];
        return $this->responseData($data);


    }

    public function findOrderByOneField(Request $request)
    {
        return $this->orderRepo->findOrderByOneField($request->input('field'), $request->input('value'));
    }

    public function findOrderByInforCustomer(Request $request)
    {
        $id = $request->input('id');
        $email = $request->input('email');
        $fullName = $request->input('fullName');
        $phoneNumber = $request->input('phoneNumber');
        $address = $request->input('address');

        $data = [
            Customer::_ID => $id,
            Customer::_EMAIL => $email,
            Customer::_FULLNAME => $fullName,
            Customer::_PHONENUMBER => $phoneNumber,
            Customer::_ADDRESS => $address,
        ];

        $dataCustomers = $this->orderRepo->findOrderByInforCustomer($data);
        if (!$dataCustomers) {
            $this->message = 'No Orders';
            $this->status = 'failure';
            goto next;
        }
        $orderIds = [];
        foreach ($dataCustomers as $item) {
            array_push($orderIds, $item['orderId']);
        }
        $dataRespose = [];
       // dd($orderIds);
        foreach($orderIds as $key => $orderId ) {

           array_push($dataRespose, $this->orderRepo->getDetailOrderById($orderId)->toArray());
        };
        //dd($dataRespose);
        if (!$dataRespose) {
            $this->message = 'found not orders';
            $this->status = 'failure';

        } else {
            $this->message = 'get Orders';
            $this->status = 'success';


        }

        next:
        if(!isset($dataRespose)) {
            $dataRespose = [];
        }
        return $this->responseData($dataRespose);


    }
}

