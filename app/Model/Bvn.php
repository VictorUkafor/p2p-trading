<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Bvn extends Model
{

    use SoftDeletes;
    
    protected $fillable = [
        'user_id',
        'bvn_number',
        'first_name',
        'middle_name',
        'last_name',
        'phone',
        'verified',
        'otp_code'
    ];


    protected $hidden = [
        'otp_code',
    ];


    /**
     * Get the user of the bvn.
     */
    public function user()
    {
        return $this->belongsTo('App\Model\User');
    }

}
