<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SellCrypto extends Model
{
    use SoftDeletes;
    
    protected $fillable = [
        'wallet_id',
        'bank_account_id',
        'commission_id',
        'cryptocurrency',
        'amount',
        'value',
        'status'
    ];

    /**
     * Get the wallet of the sellCrypto.
     */
    public function wallet()
    {
        return $this->belongsTo('App\Model\Wallet');
    }

    /**
     * Get the account payable to.
     */
    public function bankAccount()
    {
        return $this->belongsTo('App\Model\BankAccount');
    }


    /**
     * Get the commission of the transaction.
     */
    public function commission()
    {
        return $this->belongsTo('App\Model\Commission');
    }

}
