<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Tymon\JWTAuth\Contracts\JWTSubject;
//use Illuminate\Foundation\Auth\User as Authenticatable;
class User extends Authenticatable implements JWTSubject
{
    public $timestamps = false;
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
//    protected $hidden = [
//        self::_PASSWORD
//    ];
//    public function roles()
//    {
//        return $this->belongsToMany(Role::class, 'user_role');
//    }
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }
    public function getJWTCustomClaims()
    {
        return [];
    }

}


