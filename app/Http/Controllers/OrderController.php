<?php

namespace App\Http\Controllers;

use App\Http\PublicHelper;
use App\Http\Services\HistoryActivityService;
use App\Models\Customer;
use App\Models\Logger;
use App\Models\Order;
use App\Models\OrderProduct;
use App\Models\Product;
use App\Repositories\CustomerRepository;
use App\Repositories\OrderProductRepository;
use App\Repositories\OrderRepository;
use App\Repositories\ProductRepository;
use Illuminate\Http\Request;
use SebastianBergmann\LinesOfCode\LinesOfCode;

class OrderController extends Controller
{
    //
    private $orderRepo, $customerRepo, $orderProductRepo, $productRepo, $historyActivityService;

    function __construct(OrderRepository   $orderRepo, CustomerRepository $customerRepo, OrderProductRepository $orderProductRepo,
                         ProductRepository $productRepo, HistoryActivityService $historyActivityService)
    {
        $this->orderRepo = $orderRepo;
        $this->customerRepo = $customerRepo;
        $this->orderProductRepo = $orderProductRepo;
        $this->productRepo = $productRepo;
        $this->historyActivityService = $historyActivityService;
    }

    private function checkPermissionOrder($userRole, $roleExecute = [])
    {
        // dd($userRole, $roleExecute);
        return in_array($userRole, $roleExecute);
    }

    function getAllOrders(Request $request)
    {

//        LIMIT. OFFSET
        $limit = $request->input('limit', 5);
        $page = $request->input('page', 1);

        $orders = $this->orderRepo->getList($page, $limit);
        $this->message = 'get list orders';

        $this->status = 'success';
        $data['data'] = $orders;
        $data['total'] = $this->orderRepo->getTotal();

        return $this->responseData($data);
    }

    function createOrder(Request $request)
    {
//        $request->validate(
//            [
//                'name' => 'required',
////                'user_id' => 'required',
//                'phoneNumber' => 'required',
//
//            ]
//        );
        // TẠO ĐƠN THEO sdt => CID
        // sẢN PHẨM KHÁCH HÀNG MUA, SỐ LƯỢNG
        $name = $request->input('name');
        $phoneNumber = $request->input('phoneNumber');
        $address = $request->input('address');
        $fullName = $request->input('fullName');
        $email = $request->input('email');
        $listProduct = $request->input('products');
        $status = $request->input('status');
        $userInfor = $request->attributes->get('user')->data;
        $userRole = $userInfor->role;
        if (!$this->checkPermissionOrder($userRole->role_id, [Order::ADMIN, Order::MANAGER, Order::STAFF])) {
            $this->message = 'user no permission';
            goto next;
        }

        $userId = $userInfor->userId;
        $customerFindByPhoneNumber = $this->customerRepo->findOneField(Customer::_PHONENUMBER, $phoneNumber)->toArray();
        if (!$customerFindByPhoneNumber == false) {
            $customerId = $customerFindByPhoneNumber[0]['id'];
            $dataUpdate = [
                Customer::_EMAIL => $email,
                Customer::_FULLNAME => $fullName,
                Customer::_ADDRESS => $address,
                Customer::_PHONENUMBER => $phoneNumber,
                Customer::_UPDATED_AT => time(),
            ];
            $checkUpdateData = $this->customerRepo->update($customerId, $dataUpdate);
            if (!$checkUpdateData) {
                $this->message = 'No create';
                goto next;
            }


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
            $customerId = $newCustomerId;
        }
        $dataInsert = [
            Order::_NAME => $name,
            Order::_USER_ID => $userId,
            Order::_CUSTOMER_ID => $customerId,
            Order::_STATUS => $status,
            Order::_CREATED_AT => time(),
            Order::_UPDATED_AT => time()
        ];
        $newOrderId = $this->orderRepo->insertGetId($dataInsert);
        if (!$newOrderId) {
            $this->message = 'No create new order';
            goto next;
        }
//        dd($listProduct);
        $products = json_decode($listProduct, true);
//        $products  = $listProduct;
        foreach ($products as $key => $product) {
            $informationProduct = $this->productRepo->find($product['product_id'])->toArray();
            $priceProduct = $informationProduct['price'];
            $dataInsert = [
                OrderProduct::_ORDER_ID => $newOrderId,
                OrderProduct::_PRODUCT_ID => $product['product_id'],
                OrderProduct::_PRICE => $priceProduct,
                OrderProduct::_QUANTITY => $product['quantity'],
                OrderProduct::_CREATED_AT => time(),
                OrderProduct::_UPDATED_AT => time(),
            ];
            $checkInsertData = $this->orderProductRepo->insert($dataInsert);
            if (!$checkInsertData) {
                $this->message = 'No insert data at order_products table';
                goto next;
            }
        }
        $this->status = 'success';
        $this->message = 'created new order';
        $this->historyActivityService->logger([
            Logger::_USER_ID => $userId,
            Logger::_ACTION => 'create new order, order_id:  ' . $newOrderId,
            Logger::_TIME => time(),
        ]);
        next:
        return $this->responseData($newOrderId ?? []);

    }

