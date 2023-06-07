<?php
namespace App\Http\Controllers;

use App\Models\Logger;
use Illuminate\Http\Request;
class TestController extends Controller {


    public function __construct() {

    }
    public function test(Request $request) {
        $userId = $request->input('user_id');
        $action = $request->input('action');
        $logger = new Logger;
        $logger->userId = $userId;
        $logger->action = $action;
        $logger->save();
        dd(123);
        return $this->responseData('ok');
    }
}
