<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Transaction extends Model
{
    use SoftDeletes;
    
    protected $fillable = [
        'client_id',
        'fee_id',
        'coin',
        'approved_time',
        'amount_in_cash',
        'amount_in_coin',
        'status'
    ];


    /**
     * Get the fee of a transaction.
     */
    public function fee()
    {
        return $this->belongsTo('App\Model\Fee');
    } 

    /**
     * Get the client of a transaction.
     */
    public function client()
    {
        return $this->belongsTo('App\Model\Client');
    } 


}

