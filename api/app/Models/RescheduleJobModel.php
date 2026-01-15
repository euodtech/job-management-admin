<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class RescheduleJobModel extends Model
{

    protected $table = 'RescheduledJob';
    protected $primaryKey = 'RescheduledID';

    public $timestamps = false;

    protected $guarded = [];
    //const CREATED_AT = 'created_at';
    //const UPDATED_AT = 'updated_at';

}
