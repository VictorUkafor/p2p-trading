<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Transfer extends Model
{
    //use SoftDeletes;
    
    protected $fillable = [
        'method',
        'amount',
        'coin',
        'commission_id',
        'sender_wallet_id',
        'receiver_wallet_id',
    ];


    /**
     * Get the commission of the transfer.
     */
    public function commission()
    {
        return $this->belongsTo('App\Model\Commission');
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
