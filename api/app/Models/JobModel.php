<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class JobModel extends Model
{

    protected $table = 'ListJob';
    protected $primaryKey = 'JobID';

    public $timestamps = false;

    protected $fillable = [
        'JobName', 'CustomerID', 'CompanyID', 'TypeJob', 'UserID',
        'Status', 'Notes', 'JobDate', 'CreatedBy', 'AssignWhen',
        'FinishWhen', 'created_at'
    ];

    public function details()
    {
        return $this->hasMany(JobDetailModel::class, 'ListJobID', 'JobID');
    }



}
