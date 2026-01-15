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
        if ($country == 1) {
            //indonesia
            $data = array('id' => '10057', 'secret_key' => '8wx2LlcvfJBEBBhTuV8lvtszRnU8aK6o', 'type' => 'channel');
        } else if ($country == 2) {
            //Malaysia
            $data = array('id' => '10798', 'secret_key' => 'p7fCzc7abFjzgCBLYL9uHpUa34xGXHSg', 'type' => 'channel');
        } else if ($country == 5) {
            //Singapore
            $data = array('id' => '10799', 'secret_key' => 'BeDafw8GYKvz9gmr24gvf2yQD6y2wBKS', 'type' => 'channel');
        } else {
            //indonesia
            $data = array('id' => '10057', 'secret_key' => '8wx2LlcvfJBEBBhTuV8lvtszRnU8aK6o', 'type' => 'channel');
        }

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
        // return $country;
        if ($country == 1) {
            $channel = [
                'account_id' => 8130,
                'channel' => 10057
            ];
        } else if ($country == 2) {
            $channel = [
                'account_id' => 12297,
                'channel' => 10798
            ];
        } else if ($country == 5) {
            $channel = [
                'account_id' => 12298,
                'channel' => 10799
            ];
        } else {
            $channel = [
                'account_id' => 8130,
                'channel' => 10057
            ];
        }

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
