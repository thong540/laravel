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
    const TABLE = 'users';
    const _ID = 'id';
    const _EMAIL = 'email';
    const _PASSWORD = 'password';
    const _FULLNAME = 'fullName';
    const _ADDRESS = 'address';
    const _PHONENUMBER = 'phoneNumber';
    const _CREATED_AT = 'created_at';
    const _UPDATED_AT = 'updated_at';
    protected $fillable = [
        self::_ID,
        self::_EMAIL,
        self::_PASSWORD,
        self::_FULLNAME,
        self::_ADDRESS,
        self::_PHONENUMBER,
        self::_CREATED_AT.
        self::_UPDATED_AT
    ];
    protected $hidden = [
        self::_PASSWORD
    ];
//    public function roles()
//    {
//        return $this->belongsToMany(Role::class, 'user_role');
//    }
}
