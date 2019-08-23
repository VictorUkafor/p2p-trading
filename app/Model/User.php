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
        'google2fa_secret',
        'sms2fa_otp',
        'sms2fa',
        'two_fa',
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
        'google2fa_secret',
    ];

    public function setGoogle2faSecretAttribute($value)
    {
        $this->attributes['google2fa_secret'] = encrypt($value);
    }

    /**
     * Decrypt the user's google_2fa secret.
     *
     * @param  string  $value
     * @return string
     */
    public function getGoogle2faSecretAttribute($value)
    {
        return $value ? decrypt($value) : "";
    }

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
     * Get the banks of a user.
     */
    public function banks()
    {
        return $this->hasMany('App\Model\Bank');
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
     * Get the ads of a user.
     */
    public function ads()
    {
        return $this->hasMany('App\Model\Ad');
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
     * Get the clients of the user.
     */
    public function clients()
    {
        return $this->hasMany('App\Model\Client');
    }  

}

