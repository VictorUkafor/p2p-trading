<?php

namespace App\Model;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Tymon\JWTAuth\Contracts\JWTSubject;


class User extends Authenticatable implements JWTSubject
{
    use Notifiable, SoftDeletes;

    protected $dates = ['deleted_at'];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'phone',
        'password',
        'date_of_birth',
        'active',
        'activation_token',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
        'activation_token',
    ];

    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }
    
    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [];
    }


    /**
     * Get the BVN details of a user.
     */
    public function bvn()
    {
        return $this->hasOne('App\Model\Bvn');
    }    


    /**
     * Get the wallet of a user.
     */
    public function wallet()
    {
        return $this->hasOne('App\Model\Wallet');
    } 

    /**
     * Get the mails of a user.
     */
    public function mails()
    {
        return $this->hasMany('App\Model\Mail');
    }  


    /**
     * Get the bank accounts of a user.
     */
    public function bankAccounts()
    {
        return $this->hasMany('App\Model\BankAccount');
    }


    /**
     * Get the buyCryptos of a user.
     */
    public function buyCryptos()
    {
        return $this->hasMany('App\Model\BuyCrypto');
    }


    /**
     * Get the transfers of a user.
     */
    public function transfers()
    {
        return $this->hasMany('App\Model\Transfers');
    }
    
    
    /**
     * Get the BVN notifications of a user.
     */
    public function notifications()
    {
        return $this->hasOne('App\Model\Notification');
    }  


    /**
     * Get the commissions of a user.
     */
    public function commissions()
    {
        return $this->hasMany('App\Model\Commission');
    } 

}

