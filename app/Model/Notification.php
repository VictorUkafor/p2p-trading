<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{    
    protected $fillable = [
        'user_id',
        'push_notification',
        'email_notification',
        'auto_logout',
    ];
}
