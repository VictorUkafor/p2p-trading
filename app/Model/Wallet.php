<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class Wallet extends Model
{
    use SoftDeletes;
    
    protected $fillable = [
        'user_id',
        'BTC',
        'LTC',
        'ETH',
    ];
}
