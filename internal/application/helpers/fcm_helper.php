<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Check that FCM prerequisites (config, service account file) are met.
 * Returns the resolved config on success, or false on failure.
 * Logs the error only once per request to avoid log spam.
 *
 * @return array|false  ['service_account_path' => ..., 'project_id' => ...] or false
 */
function _fcm_check_prerequisites()
{
    static $result = null;
    static $checked = false;

    if ($checked) {
        return $result;
    }
    $checked = true;

    $CI =& get_instance();
    if ($CI->config->load('firebase', true, true) === false) {
        log_message('error', 'FCM: firebase.php config file not found');
        $result = false;
        return false;
    }

    $service_account_path = $CI->config->item('firebase_service_account', 'firebase');
    $project_id           = $CI->config->item('firebase_project_id', 'firebase');

    if (empty($project_id)) {
        log_message('error', 'FCM: firebase_project_id not configured');
        $result = false;
        return false;
    }

    if (empty($service_account_path) || !file_exists($service_account_path)) {
        log_message('error', 'FCM: Service account file not found at: ' . ($service_account_path ?: '(empty)'));
        $result = false;
        return false;
    }

    $result = [
        'service_account_path' => $service_account_path,
        'project_id'           => $project_id,
    ];
    return $result;
}

/**
 * Send a single FCM push notification to a device.
 *
 * @param string $fcm_token  The device's FCM registration token
 * @param string $title      Notification title
 * @param string $body       Notification body
 * @param int|string $job_id Job ID to include in the data payload
 * @param string $type       Notification type (e.g. 'new_job', 'job_assigned', 'reschedule_approved')
 * @return bool  True on success, false on failure
 */
function send_fcm_notification($fcm_token, $title, $body, $job_id = null, $type = 'new_job')
{
    if (empty($fcm_token)) {
        return false;
    }

    $config = _fcm_check_prerequisites();
    if ($config === false) {
        return false;
    }

    try {
        $access_token = _fcm_get_access_token($config['service_account_path']);
        if ($access_token === false) {
            return false;
        }

        $url = 'https://fcm.googleapis.com/v1/projects/' . $config['project_id'] . '/messages:send';

        $payload = [
            'message' => [
                'token' => $fcm_token,
                'notification' => [
                    'title' => $title,
                    'body'  => $body,
                ],
                'data' => [
                    'job_id' => (string) ($job_id ?? ''),
                    'type'   => $type,
                ],
                'android' => [
                    'notification' => [
                        'channel_id' => 'efms_job_notifications',
                        'sound'      => 'default',
                    ],
                ],
            ],
        ];

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Authorization: Bearer ' . $access_token,
            'Content-Type: application/json',
        ]);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);

        $response  = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curl_err  = curl_error($ch);
        curl_close($ch);

        if ($http_code !== 200) {
            log_message('error', 'FCM: HTTP ' . $http_code . ' - ' . $response . ($curl_err ? ' cURL: ' . $curl_err : ''));
            return false;
        }

        return true;

    } catch (\Throwable $e) {
        log_message('error', 'FCM: Exception - ' . $e->getMessage());
        return false;
    }
}

/**
 * Send FCM notifications to multiple devices.
 * Checks prerequisites once before the loop to avoid redundant work.
 *
 * @param array  $fcm_tokens Array of FCM tokens
 * @param string $title      Notification title
 * @param string $body       Notification body
 * @param int|string $job_id Job ID
 * @param string $type       Notification type
 * @return int   Number of successfully sent notifications
 */
function send_fcm_to_multiple($fcm_tokens, $title, $body, $job_id = null, $type = 'new_job')
{
    if (empty($fcm_tokens) || !is_array($fcm_tokens)) {
        return 0;
    }

    // Check prerequisites once before the loop
    $config = _fcm_check_prerequisites();
    if ($config === false) {
        return 0;
    }

    $sent = 0;
    foreach ($fcm_tokens as $token) {
        if (!empty($token) && send_fcm_notification($token, $title, $body, $job_id, $type)) {
            $sent++;
        }
    }
    return $sent;
}

/**
 * Get the FCM token for a driver by their UserID (ListUser.UserID).
 *
 * @param int $user_id  The driver's UserID from ListUser table
 * @return string|null   The FCM token, or null if not found
 */
function get_driver_fcm_token($user_id)
{
    $CI =& get_instance();
    $query = $CI->db->select('ul.FirebaseToken')
                     ->from('ListUser lu')
                     ->join('UserLogin ul', 'lu.UserLoginID = ul.UserLoginID', 'inner')
                     ->where('lu.UserID', (int) $user_id)
                     ->get();

    if (!$query) {
        return null;
    }

    $row = $query->row();
    return ($row && !empty($row->FirebaseToken)) ? $row->FirebaseToken : null;
}

/**
 * Get FCM tokens for all active drivers in a company.
 *
 * @param int $company_id  The ListCompanyID
 * @return array           Array of FCM tokens (non-empty only)
 */
