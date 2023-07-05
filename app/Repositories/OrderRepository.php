<?php

namespace App\Repositories;

//use App\Repositories\RepositoryInterface;
use App\Models\Category;
use App\Models\Customer;
use App\Models\Order;
use App\Models\OrderProduct;
use App\Models\Product;
use App\Models\User;

class OrderRepository extends EloquentRepository
{

    private $total;
    public function getModel()
    {
        // TODO: Implement getModel() method.
        return Order::class;
    }
    function setTotal($total)
    {
        $this->total = $total;
    }

    function getTotal()
    {
        return $this->total;
    }

    public function getDetailOrderById($orderId)
    {

        $query = $this->_model
            ->select(
                Customer::TABLE . '.' . Customer::_EMAIL,
                Customer::TABLE . '.' . Customer::_FULLNAME,
                Customer::TABLE . '.' . Customer::_ADDRESS,
                Customer::TABLE . '.' . Customer::_PHONENUMBER,
                Order::TABLE . '.' . Order::_NAME,
                Order::TABLE . '.' . Order::_STATUS,
                Product::TABLE . '.' . Product::_NAME,
                Product::TABLE . '.' . Product::_IMAGE,
                Product::TABLE . '.' . Product::_DESCRIPTION,
                Product::TABLE . '.' . Product::_PRICE,
                OrderProduct::TABLE . '.' . OrderProduct::_QUANTITY,
                User::TABLE . '.' . User::_FULLNAME . ' as nameStaff',
                User::TABLE . '.' . User::_ADDRESS,
                User::TABLE . '.' . User::_PHONENUMBER,
                Order::TABLE . '.' . Order::_CREATED_AT,
                Order::TABLE . '.' . Order::_UPDATED_AT)
            ->join(Customer::TABLE, Customer::TABLE . '.' . Customer::_ID, Order::TABLE . '.' . Order::_CUSTOMER_ID)
            ->join(User::TABLE, User::TABLE . '.' . User::_ID, Order::TABLE . '.' . Order::_USER_ID)
            ->join(OrderProduct::TABLE, OrderProduct::TABLE . '.' . OrderProduct::_ORDER_ID, Order::TABLE . '.' . Order::_ID)
            ->join(Product::TABLE, Product::TABLE . '.' . Product::_ID, OrderProduct::TABLE . '.' . OrderProduct::_PRODUCT_ID);
        if (!isset($orderId)) {
            return false;
        }
        return $query->where(Order::TABLE . '.' . Order::_ID, $orderId)->get();
    }

    public function getInformationCustomerById($orderId)
    {
        $query = $this->_model->select(
            Customer::TABLE . '.' . Customer::_ID,
            Customer::TABLE . '.' . Customer::_EMAIL,
            Customer::TABLE . '.' . Customer::_FULLNAME,
            Customer::TABLE . '.' . Customer::_ADDRESS,
            Customer::TABLE . '.' . Customer::_PHONENUMBER,
            Order::TABLE . '.' . Order::_STATUS)
            ->join(Customer::TABLE, Customer::TABLE . '.' . Customer::_ID, Order::TABLE . '.' . Order::_CUSTOMER_ID);
        if (!isset($orderId)) {
            return false;
        }
        return $query->where(Order::TABLE . '.' . Order::_ID, $orderId)->get();
    }

    public function getDetailProductInOrdeById($orderId)
    {
        $query = $this->_model->select(
            Product::TABLE . '.' . Product::_ID,
            Product::TABLE . '.' . Product::_NAME,
            Product::TABLE . '.' . Product::_IMAGE,
            Product::TABLE . '.' . Product::_DESCRIPTION,
            Product::TABLE . '.' . Product::_PRICE,
            Category::TABLE . '.'. Category::_NAME . ' as category_name',
            OrderProduct::TABLE . '.' . OrderProduct::_QUANTITY)
            ->join(OrderProduct::TABLE, OrderProduct::TABLE . '.' . OrderProduct::_ORDER_ID, Order::TABLE . '.' . Order::_ID)
            ->join(Product::TABLE, Product::TABLE . '.' . Product::_ID, OrderProduct::TABLE . '.' . OrderProduct::_PRODUCT_ID)
            ->join(Category::TABLE, Category::TABLE . '.' . Category::_ID, Product::TABLE . '.' . Product::_CATEGORY_ID);
        if (!isset($orderId)) {
            return false;
        }
        return $query->where(Order::TABLE . '.' . Order::_ID, $orderId)->get();
    }
    public function getInformationOrderById($id) {
        return $this->_model->find($id)->first();
    }
    public function updateOneFieldById($field, $id, $currentStatus)
    {

        $result = $this->_model->where('id', $id);
        if ($currentStatus == Order::CANCEL) {
            $result->delete($id);
            return true;
        }
        if ($result) {
            $result->update([$field => $currentStatus])->get();

            return true;
        }
        return false;


    }

