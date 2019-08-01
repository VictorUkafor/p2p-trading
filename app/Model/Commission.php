<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Commission extends Model
{
    use SoftDeletes;
    
    protected $fillable = [
        'user_id',
        'description',
        'buy_crypto_id',
        'transfer_id',
        'amount',
        'value',
    ];

    /**
     * Get the buyCrypto of a commission.
     */
    public function buyCrypto()
    {
        return $this->hasOne('App\Model\BuyCrypto', 'buy_crypto_id');
    } 

    /**
     * Get the transfer of a commission.
     */
    public function transfer()
    {
        return $this->hasOne('App\Model\Transfer', 'transfer_id');
    } 

}
