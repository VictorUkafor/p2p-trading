<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Ad extends Model
{
    use SoftDeletes;
    
    protected $fillable = [
        'user_id',
        'referenceNo',
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

    /**
     * Get the creator of the add.
     */
    public function creator()
    {
        return $this->belongsTo('App\Model\User', 'user_id');
    }


    /**
     * Get the client of the.
     */
    public function clients()
    {
        return $this->hasMany('App\Model\Client');
    }


}
