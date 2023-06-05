<?php

namespace App\Repositories;

//use App\Repositories\RepositoryInterface;
use App\Models\Customer;
use App\Models\Order;
use App\Models\OrderProduct;
use App\Models\Product;
use App\Models\User;

class OrderRepository extends EloquentRepository
{

    public function getModel()
    {
        // TODO: Implement getModel() method.
        return Order::class;
    }

//    public function getDetailOrder($telephone) {
//        $query = $this->_model
//            ->select(Order::TABLE . '.' . Order::_ID, User::TABLE . '.' . User::_FULLNAME)
//            ->join(User::TABLE, User::TABLE . '.' . User::_ID, Order::TABLE . '.' . Order::_USER_ID)
//            ->join(OrderProduct::TABLE, OrderProduct::TABLE . '.' . OrderProduct::_ORDER_ID, Order::TABLE . '.' . Order::_ID);
//
//        if ($telephone) {
//            $query = $query->where(OrderProduct::TABLE . '.' . OrderProduct::_PRODUCT_ID, $telephone);
//        }
//        return $query->get();
//    }
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
       // dd($query->where(Order::TABLE . '.' . Order::_ID, $orderId)->get()->toArray());
        if (!isset($orderId)) {
            return false;
        }
        return $query->where(Order::TABLE . '.' . Order::_ID, $orderId)->get();
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

    public function findOrderByOneField($field, $value)
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

        if (isset($field) && isset($value)) {
            $query = $query->where(Customer::TABLE . '.' . $field, 'like', '%' . $value . '%');
        }
        return $query->get();


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
        return $this->_model->limit($limit)->offset(($page - 1) * $limit)->get()->toArray();
    }
}
