<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class UserLogin extends Model
{

    protected $table = 'UserLogin';
    protected $primaryKey = 'UserLoginID';

    public $timestamps = false;


    protected $guarded = [];
    //const CREATED_AT = 'created_at';
    //const UPDATED_AT = 'updated_at';




}