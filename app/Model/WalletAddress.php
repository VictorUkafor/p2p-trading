<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class WalletAddress extends Model
{
    use SoftDeletes;
    
    protected $fillable = [
        'wallet_id',
        'address',
        'coin',
        'balance',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'id',
        'wallet_id',
        'created_at',
        'updated_at',
        'deleted_at'
    ];


    /**
     * Get the wallet of the address.
     */
    public function wallet()
    {
        return $this->belongsTo('App\Model\Wallet');
    } 


}
