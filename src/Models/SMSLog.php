<?php

namespace Amani64\SMS\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SMSLog extends Model
{

    use SoftDeletes;

    public $table = 'sms_log';

    protected $fillable = [
        "driver",
        "message",
        "mobiles",
        "status",
        "response",
    ];

    protected $attributes = [

    ];

    protected $hidden = [

    ];

    protected $casts = [
        "driver" => 'string',
        "message" => 'string',
        "mobiles" => 'string',
        "response" => 'string',
    ];

    protected $dates = [
        'created_at',
        'updated_at',
    ];

}
