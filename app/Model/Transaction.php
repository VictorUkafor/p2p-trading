<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Transaction extends Model
{
    use SoftDeletes;
    
    protected $fillable = [
        'client_id',
        'fee_id',
        'coin',
        'amount_in_cash',
        'amount_in_coin',
        'status'
    ];


}

