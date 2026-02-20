<?php

namespace App\Http\Helpers;

use App\Models\TransactionModel;
use GuzzleHttp\Client;

class ForstokHelpers
{
    // private $url;
    // private $key;
    // public function __construct()
    // {
    //     $this->url = env('SHIPPING_URL_END');
    //     $this->key = env('SHIPPING_KEY');
    // }
    public function authForstok($country)
    {
        $configs = [
            1 => ['id' => env('FORSTOK_ID_ID', '10057'), 'secret_key' => env('FORSTOK_SECRET_ID', ''), 'type' => 'channel'],
            2 => ['id' => env('FORSTOK_ID_MY', '10798'), 'secret_key' => env('FORSTOK_SECRET_MY', ''), 'type' => 'channel'],
            5 => ['id' => env('FORSTOK_ID_SG', '10799'), 'secret_key' => env('FORSTOK_SECRET_SG', ''), 'type' => 'channel'],
        ];

        $data = $configs[$country] ?? $configs[1];

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://integration.forstok.com/api/v2/auth',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => json_encode($data),
        ));
        $response = curl_exec($curl);

        curl_close($curl);
        return json_decode($response, true)['data']['token'];
    }

    public function updateForstok($transNumber, $status)
    {
        $trans = TransactionModel::where('TransactionNumber', $transNumber)->first();
        $country = $trans['CountryID'];

        $channels = [
            1 => ['account_id' => (int) env('FORSTOK_ACCOUNT_ID', 8130), 'channel' => (int) env('FORSTOK_ID_ID', 10057)],
            2 => ['account_id' => (int) env('FORSTOK_ACCOUNT_MY', 12297), 'channel' => (int) env('FORSTOK_ID_MY', 10798)],
            5 => ['account_id' => (int) env('FORSTOK_ACCOUNT_SG', 12298), 'channel' => (int) env('FORSTOK_ID_SG', 10799)],
        ];

        $channel = $channels[$country] ?? $channels[1];

        $token = $this->authForstok($country);

        if ($status == 'settlement') {
            $payment_status = 3;
        }

        if ($status == 'deny' || $status == 'expire') {
            $payment_status = 4;
        }

        $dansParsedAry = [
            "account_id" => $channel['account_id'],
            "id" => $trans['OrderForstockID'],
            "local_id" => $trans['TransactionNumber'],
            "payment_status" => $payment_status,
            "payment_method" => "BCA",
            "payment_description" => "Paid",
            "paid_at" => ""
        ];

        $jsonDataChange = json_encode($dansParsedAry);

        $addOrder = new client();

        $headersOrder = [
            'Content-Type' => 'application/json',
            'Authorization' => $token

        ];
        //Hit Forstock Cancel Transaction Ini
        $requestChange = $addOrder->request(
            'POST',
            'https://integration.forstok.com/api/v2/channel/' . $channel['channel'] . '/order/status_payment_update',
            [
                'headers' => $headersOrder,
                'body' => $jsonDataChange

            ]

        );

        $resOrder = json_decode($requestChange->getBody(), true);
        return $resOrder;
    }
}
