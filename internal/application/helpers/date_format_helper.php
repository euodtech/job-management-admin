<?php
defined('BASEPATH') or exit('No direct script access allowed');
if (!function_exists('return_date_format')) {
    function return_date_format($value)
    {
        $timestamp = strtotime($value);
        return date("l, F j, Y", $timestamp);
    }


    function return_date_format_with_time($value)
    {
        if (empty($value)) return '-';
        $timestamp = strtotime($value);
        if ($timestamp === false) return '-';
        $date = date("M j, Y", $timestamp);
        $time = date("H:i:s", $timestamp);
        // Hide time for legacy date-only values (stored as midnight)
        if ($time === '00:00:00') {
            return $date;
        }
        return $date . '<br><span class="text-xs text-gray-400">' . date("h:i A", $timestamp) . '</span>';
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