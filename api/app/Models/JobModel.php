<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class JobModel extends Model
{

    protected $table = 'ListJob';
    protected $primaryKey = 'JobID';

    public $timestamps = false;

    protected $guarded = [];
    //const CREATED_AT = 'created_at';
    //const UPDATED_AT = 'updated_at';

    public function details()
    {
        return $this->hasMany(JobDetailModel::class, 'ListJobID', 'JobID');
    }



}
