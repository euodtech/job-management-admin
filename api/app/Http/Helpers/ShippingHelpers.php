<?php

namespace App\Http\Helpers;

use Illuminate\Support\Facades\Http;
use App\Models\MyAddressEcom;
use App\Models\TransactionEcom;
use App\Models\TransactionDetailEcom;

class ShippingHelpers
{
    private $url;
    private $key;
    public function __construct()
    {
        $this->url = env('SHIPPING_URL_END');
        $this->key = env('SHIPPING_KEY');
    }

    private function getData($url,$method,$data)
    {
        //tets
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => $method,
            CURLOPT_POSTFIELDS => $data
        ));

        $response = curl_exec($curl);

        curl_close($curl);
        return json_decode($response,true);
    }

    public function checkPrice($request)
    {
        $data = [
            "key" => $this->key,
            "service" => "RETAIL",
            "country" => 'singapore',//ucwords($request->country),
            "city" => ucwords($request->city),
            "postcode" => $request->postcode,
            "type" => "NOC",
            "weight" => $request->weight, //gross static T 500gr
            "size" => $request->size, //dimabil dari yg tertinggi
            "category" => "General Package",
            "origin" => "JKT",
            "shipmentType" => $request->type,
        ];

        // return $data;
        return $this->getData($this->url."pricing.php","POST",$data);
    }

    public function getDataAwb($url,$method,$data)
    {
        // sample format product request
        // $product = [
        //     [
        //         "description" => "OLYMPICO IN BLACK CYAN ORTRANGE",
        //         "qty" => "1",
        //         "unit" => "Pcs",
        //         "currency"=> "SGD",
        //         "value" => "10.05"
        //     ]
        // ];
        $data = [
            "key" => $this->key,
            "shipperReference" => "Shipper Reference",
            "companyName" => " PT. Vita Nova Atletik",
            "deliveryAddress" => $trans->address->AddressName,
            "postcode" => $trans->address->postcode,
            "country" => ucwords($trans->address->country->CodeLabel),
            "contactPerson" => $trans->address->ReceiverName,
            "telephone" => $trans->address->AddressPhone,
            "commodityName" => 'Footwear',
            "quantity" => $transDetail->sum('Qty'),
            "totalNettWeight" => $transDetail->sum('Qty') * 1,
            "totalGrossWeight" => $transDetail->sum('Qty') * 1,
            "dimension" => 30 * 20 * 15,
            "invoiceValue" => "SGD 0",
            "coverByInsurance" => "NO",
            "taxDutyAtDestination" => "Receiver",
            "service" => "RETAIL",
            "specialInstruction" => "",
            "type" => "NOC",
            "category" => "General Package",
            "shipmentType" => "Express",
            "transportCharges" => "Prepaid",
            "typeOfExport" => "Permanent",
            "invoice" => $product
        ];
        return $this->getData($this->url."awb_post.php","POST",json_encode($data));
    }

    public function  tracking($awb){
        $curl = curl_init();

        curl_setopt_array($curl, array(
        CURLOPT_URL => $this->url.'current.php?key='.$this->key.'&awb='.$awb.'',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'GET',
        ));

        $response = curl_exec($curl);

        curl_close($curl);
        return json_decode($response,true);
    }

}