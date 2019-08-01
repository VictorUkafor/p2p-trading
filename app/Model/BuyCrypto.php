<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BuyCrypto extends Model
{
    use SoftDeletes;
    
    protected $fillable = [
        'user_id',
        'payment_method',
        'method_details',
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

}
