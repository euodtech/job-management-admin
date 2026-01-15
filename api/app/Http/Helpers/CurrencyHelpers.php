<?php
namespace App\Http\Helpers;

use Illuminate\Support\Facades\Http;

class CurrencyHelpers
{
    public function currency($value, $country){
       
        if($country == 'id'){
            return 'Rp. '.number_format($value,2);
        } else if($country == 'my'){
            return 'RM. '.number_format($value, 2);
        } else if ($country == 'sg'){
            return 'SGD '.number_format($value, 2);
        }
    }
}