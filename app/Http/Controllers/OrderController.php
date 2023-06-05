<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Order;
use App\Models\Product;
use App\Repositories\CustomerRepository;
use App\Repositories\OrderProductRepository;
use App\Repositories\OrderRepository;
use App\Repositories\ProductRepository;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    //
    private $orderRepo, $customerRepo, $orderProductRepo, $productRepo;

    function __construct(OrderRepository $orderRepo, CustomerRepository $customerRepo , OrderProductRepository $orderProductRepo, ProductRepository $productRepo)
    {
        $this->orderRepo = $orderRepo;
        $this->customerRepo = $customerRepo;
        $this->orderProductRepo = $orderProductRepo;
        $this->productRepo = $productRepo;
    }

    private function checkPermissionOrder($userRole, $roleExecute = [])
    {
        // dd($userRole, $roleExecute);
        return in_array($userRole, $roleExecute);
    }

    function getAllOrders(Request $request)
    {

//        LIMIT. OFFSET
        $limit = $request->input('limit');
        $page = $request->input('page');
        if (!isset($limit) && !isset($page)) {
            $orders = $this->orderRepo->getAll();
            $this->message = 'get all orders';
        } else {
            $orders = $this->orderRepo->getList($page, $limit);
            $this->message = 'get list orders';
        }
        $this->status = 'success';


        return $this->responseData($orders);
    }

    function createOrder(Request $request)
    {
        $request->validate(
            [
                'name' => 'required',
//                'user_id' => 'required',
                'phoneNumber' => 'required',

            ]
        );
        // TẠO ĐƠN THEO sdt => CID
        // sẢN PHẨM KHÁCH HÀNG MUA, SỐ LƯỢNG
        $name = $request->input('name');
        $phoneNumber = $request->input('phoneNumber');
        $address = $request->input('address');
        $fullName = $request->input('fullName');
        $email = $request->input('email');

        $products = $request->input('products');

        $userInfor = $request->attributes->get('user')->data;
        $userRole = $userInfor->role;
        if (!$this->checkPermissionOrder($userRole[0]->role_id, [Order::ADMIN, Order::MANAGER, Order::STAFF])) {
            $this->message = 'user no permission';
            goto next;
        }

        $user_id = $userInfor->userId;
        $customerFindByPhoneNumber = $this->customerRepo->findOneField(Customer::_PHONENUMBER, $phoneNumber);
        // $customer_id = null;
        if ($customerFindByPhoneNumber) {
            $customer_id = $customerFindByPhoneNumber[0]['id'];
        } else {
            $dataInsert = [
                Customer::_EMAIL => $email,
                Customer::_FULLNAME => $fullName,
                Customer::_ADDRESS => $address,
                Customer::_PHONENUMBER => $phoneNumber,
                Customer::_CREATED_AT => time(),
                Customer::_UPDATED_AT => time(),
            ];

            $newCustomerId = $this->customerRepo->insertGetId($dataInsert);
            if (!$newCustomerId) {
                $this->message = 'No create new customer';
                goto next;
            }
            $customer_id = $newCustomerId;
        }
        $dataInsert = [
            Order::_NAME => $name,
            Order::_USER_ID => $user_id,
            Order::_CUSTOMER_ID => $customer_id,
            Order::_STATUS => 1,
            Order::_CREATED_AT => time(),
            Order::_UPDATED_AT => time()
        ];
        $newOrderId = $this->orderRepo->insertGetId($dataInsert);
        if (!$newOrderId) {
            $this->message = 'No create new order';
            goto next;
        }
        $products = json_decode($products, true);
       // dd($products);
        foreach ($products as $key => $product)
        {
            $listProduct = $this->productRepo->find($product['product_id']);
            dd($listProduct);
         $dataInsert = [

         ];
        }
        $this->status = 'success';
        $this->message = 'created new order';
        next:
        return $this->responseData($orderId ?? []);

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
        $newOrderId = $this->orderRepo->update($id, $dataUpdate);
        if (!$newOrderId) {
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

            goto next;
        }
        $orderDetail = $this->orderRepo->getDetailOrderById($orderId);

        if (!$orderDetail) {
            $this->message = 'Not found by OrderId';
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
//        if (!isset($data)) $data = [];
        return $this->responseData($data ?? []);
        // query lay thong tin don hang - order + customer + user
        // lay danh sach sp order_product => tinh ra tong gia tien don hang

    }

    public function updateStatusOrder(Request $request)
    {
        $status = $request->input('status');
        $id = $request->input('id');

        if (!$id || !$status) {
            $this->message = 'no update status in the order';
            goto next;
        }
        $dataUpdate = [Order::_STATUS => $status, Order::_UPDATED_AT => time()];
        $statusUpdate = $this->orderRepo->update($id, $dataUpdate);
        if (!$statusUpdate) {
            $this->message = 'no update status in the order';

            goto next;
        }
        $this->message = 'updated status';
        $this->status = 'success';
        next:
//        if (!isset($dataUpdate)) {
//            $dataUpdate = [];
//        }
//        return $this->orderRepo->updateOneFieldById(Order::_STATUS, $id, $currentStatus);
        return $this->responseData($dataUpdate ?? []);


    }

    public function getStatusOrder(Request $request)
    {
        $id = $request->input('id');
        if (!isset($OrderId)) return false;
        $result = $this->orderRepo->getStatusOrder($id);
        if (!$result) {
            $this->message = 'not found order';

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

    //getOrderByParams
    public function findOrderByInforCustomer(Request $request)
    {
        $id = $request->input('id');
        $email = $request->input('email');
        $fullName = $request->input('fullName');
        $phoneNumber = $request->input('phoneNumber');
        $address = $request->input('address');
// sản phẩm => id
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

            goto next;
        }
//        $orderIds = [];
//        $orderIds = array_column($dataCustomers, 'orderId');
//        foreach ($dataCustomers as $item) {
//            array_push($orderIds, $item['orderId']);
//        }
//
//        if (!$orderIds) {
//            $this->message = 'No Orders';
//
//            goto next;
//        }
//        $dataRespose = [];
//        foreach($orderIds as $key => $orderId ) {
//           $order = $this->orderRepo->getDetailOrderById($orderId)->toArray();
//
//           if (!empty($order)) {
//               $dataRespose[] = $order;
//           }
//        };


        if (!$dataCustomers) {
            $this->message = 'found not orders';


        } else {
            $this->message = 'get Orders';
            $this->status = 'success';


        }

        next:
        if (!isset($dataRespose)) {
            $dataRespose = [];
        }
        return $this->responseData($dataRespose);


    }
}

