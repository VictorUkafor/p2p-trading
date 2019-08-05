<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Commission extends Model
{
    use SoftDeletes;
    
    protected $fillable = [
        'amount',
        'status',
    ];


    /**
     * Get the sellCrypto of a commission.
     */
    public function SellTrade()
    {
        return $this->belongsTo('App\Model\Trade');
    } 

    /**
     * Get the transfer of a commission.
     */
    public function transfer()
    {
        return $this->belongsTo('App\Model\Transfer');
    } 

 


}
