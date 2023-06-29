<?php

namespace App\Repositories;

//use App\Repositories\RepositoryInterface;
use App\Models\Customer;

class CustomerRepository extends EloquentRepository
{

    private $total;

    public function getModel()
    {
        // TODO: Implement getModel() method.
        return Customer::class;
    }

    function setTotal($total)
    {
        $this->total = $total;
    }

    function getTotal()
    {
        return $this->total;
    }

    public function updateById($id, $dataUpdate)
    {
        return $this->_model
            ->where(Customer::_ID, $id)
            ->update($dataUpdate);
    }

    public function getListCustomer($page, $limit, $email = null, $fullName = null, $address = null, $phoneNumber = null)
    {
        $query = $this->_model->select(Customer::TABLE . '.*');
        if ($email) {
            $query = $query->where(Customer::TABLE . '.' . Customer::_EMAIL, 'LIKE', '%' . $email . '%');
        }
        if ($fullName) {
            $query = $query->where(Customer::TABLE . '.' . Customer::_FULLNAME, 'LIKE', '%' . $fullName . '%');
        }
        if ($address) {
            $query = $query->where(Customer::TABLE . '.' . Customer::_ADDRESS, 'LIKE', '%' . $address . '%');
        }
        if ($phoneNumber) {
            $query = $query->where(Customer::TABLE . '.' . Customer::_PHONENUMBER, 'LIKE', '%' . $phoneNumber . '%');
        }
        $this->setTotal($query->count());
        $query = $query->limit($limit)->offset(($page - 1) * $limit);

        return $query->get()->toArray();
    }


}
