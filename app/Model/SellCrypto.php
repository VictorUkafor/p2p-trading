<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SellCrypto extends Model
{
    use SoftDeletes;
    
    protected $fillable = [
        'user_id',
        'bank_account_id',
        'commission_id',
        'cryptocurrency',
        'amount',
        'value',
        'status'
    ];

    /**
     * Get the user of the buyCrypto.
     */
    public function user()
    {
        return $this->belongsTo('App\Model\User');
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
