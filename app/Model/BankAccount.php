<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BankAccount extends Model
{
    use SoftDeletes;
    
    protected $fillable = [
        'user_id',
        'account_number',
        'account_name',
        'bank',
        'internet_banking',
    ];


    /**
     * Get the sellCrypto of a bank account.
     */
    public function sellCryptos()
    {
        return $this->belongsToMany('App\Model\SellCrypto', 'bank_account_id');
    }

}
