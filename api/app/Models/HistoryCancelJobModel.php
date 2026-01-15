<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class HistoryCancelJobModel extends Model
{

    protected $table = 'HistoryCancelJob';
    protected $primaryKey = 'HistoryCancelJobID';

    public $timestamps = false;

    protected $guarded = [];
    //const CREATED_AT = 'created_at';
    //const UPDATED_AT = 'updated_at';




}