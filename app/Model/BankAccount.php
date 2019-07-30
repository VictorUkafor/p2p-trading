<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BankAccount extends Model
{
    use SoftDeletes;
    
    protected $fillable = [
        'user_id',
        'account_number',
        'account_name',
        'bank',
        'internet_banking',
    ];
}
