<?php
namespace \App\Repositories;

use \App\Repositories\EloquentRepository;
use \App\Models\Order;
class TestRepository extends EloquentRepository {

    public function __construct() {

    }
    public function getModel()
    {
        return Order::class;
        // TODO: Implement getModel() method.
    }
}
