<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $table = 'categories';


    protected $fillable = ['name', 'parent_id', 'created_by', 'updated_by', 'created_at', 'updated_at'];

}
