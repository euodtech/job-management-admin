<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\UserLogin;
use App\Models\ListCompanyModel;
use Illuminate\Support\Facades\Log;

class TraxrootProxyController extends Controller
{
    private function traxrootDecrypt($ciphertext)
    {
        $key = env('TRAXROOT_ENCRYPT_KEY', '');
        if (empty($key) || empty($ciphertext)) {
            return $ciphertext;
        }
        $data = base64_decode($ciphertext, true);
        if ($data === false || strlen($data) < 17) {
            return $ciphertext; // not encrypted or invalid, return as-is
        }
        $iv = substr($data, 0, 16);
        $encrypted = substr($data, 16);
        $decrypted = openssl_decrypt($encrypted, 'AES-256-CBC', $key, OPENSSL_RAW_DATA, $iv);
        return ($decrypted !== false) ? $decrypted : $ciphertext;
    }

    private function getCompanyTraxrootCredentials(Request $request)
    {
        $apiKey = $request->header('X-API-Key') ?? $request->input('x-key');
        $user = UserLogin::leftJoin('ListUser', 'UserLogin.UserLoginID', '=', 'ListUser.UserLoginID')
            ->where('ApiKey', $apiKey)
            ->first();

        if (!$user || empty($user->ListCompanyID)) {
            return null;
        }

        $company = ListCompanyModel::where('ListCompanyID', $user->ListCompanyID)->first();

        if (!$company || empty($company->username_traxroot)) {
            return null;
        }

        return [
            'username' => $this->traxrootDecrypt($company->username_traxroot),
            'password' => $this->traxrootDecrypt($company->password_traxroot),
        ];
    }

    private function callTraxrootApi($method, $url, $headers = [], $body = null)
    {
        $ch = curl_init($url);

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);

        if ($method === 'POST') {
            curl_setopt($ch, CURLOPT_POST, true);
            if ($body) {
                curl_setopt($ch, CURLOPT_POSTFIELDS, $body);
            }
        }

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        curl_close($ch);

        if ($error) {
            return ['error' => $error, 'code' => 500];
        }

        return ['body' => $response, 'code' => $httpCode];
    }

    public function getToken(Request $request)
    {
        $creds = $this->getCompanyTraxrootCredentials($request);

        if (!$creds) {
            return response()->json([
                'Success' => false,
                'Message' => 'Traxroot credentials not configured for this company'
            ], 404);
        }

        $payload = json_encode([
            'userName'  => $creds['username'],
            'password'  => $creds['password'],
            'subUserId' => 0,
            'language'  => 'en'
        ]);

        $result = $this->callTraxrootApi('POST', 'https://connect.traxroot.com/api/Token', [
            'Content-Type: application/json',
            'Accept: application/json'
        ], $payload);

        if (isset($result['error'])) {
            Log::error('Traxroot Token API Error: ' . $result['error']);
            return response()->json([
                'Success' => false,
                'Message' => 'Failed to connect to Traxroot'
            ], 502);
        }

        return response($result['body'], $result['code'])
            ->header('Content-Type', 'application/json');
    }

    public function getObjectsStatus(Request $request)
    {
        $token = $request->input('token');

        if (empty($token)) {
            return response()->json([
                'Success' => false,
                'Message' => 'Traxroot token is required'
            ], 400);
        }

        $result = $this->callTraxrootApi('GET', 'https://connect.traxroot.com/api/ObjectsStatus', [
            'Authorization: Bearer ' . $token,
            'Accept: application/json'
        ]);

        if (isset($result['error'])) {
            Log::error('Traxroot ObjectsStatus API Error: ' . $result['error']);
            return response()->json([
                'Success' => false,
                'Message' => 'Failed to connect to Traxroot'
            ], 502);
        }

        return response($result['body'], $result['code'])
            ->header('Content-Type', 'application/json');
    }

    public function getGeozones(Request $request)
    {
        $token = $request->input('token');

        if (empty($token)) {
            return response()->json([
                'Success' => false,
                'Message' => 'Traxroot token is required'
            ], 400);
        }

        $result = $this->callTraxrootApi('GET', 'https://connect.traxroot.com/api/Geozones', [
            'Authorization: Bearer ' . $token,
            'Accept: application/json'
        ]);

        if (isset($result['error'])) {
            Log::error('Traxroot Geozones API Error: ' . $result['error']);
            return response()->json([
                'Success' => false,
                'Message' => 'Failed to connect to Traxroot'
            ], 502);
        }

        return response($result['body'], $result['code'])
            ->header('Content-Type', 'application/json');
    }
}
