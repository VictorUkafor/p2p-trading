<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Fee extends Model
{
    use SoftDeletes;
    
    protected $fillable = [
        'amount',
        'status',
    ];


    /**
     * Get the transfer of a fee.
     */
    public function transfer()
    {
        return $this->hasOne('App\Model\Transfer');
    } 

    /**
     * Get the transaction of a fee.
     */
    public function transaction()
    {
        return $this->hasOne('App\Model\Transaction');
    } 

 


}
