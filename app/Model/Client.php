<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Client extends Model
{
    use SoftDeletes;
    
    protected $fillable = [
        'user_id',
        'ad_id',
        'status'
    ];


    

    /**
     * Get the user of the client.
     */
    public function user()
    {
        return $this->belongsTo('App\Model\User');
    }


    /**
     * Get the ad of the add.
     */
    public function ad()
    {
        return $this->belongsTo('App\Model\Ad');
    }


    /**
     * Get the ad of the add.
     */
    public function transaction()
    {
        return $this->hasOne('App\Model\Transaction');
    }

}

