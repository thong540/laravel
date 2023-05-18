<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    protected $message = '';
    protected $status = '';

    protected function responseData($data = [])
    {
        return [
            'status' => $this->status,
            'data' => $data,
            'message' => $this->message,
            'code' => 200
        ];
    }
}
