<?php
namespace App\Repositories;

//use App\Repositories\RepositoryInterface;
use App\Models\Customer;
class CustomerRepository extends EloquentRepository
{

    public function getModel()
    {
        // TODO: Implement getModel() method.
        return Customer::class;
    }
    public function updateById($id, $dataUpdate)
    {
        return $this->_model
            ->where(Customer::_ID, $id)
            ->update($dataUpdate);
    }

}
