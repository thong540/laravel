<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Model
{
    protected $fillable = [
        'email',
        'password',
        'fullName',
        'address',
        'phoneNumber',
        'created_at',
        'updated_at'
    ];
    protected $hidden = [
        'password'
    ];
//    public function roles()
//    {
//        return $this->belongsToMany(Role::class, 'user_role');
//    }
}
