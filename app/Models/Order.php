<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{

    use HasFactory;
    public $timestamps = false;
    const TABLE = 'orders';
    const _ID = 'id';
    const _NAME = 'name';
    const _USER_ID = 'user_id';
    const _CUSTOMER_ID = 'customer_id';
    const _STATUS = 'status';
    const _CREATED_AT = 'created_at';
    const _UPDATED_AT = 'updated_at';
    protected $fillable = [
        self::_ID,
        self::_NAME,
        self::_USER_ID,
        self::_CUSTOMER_ID,
        self::_STATUS,
        self::_CREATED_AT,
        self::_UPDATED_AT
    ];
}