    public function getStatusOrder($OrderId)
    {
        $result = $this->_model->where('id', $OrderId)->get()->toArray();

        return $result;
    }

    public function findOrderByManyField($data, $page, $limit)
    {

        $query = $this->_model
            ->select(
                Order::TABLE . '.' .Order::_ID . ' as orderId',
                Customer::TABLE . '.' . Customer::_EMAIL,
                Customer::TABLE . '.' . Customer::_FULLNAME,
                Customer::TABLE . '.' . Customer::_ADDRESS,
                Customer::TABLE . '.' . Customer::_PHONENUMBER,
                Order::TABLE . '.' . Order::_NAME,
                Order::TABLE . '.' . Order::_STATUS,
                Order::TABLE . '.' . Order::_CREATED_AT,
                Order::TABLE . '.' . Order::_UPDATED_AT)
            ->join(Customer::TABLE, Customer::TABLE . '.' . Customer::_ID, Order::TABLE . '.' . Order::_CUSTOMER_ID);
        if ($data[Order::_ID]) {
            $query = $query->where(Order::TABLE . '.' . Order::_ID, $data[Order::_ID]);
        }
        if ($data[Order::_STATUS]) {
            $query = $query->where(Order::TABLE . '.' . Order::_STATUS, $data[Order::_STATUS]);
        }
        if ($data[Customer::_EMAIL]) {
            $query = $query->where(Customer::TABLE . '.' . Customer::_EMAIL, $data[Customer::_EMAIL]);
        }
        if ($data[Customer::_PHONENUMBER]) {
            $query = $query->where(Customer::TABLE . '.' . Customer::_PHONENUMBER, $data[Customer::_PHONENUMBER]);
        };
        if ($data['product_id']) {
            $query
                ->join(OrderProduct::TABLE, OrderProduct::TABLE . '.' . OrderProduct::_ORDER_ID, Order::TABLE . '.' . Order::_ID)
                ->join(Product::TABLE, Product::TABLE . '.' . Product::_ID, OrderProduct::TABLE . '.' . OrderProduct::_PRODUCT_ID)
                ->where(Product::TABLE . '.' . Product::_ID, $data['product_id']);
        }

        return $query->limit($limit)->offset(($page-1) * $limit)->get();


    }

    public function findOrderByInforCustomer($inforCustomer)
    {

        $query = $this->_model
            ->select(
                Customer::TABLE . '.' . Customer::_ID . ' as customerId',
                Customer::TABLE . '.' . Customer::_EMAIL,
                Customer::TABLE . '.' . Customer::_FULLNAME,
                Customer::TABLE . '.' . Customer::_ADDRESS,
                Customer::TABLE . '.' . Customer::_PHONENUMBER,
                Order::TABLE . '.' . Order::_ID . ' as orderId',
            )
            ->join(Customer::TABLE, Customer::TABLE . '.' . Customer::_ID, Order::TABLE . '.' . Order::_CUSTOMER_ID);
        if ($inforCustomer['id']) {
            $query = $query->where(Customer::TABLE . '.' . Customer::_ID, $inforCustomer['id']);
        }

        if (isset($inforCustomer['email'])) {
            $query = $query->where(Customer::TABLE . '.' . Customer::_EMAIL, $inforCustomer['email']);
        }

        if (isset($inforCustomer['fullName'])) {
            $query = $query->where(Customer::TABLE . '.' . Customer::_FULLNAME, $inforCustomer['fullName']);
        }

        if (isset($inforCustomer['address'])) {
            $query = $query->where(Customer::TABLE . '.' . Customer::_ADDRESS, $inforCustomer['address']);
        }
        if (isset($inforCustomer['phoneNumber'])) {
            $query = $query->where(Customer::TABLE . '.' . Customer::_PHONENUMBER, $inforCustomer['phoneNumber']);
        }

//        dd($query->get()->toArray());
        return $query->get()->toArray();


    }

    public function getList($page, $limit)
    {
        $query = $this->_model->select(Order::TABLE . '.*', User::TABLE . '.' . User::_FULLNAME . ' as userName', Customer::TABLE . '.' .Customer::_FULLNAME . ' as customerName')
            ->join(User::TABLE, User::TABLE . '.'.User::_ID, Order::TABLE . '.'.Order::_USER_ID)
            ->join(Customer::TABLE, Customer::TABLE . '.' . Customer::_ID, Order::TABLE . '.' . Order::_CUSTOMER_ID);
        $this->setTotal($this->_model->all()->count());
        return $query->limit($limit)->offset(($page - 1) * $limit)->get()->toArray();
    }

}
