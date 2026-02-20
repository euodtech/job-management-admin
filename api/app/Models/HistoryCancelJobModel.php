<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class HistoryCancelJobModel extends Model
{

    protected $table = 'HistoryCancelJob';
    protected $primaryKey = 'HistoryCancelJobID';

    public $timestamps = false;

    protected $fillable = ['JobID', 'UserBefore', 'Reason', 'created_at'];




}