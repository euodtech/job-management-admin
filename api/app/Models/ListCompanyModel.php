<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class ListCompanyModel extends Model
{

    protected $table = 'ListCompany';
    protected $primaryKey = 'ListCompanyID';

    public $timestamps = false;


    protected $fillable = [
        'CompanyName', 'CompanyCode', 'CompanyPhone', 'CompanyEmail',
        'CompanySubscribe', 'CompanyLogo', 'UserLoginID'
    ];




}