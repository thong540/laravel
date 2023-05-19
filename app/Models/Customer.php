<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    use HasFactory;
    protected $fillable = [
        'email',
        'password',
        'fullName',
        'address',
        'phoneNumber',
        'created_by',
        'updated_by',
        'created_at',
        'updated_at'
    ];
}
