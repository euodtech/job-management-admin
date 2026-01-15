<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class DriverModel extends Model
{

    protected $table = 'ListUser';
    protected $primaryKey = 'UserID';

    public $timestamps = false;


    protected $guarded = [];
    //const CREATED_AT = 'created_at';
    //const UPDATED_AT = 'updated_at';




}