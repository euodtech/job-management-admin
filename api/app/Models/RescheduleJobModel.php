<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class RescheduleJobModel extends Model
{

    protected $table = 'RescheduledJob';
    protected $primaryKey = 'RescheduledID';

    public $timestamps = false;

    protected $fillable = [
        'JobID', 'CurrentDateJob', 'RescheduledDateJob', 'Reason',
        'StatusApproved', 'created_at'
    ];

}
