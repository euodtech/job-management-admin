<?php
defined('BASEPATH') or exit('No direct script access allowed');
if (!function_exists('idr')) {
    function idr($number)
    {
        $result_idr = "Rp ".number_format($number, 0, ',', '.');
        return $result_idr;
    }
}
