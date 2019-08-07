<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Transfer extends Model
{
    use SoftDeletes;
    
    protected $fillable = [
        'method',
        'amount',
        'coin',
        'fee_id',
        'sender_wallet_id',
        'receiver_wallet_id',
    ];


    /**
     * Get the fee of the transfer.
     */
    public function fee()
    {
        return $this->belongsTo('App\Model\Fee');
    }


    /**
     * Get the sender of the transfer.
     */
    public function sender()
    {
        return $this->belongsTo('App\Model\Wallet', 'sender_wallet_id');
    }


    /**
     * Get the receiver of the transfer.
     */
    public function receiver()
    {
        return $this->belongsTo('App\Model\Wallet', 'receiver_wallet_id');
    }

}
