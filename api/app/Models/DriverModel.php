<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class DriverModel extends Model
{

    protected $table = 'ListUser';
    protected $primaryKey = 'UserID';

    public $timestamps = false;


    protected $fillable = [
        'Fullname', 'ListCompanyID', 'Email', 'PhoneNumber',
        'UserRole', 'StatusActive', 'UserLoginID', 'Category',
        'Rank', 'License', 'LicenseValidUntil'
    ];




}