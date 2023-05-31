<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    public $timestamps = false;

    const TABLE = 'customers';
    const _ID = 'id';
    const _EMAIL = 'email';
    const _FULLNAME = 'fullName';
    const _ADDRESS = 'address';
    const _PHONENUMBER = 'phoneNumber';
    const _CREATED_AT = 'created_at';
    const _UPDATED_AT = 'updated_at';
    const ADMIN = 1;
    const MANAGER = 2;
    const STAFF = 3;
    const USER  = 4;
    protected $fillable = [
        self::_ID,
        self::_EMAIL,
        self::_FULLNAME,
        self::_ADDRESS,
        self::_PHONENUMBER,
        self::_CREATED_AT,
        self::_UPDATED_AT
    ];
}
