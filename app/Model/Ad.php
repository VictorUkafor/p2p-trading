<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Ad extends Model
{
    use SoftDeletes;
    
    protected $fillable = [
        'user_id',
        'type',
        'bank_account_id',
        'coin',
        'price',
        'price_type',
        'min',
        'max',
        'deadline',
        'state',
    ];


    /**
     * Get the account payable to.
     */
    public function bankAccount()
    {
        return $this->belongsTo('App\Model\BankAccount');
    }


}
