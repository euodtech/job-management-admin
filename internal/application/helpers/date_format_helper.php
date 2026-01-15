<?php
defined('BASEPATH') or exit('No direct script access allowed');
if (!function_exists('return_date_format')) {
    function return_date_format($value)
    {
        $timestamp = strtotime($value);
        return date("l, F j, Y", $timestamp);
    }


    function return_date_format_detail($value)
    {
        // Pastikan value bisa diparse jadi date
        $timestamp = strtotime($value);
        
        // Format: Day Name, dd Month YYYY - HH:ii:ss
        $result_date = date("l, d F Y - H:i:s", $timestamp);

        return $result_date;
    }


}