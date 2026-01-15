<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class ListCompanyModel extends Model
{

    protected $table = 'ListCompany';
    protected $primaryKey = 'ListCompanyID';

    public $timestamps = false;


    protected $guarded = [];
    //const CREATED_AT = 'created_at';
    //const UPDATED_AT = 'updated_at';




}