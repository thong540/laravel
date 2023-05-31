<?php

namespace App\Http\Controllers;

use App\Repositories\OrderProductRepository;
use App\Repositories\OrderRepository;
use Illuminate\Http\Request;

class OrderProductController extends Controller
{
    private $orderProductRepo;
    public function __construct(OrderProductRepository $orderProductRepo)
    {
        $this->orderProductRepo = $orderProductRepo;
    }

    public function getProductByOrderId()
    {

    }

}
