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

    /**
     * Get the addresses of the wallet.
     */
    public function addresses()
    {
        return $this->hasMany('App\Model\WalletAddress');
    }


    /**
     * Get the user of the wallet.
     */
    public function user()
    {
        return $this->belongsTo('App\Model\User');
    }


    /**
     * Get the transfers of the wallet.
     */
    public function transfers()
    {
        return $this->hasMany('App\Model\Transfer', 'sender_wallet_id');
    }


    /**
     * Get the receives of the wallet.
     */
    public function receives()
    {
        return $this->hasMany('App\Model\Transfer', 'receiver_wallet_id');
    }


    /**
     * Get the sales of the wallet.
     */
    public function sales()
    {
        return $this->hasMany('App\Model\SellCrypto');
    }


    /**
     * Get the purchases of the wallet.
     */
    public function purchases()
    {
        return $this->hasMany('App\Model\BuyCrypto');
    }

}