function get_company_driver_fcm_tokens($company_id)
{
    $CI =& get_instance();
    $query = $CI->db->select('ul.FirebaseToken')
                     ->from('ListUser lu')
                     ->join('UserLogin ul', 'lu.UserLoginID = ul.UserLoginID', 'inner')
                     ->where('lu.ListCompanyID', (int) $company_id)
                     ->where('lu.StatusActive', 0)
                     ->where('lu.deleted_at IS NULL', null, false)
                     ->where('ul.FirebaseToken IS NOT NULL', null, false)
                     ->where('ul.FirebaseToken !=', '')
                     ->get();

    if (!$query) {
        return [];
    }

    $tokens = [];
    foreach ($query->result() as $row) {
        $tokens[] = $row->FirebaseToken;
    }
    return $tokens;
}

/**
 * Get the driver UserID assigned to a job.
 *
 * @param int $job_id  The JobID
 * @return int|null    The UserID, or null if not found/unassigned
 */
function get_job_driver_id($job_id)
{
    $CI =& get_instance();
    $query = $CI->db->select('UserID')
                     ->from('ListJob')
                     ->where('JobID', (int) $job_id)
                     ->where('UserID IS NOT NULL', null, false)
                     ->get();

    if (!$query) {
        return null;
    }

    $row = $query->row();
    return $row ? (int) $row->UserID : null;
}

/**
 * Log a sent notification to the Notif table.
 *
 * @param int    $user_login_id  Target UserLoginID
 * @param string $fcm_token      FCM token used
 * @param string $title          Notification title
 * @param string $message        Notification body
 * @param string $type           Notification type
 * @param string $status         'sent' or 'failed'
 */
function log_notification($user_login_id, $fcm_token, $title, $message, $type, $status = 'sent')
{
    $CI =& get_instance();
    $now = date('Y-m-d H:i:s');

    $CI->db->insert('Notif', [
        'PendingDate'   => $now,
        'DeliveryDate'  => ($status === 'sent') ? $now : null,
        'UserLoginID'   => $user_login_id,
        'FirebaseToken' => $fcm_token,
        'NotifTitle'    => $title ?? '',
        'NotifMessage'  => $message,
        'NotifImage'    => '',
        'NotifStatus'   => $status ?? 'sent',
        'Type'          => $type ?? '',
        'created_at'    => $now,
        'updated_at'    => $now,
    ]);
}

/**
 * Obtain an OAuth2 access token from the Firebase service account.
 * Uses pure PHP (openssl) — no external packages required.
 * Caches the token until it expires.
 *
 * @param string $service_account_path  Path to service account JSON
 * @return string|false  The access token, or false on failure
 */
function _fcm_get_access_token($service_account_path)
{
    static $cached_token = null;
    static $cached_expiry = 0;

    // Return cached token if still valid (with 60s buffer)
    if ($cached_token !== null && time() < ($cached_expiry - 60)) {
        return $cached_token;
    }

    try {
        $sa = json_decode(file_get_contents($service_account_path), true);
        if (!$sa || empty($sa['private_key']) || empty($sa['client_email']) || empty($sa['token_uri'])) {
            log_message('error', 'FCM: Invalid service account JSON — missing required fields');
            return false;
        }

        // Build JWT header + claims
        $now = time();
        $header = _fcm_base64url(json_encode(['alg' => 'RS256', 'typ' => 'JWT']));
        $claims = _fcm_base64url(json_encode([
            'iss'   => $sa['client_email'],
            'scope' => 'https://www.googleapis.com/auth/firebase.messaging',
            'aud'   => $sa['token_uri'],
            'iat'   => $now,
            'exp'   => $now + 3600,
        ]));

        // Sign with RSA-SHA256
        $unsigned = $header . '.' . $claims;
        $signature = '';
        $key = openssl_pkey_get_private($sa['private_key']);
        if ($key === false) {
            log_message('error', 'FCM: Failed to parse private key from service account');
            return false;
        }
        if (!openssl_sign($unsigned, $signature, $key, OPENSSL_ALGO_SHA256)) {
            log_message('error', 'FCM: openssl_sign failed');
            return false;
        }
        $jwt = $unsigned . '.' . _fcm_base64url($signature);

        // Exchange JWT for access token
        $ch = curl_init($sa['token_uri']);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query([
            'grant_type' => 'urn:ietf:params:oauth:grant-type:jwt-bearer',
            'assertion'  => $jwt,
        ]));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/x-www-form-urlencoded']);

        $response  = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($http_code !== 200 || $response === false) {
            log_message('error', 'FCM: Token exchange failed HTTP ' . $http_code . ' - ' . ($response ?: ''));
            return false;
        }

        $token_data = json_decode($response, true);
        if (isset($token_data['access_token'])) {
            $cached_token  = $token_data['access_token'];
            $cached_expiry = $now + ($token_data['expires_in'] ?? 3600);
            return $cached_token;
        }

        log_message('error', 'FCM: No access_token in response - ' . $response);
        return false;

    } catch (\Throwable $e) {
        log_message('error', 'FCM: OAuth error - ' . $e->getMessage());
        return false;
    }
}

/**
 * Base64url encode (RFC 4648) — used for JWT.
 *
 * @param string $data
 * @return string
 */
function _fcm_base64url($data)
{
    return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
}
