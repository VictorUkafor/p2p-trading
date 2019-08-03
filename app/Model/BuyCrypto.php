<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BuyCrypto extends Model
{
    use SoftDeletes;
    
    protected $fillable = [
        'wallet_id',
        'payment_method',
        'method_details',
        'cryptocurrency',
        'amount',
        'value',
        'status'
    ];

    /**
     * Get the wallet of the buyCrypto.
     */
    public function wallet()
    {
        return $this->belongsTo('App\Model\Wallet');
    }

}
