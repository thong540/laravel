<?php

namespace App\Repositories;

use App\Models\Logger;
use App\Repositories\EloquentRepository;

class LoggerRepository extends EloquentRepository
{


    public function getModel()
    {
        return Logger::class;
    }
}
