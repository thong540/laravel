<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;
    const TABLE = 'products';
    const _ID = 'id';
    const _NAME = 'name';
    const _CATEGORY_ID = 'category_id';
    const _IMAGE = 'image';
    const _DESCRIPTION = 'description';
    const _PRICE = 'price';
    const _CREATED_AT = 'created_at';
    const _UPDATED_AT = 'updated_at';
    protected $fillable = [
        self::_ID,
        self::_NAME,
        self::_CATEGORY_ID,
        self::_IMAGE,
        self::_DESCRIPTION,
        self::_PRICE,
        self::_CREATED_AT,
        self::_UPDATED_AT
    ];
}
