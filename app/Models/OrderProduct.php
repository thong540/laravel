<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderProduct extends Model
{
    use HasFactory;
    public $timestamps = false;
    const TABLE = 'order_products';
    const _ID = 'id';
    const _ORDER_ID = 'order_id';
    const _PRODUCT_ID = 'product_id';
    const _QUANTITY = 'quantity';
    const _PRICE = 'price';
    const _CREATED_AT = 'created_at';
    const _UPDATED_AT = 'updated_at';
    protected $fillable = [
        self::_ID,
        self::_ORDER_ID,
        self::_PRODUCT_ID,
        self::_QUANTITY,
        self::_PRICE,
        self::_CREATED_AT,
        self::_UPDATED_AT
    ];
}
