<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Transfer extends Model
{
    use SoftDeletes;
    
    protected $fillable = [
        'user_id',
        'receiver_address',
        'receiver_username',
        'description',
        'cryptocurrency',
        'amount',
        'status'
    ];
}
