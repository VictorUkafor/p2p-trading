<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Mail extends Model
{
    use SoftDeletes;
    
    protected $fillable = [
        'user_id',
        'name',
        'email',
        'subject',
        'message',
        'file',
    ];
}
