<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class UserLogin extends Model
{

    protected $table = 'UserLogin';
    protected $primaryKey = 'UserLoginID';

    public $timestamps = false;


    protected $fillable = [
        'Fullname', 'Email', 'Password', 'ApiKey', 'FirebaseToken',
        'LastLogin', 'LastActivity', 'key_resetpassword', 'Role'
    ];

    protected $hidden = ['Password', 'ApiKey', 'key_resetpassword'];




}