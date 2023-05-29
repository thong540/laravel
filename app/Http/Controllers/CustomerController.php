<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Repositories\CustomerRepository;
use Illuminate\Http\Request;
use Illuminate\Validation\Rules\Password;

class CustomerController extends Controller
{
    private $customerRepo;

    public function __construct(CustomerRepository $customerRepo)
    {
        $this->customerRepo = $customerRepo;
    }

    function getAllCustomers()
    {

        $this->status = 'success';
        $this->message = 'get All Customers';
        $customers =$this->customerRepo->getAll();;
        return $this->responseData($customers);

    }

    function createCustomer(Request $request)
    {
        $request->validate([
            'email' => ['required|email|ends_with:@gmail.com' ],
            'fullName' => ['required','min:3','max:20'],
            'address' => ['required','min:3','max:20'],
            'phoneNumber' => ['required|min:10|regex:/(01)[0-9]{9}/']


        ]);
        $email = $request->input('email');
//        $description = $request->input('description', 0);
        $fullName = $request->input('fullName');
        $address = $request->input('address');
        $phoneNumber = $request->input('phoneNumber');
        $dataInsert = [
            Customer::_EMAIL => $email,
            Customer::_FULLNAME => $fullName,
            Customer::_ADDRESS => $address,
            Customer::_PHONENUMBER => $phoneNumber,
            Customer::_CREATED_AT => time(),
            Customer::_UPDATED_AT => time(),
        ];

        $check = $this->customerRepo->insert($dataInsert);
        if (!$check) {
            $this->message = 'No create new customer';
            goto next;
        }
        $this->status = 'success';
        $this->message = 'created new customer';
        next:
        return $this->responseData();

    }

    function updateCustomer(Request $request)
    {
        $request->validate([
            'email' => ['required|email|ends_with:@gmail.com' ],
            'fullName' => ['required','min:3','max:20'],
            'address' => ['required','min:3','max:20'],
            'phoneNumber' => ['required|min:10|regex:/(01)[0-9]{9}/']


        ]);
        $id = $request->input('id');
        $email = $request->input('email');
        $fullName = $request->input('fullName');
        $address = $request->input('address');
        $phoneNumber = $request->input('phoneNumber');
        $dataUpdate = [
            Customer::_EMAIL => $email,
            Customer::_FULLNAME => $fullName,
            Customer::_ADDRESS => $address,
            Customer::_PHONENUMBER => $phoneNumber,
            Customer::_UPDATED_AT => time()

        ];
//        dd($dataUpdate);
        $check = $this->customerRepo->update($id, $dataUpdate);
        if (!$check) {
            $this->message = 'No update customer';
            goto next;
        }
        $this->status = 'success';
        $this->message = 'updated customer';
        next:
        return $this->responseData();

    }
    function deleteCustomer(Request $request)
    {

        $id = $request->input('id');

        $check = $this->customerRepo->delete($id);
        if (!$check) {
            $this->message = 'No delete customer';
            goto next;
        }
        $this->status = 'success';
        $this->message = 'deleted customer';
        next:
        return $this->responseData();
    }
}
