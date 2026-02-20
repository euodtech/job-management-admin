<?php
defined('BASEPATH') or exit('No direct script access allowed');
if (!function_exists('idr')) {
    function idr($number)
    {
        $result_idr = "Rp ".number_format($number, 0, ',', '.');
        return $result_idr;
    }
}

if (!function_exists('time_ago')) {
    function time_ago($timestamp)
    {
        $diff = time() - $timestamp;
        if ($diff < 60) return 'Just now';
        if ($diff < 3600) { $m = floor($diff / 60); return $m . ($m == 1 ? ' min ago' : ' mins ago'); }
        if ($diff < 86400) { $h = floor($diff / 3600); return $h . ($h == 1 ? ' hr ago' : ' hrs ago'); }
        if ($diff < 2592000) { $d = floor($diff / 86400); return $d . ($d == 1 ? ' day ago' : ' days ago'); }
        return date('M d, Y h:i A', $timestamp);
    }
}
