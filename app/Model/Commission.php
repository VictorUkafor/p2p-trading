<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Commission extends Model
{
    use SoftDeletes;
    
    protected $fillable = [
        'user_id',
        'amount',
        'value',
    ];


    /**
     * Get the sellCrypto of a commission.
     */
    public function sellCrypto()
    {
        return $this->belongsTo('App\Model\sellCrypto', 'commission_id');
    } 

    /**
     * Get the transfer of a commission.
     */
    public function transfer()
    {
        return $this->belongsTo('App\Model\Transfer', 'commission_id');
    } 


}
