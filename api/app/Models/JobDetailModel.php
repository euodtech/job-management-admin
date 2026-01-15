<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class JobDetailModel extends Model
{

    protected $table = 'ListJobDetail';
    protected $primaryKey = 'ListDetailID';

    public $timestamps = false;

    protected $guarded = [];
    //const CREATED_AT = 'created_at';
    //const UPDATED_AT = 'updated_at';

    public function job()
    {
        return $this->belongsTo(JobModel::class, 'ListJobID', 'JobID');
    }





}
