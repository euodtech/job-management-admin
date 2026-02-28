<?php

if (!defined('BASEPATH')) exit('No direct script access allowed');

use PhpOffice\PhpSpreadsheet\Spreadsheet;

class Excel extends Spreadsheet {

    public function __construct(){
        parent::__construct();
    }

}
