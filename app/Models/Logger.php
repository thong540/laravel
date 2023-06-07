<?php
namespace App\Models;
//use Illuminate\Database\Eloquent\Model;
use Jenssegers\Mongodb\Eloquent\Model;
class Logger extends Model {
    protected $connection = 'mongodb';
    protected $collection = 'loggers';
    const TABLE = 'loggers';
    const _USER_ID = 'user_id';
    const _ACTION = 'action';
    const _TIME = 'time';
//    const _CREATED_AT = 'created_at';
//    const _UPDATED_AT = 'updated_at';

    protected $fillable = [self::_USER_ID, self::_ACTION, self::_TIME];

}

