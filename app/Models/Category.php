<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $table = 'categories';

    const TABLE = 'categories';
    const _ID = 'id';
    const _NAME = 'name';
    const _PARENT_ID = 'parent_id';
    const _DESCRIPTION = 'description';
    const _CREATED_AT = 'created_at';
    const _UPDATED_AT = 'updated_at';
    protected $fillable = [
        self::_ID,
        self::_NAME,
        self::_PARENT_ID,
        self::_DESCRIPTION,
        self::_CREATED_AT,
        self::_UPDATED_AT,
    ];

}
