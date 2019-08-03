<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Bank extends Model
{
    use SoftDeletes;
    
    protected $fillable = [
        'user_id',
        'account_number',
        'account_name',
        'date_of_birth',
        'bank',
        'balance',
        'bvn',
    ];

    /**
     * Get the user of the bank.
     */
    public function user()
    {
        return $this->belongsTo('App\Model\User');
    } 
}