    public function updateProductInOrder(Request $request)
    {
        $request->validate([
            'order_id' => 'required',


        ]);
        $orderId = $request->input('order_id');
        $products = $request->input('products');
        $userInfor = $request->attributes->get('user')->data;
        $userRole = $userInfor->role;
        if (!$this->checkPermissionOrder($userRole->role_id, [Order::ADMIN, Order::MANAGER, Order::STAFF])) {
            $this->message = 'user no permission';
            goto next;
        }

        $userId = $userInfor->userId;

        $currentListProducts = json_decode($products, true);
        $listProducts = $this->orderProductRepo->findOneField(OrderProduct::_ORDER_ID, $orderId)->toArray();

        foreach ($currentListProducts as $index => $currentProduct) {
            foreach ($listProducts as $key => $oldProduct) {
                if ($currentListProducts[$index]['product_id'] == $oldProduct['product_id']) {
                    $currentListProducts[$index] = array_merge($currentListProducts[$index], ['id' => $oldProduct['id']]);
                    unset($listProducts[$key]);
                }
            }

        }

        foreach ($currentListProducts as $currentProduct) {
            if (isset($currentProduct['id'])) {

                $checkUpdateData = $this->orderProductRepo->update($currentProduct['id'], [
                    OrderProduct::_PRODUCT_ID => $currentProduct['product_id'],
                    OrderProduct::_QUANTITY => $currentProduct['quantity'],
                    OrderProduct::_UPDATED_AT => time()
                ]);
                if (!$checkUpdateData) {
                    $this->message = 'No, update data';
                    goto next;
                }
            } else {
                // không nên để câu truy vấn trong vòng lặp
                $informationProduct = $this->productRepo->find($currentProduct['product_id']);
                $price = $informationProduct['price'];

                $checkInsertData = $this->orderProductRepo->insert([
                    OrderProduct::_ORDER_ID => $orderId,
                    OrderProduct::_PRODUCT_ID => $currentProduct['product_id'],
                    OrderProduct::_QUANTITY => $currentProduct['quantity'],
                    OrderProduct::_PRICE => $price,
                    OrderProduct::_CREATED_AT => time(),
                    OrderProduct::_UPDATED_AT => time()
                ]);
                if (!$checkInsertData) {
                    $this->message = 'No, insert data';
                    goto next;
                }

            }
        }
        foreach ($listProducts as $product) {

            $checkDeleteData = $this->orderProductRepo->delete($product['id']);
            if (!$checkDeleteData) {
                $this->message = 'No, delete data';
                goto next;
            }
        }


        $this->message = 'updated list product';
        $this->status = 'success';
        $this->historyActivityService->logger([
            Logger::_USER_ID => $userId,
            Logger::_ACTION => 'update list products in order, order_id: ' . $orderId,
            Logger::_TIME => time()
        ]);
        next:
        return $this->responseData($data ?? []);
    }


    public function updateOrder(Request $request)
    {

        $request->validate(
            [
                'order_id' => 'required',
                'phoneNumber' => 'required',
                'fullName' => 'required',
                'address' => 'required'
            ]
        );
        $userInfor = $request->attributes->get('user')->data;
        $userRole = $userInfor->role;
        if (!$this->checkPermissionOrder($userRole->role_id, [Order::ADMIN, Order::MANAGER, Order::STAFF])) {
            $this->message = 'user no permission';
            goto next;
        }
        $userId = $userInfor->userId;
        $orderId = $request->input('order_id');
        $phoneNumber = $request->input('phoneNumber');
        $fullName = $request->input('fullName');
        $address = $request->input('address');
        $informationOrder = $this->orderRepo->find($orderId)->toArray();
        $customerId = $informationOrder['customer_id'];
        $customerIdUpdate = null;
        $informationCustomer = $this->customerRepo->findOneField(Customer::_PHONENUMBER, $phoneNumber)->toArray();
        if (!$informationCustomer) {
            $checkUpdateCustomer = $this->customerRepo->update($customerId, [Customer::_PHONENUMBER => $phoneNumber]);
            if (!$checkUpdateCustomer) {
                $this->message = 'No update phoneNumber';
                goto next;
            }

        } else {
            $customerIdUpdate = $informationCustomer[0]['id'];
            //dd($customerIdUpdate);
            $checkUpdate = $this->customerRepo->update($customerIdUpdate, [
                Customer::_FULLNAME => $fullName,
                Customer::_ADDRESS => $address
            ]);
            if (!$checkUpdate) {
                $this->message = 'No update information customer';
                goto next;
            }

//            dd($customerIdUpdate, $customerId);
        }

        $dataUpdate = [
            Order::_CUSTOMER_ID => $customerIdUpdate,
            Order::_UPDATED_AT => time()
        ];
        $newOrderId = $this->orderRepo->update($orderId, $dataUpdate);
        if (!$newOrderId) {
            $this->message = 'No update order';
            goto next;
        }

        $this->status = 'success';
        $this->message = 'updated order';
        $this->historyActivityService->logger([
            Logger::_USER_ID => $userId,
            Logger::_ACTION => 'OrderController@updateOrder',
            Logger::_TIME => time()

        ]);
        next:
        return $this->responseData($dataUpdate ?? []);

    }

