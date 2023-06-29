<?php
namespace App\Http\Controllers;
use App\Models\Role;
use App\Repositories\RoleRepository;

class RoleController extends Controller {
    private $roleRepo;
    public function __construct(RoleRepository  $roleRepo) {
        $this->roleRepo = $roleRepo;
    }
    public function getListRole() {
       $data =  $this->roleRepo->getListRole();

       if (!$data) {
           $this->status= 'failure';
           goto next;
       }
       $this->status = 'success';
       next:
        return $this->responseData($data ?? []);

    }
}