    function deleteOrder(Request $request)
    {
        $request->validate([
            'id' => 'required'
        ]);
        $id = $request->input('id');
        $userInfor = $request->attributes->get('user')->data;
        $userRole = $userInfor->role;
        if (!$this->checkPermissionOrder($userRole->role_id, [Order::ADMIN, Order::MANAGER, Order::STAFF])) {
            $this->message = 'user no permission';
            goto next;
        }

        $userId = $userInfor->userId;

        $check = $this->orderRepo->delete($id);
        if (!$check) {
            $this->message = 'No delete Order';
            goto next;
        }
        $this->status = 'success';
        $this->message = 'deleted order';
        $this->historyActivityService->logger([
            Logger::_USER_ID => $userId,
            Logger::_ACTION => 'delete order, order_id: ' . $id,
            Logger::_TIME => time()

        ]);
        next:
        return $this->responseData();
    }

    public function getDetailOrder(Request $request)
    {
        $request->validate([
            'order_id' => 'required'
        ]);

        $userInfor = $request->attributes->get('user')->data;
        $userRole = $userInfor->role;
        if (!$this->checkPermissionOrder($userRole->role_id, [Order::ADMIN, Order::MANAGER, Order::STAFF])) {
            $this->message = 'user no permission';
            goto next;
        }

        $orderId = $request->input('order_id');
        $informationCustomer = $this->orderRepo->getInformationCustomerById($orderId)->toArray();
        $listProducts = $this->orderRepo->getDetailProductInOrdeById($orderId)->toArray();
        $informationOrder = $this->orderRepo->getInformationOrderById($orderId)->toArray();
        $informationStaff = $userInfor;
        $statusOrder = $informationCustomer['status'];
        unset($informationCustomer['status']);
        if (!$informationCustomer || !$listProducts) {
            $this->message = 'Not found by OrderId';
            goto next;
        }

        $totalPrice = 0;

        foreach ($listProducts as $product) {
            $totalPrice += $product['price'] * $product['quantity'];
        }
        $data['information_order'] = $informationOrder;
        $data['information_customer'] = $informationCustomer;
        $data['list_products'] = $listProducts;
        $data['total_price'] = $totalPrice;
        $data['status'] = $statusOrder;
        $data['information_staff'] = $informationStaff;
        $this->message = 'get order';
        $this->status = 'success';

        next:

        return $this->responseData($data ?? []);
        // query lay thong tin don hang - order + customer + user
        // lay danh sach sp order_product => tinh ra tong gia tien don hang

    }

    public function updateStatusOrder(Request $request)
    {
        $request->validate([
            'id' => 'required',
            'status' => 'required'
        ]);
        $status = $request->input('status');
        $id = $request->input('id');
        $userInfor = $request->attributes->get('user')->data;
        $userRole = $userInfor->role;
        if (!$this->checkPermissionOrder($userRole->role_id, [Order::ADMIN, Order::MANAGER, Order::STAFF])) {
            $this->message = 'user no permission';
            goto next;
        }
        $useId = $userInfor->userId;
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
        $this->historyActivityService->logger([
            Logger::_USER_ID => $useId,
            Logger::_ACTION => 'update status in order, order_id: ' . $id,
            Logger::_TIME => time()
        ]);
        next:
        return $this->responseData($dataUpdate ?? []);


    }

    public function getStatusOrder(Request $request)
    {
        $request->validate([
            'id' => 'required'
        ]);
        $id = $request->input('id');
        $result = $this->orderRepo->getStatusOrder($id);
        if (!$result) {
            $this->message = 'not found order';

        } else {
            $this->message = 'get status';
            $this->status = 'success';
        }
        //  $data = !$result ? [] : ['status' => $result[0]['status']];
        return $this->responseData($result[0] ?? []);
    }

    public function findOrderByManyField(Request $request)
    {

//        $request->validate([
//            'order_id' => 'required',
//            'status_order' => 'required',
//            'email_customer' => 'required',
//            'phoneNumber_customer' => 'required',
//            'product_id' => 'required'
//        ]);

        $id = $request->input('order_id');
        $status = $request->input('status_order');
        $email = $request->input('email_customer');
        $phoneNumber = $request->input('phoneNumber_customer');
        $productId = $request->input('product_id');
        $data = [
            Order::_ID => $id,
            Order::_STATUS => $status,
            Customer::_EMAIL => $email,
            Customer::_PHONENUMBER => $phoneNumber,
            'product_id' => $productId
        ];
// them phan trang, lay them danh sach san pham trong don hang

        $ListOrder = $this->orderRepo->findOrderByManyField($data, $page = 0, $Limit = 10)->keyBy('orderId');
        if (!$ListOrder) {
            $this->message = 'Not found order';
            goto next;
        }
        $this->message = 'List orders';
        $this->status = 'success';
        next:
        return $this->responseData($ListOrder ?? []);
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

