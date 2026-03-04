<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * FCM Integration Test Controller
 *
 * Access via: http://localhost:8080/be-fms/internal/TestFcm
 *
 * DELETE THIS FILE after testing is complete.
 */
class TestFcm extends CI_Controller
{
    private $passed = 0;
    private $failed = 0;
    private $skipped = 0;

    public function __construct()
    {
        parent::__construct();
        $this->load->model('M_Global');
        $this->load->helper('fcm');
    }

    public function index()
    {
        header('Content-Type: text/plain; charset=utf-8');

        echo "=== FCM Backend Integration Tests ===\n\n";

        $this->test_composer();
        $this->test_firebase_config();
        $this->test_helper_functions();
        $this->test_database_queries();
        $this->test_real_data();
        $this->test_oauth2();
        $this->test_edge_cases();

        echo "\n=== Test Summary ===\n";
        echo "Passed:  {$this->passed}\n";
        echo "Failed:  {$this->failed}\n";
        echo "Skipped: {$this->skipped}\n";
        echo "Total:   " . ($this->passed + $this->failed + $this->skipped) . "\n";

        if ($this->failed === 0) {
            echo "\nAll tests passed!\n";
        } else {
            echo "\nSome tests failed. Review the output above.\n";
        }
    }

    /**
     * Send a real test notification to a specific driver.
     * Usage: /TestFcm/sendTest/{userID}
     */
    public function sendTest($userID = null)
    {
        header('Content-Type: text/plain; charset=utf-8');

        if (empty($userID)) {
            echo "Usage: /TestFcm/sendTest/{userID}\n";
            echo "Sends a test push notification to the driver with the given UserID.\n";
            return;
        }

        $userID = (int) $userID;
        $token = get_driver_fcm_token($userID);

        if (empty($token)) {
            echo "No FCM token found for UserID: $userID\n";
            echo "The driver must log in to the app first to register their FCM token.\n";
            return;
        }

        echo "FCM Token found: " . substr($token, 0, 30) . "...\n";
        echo "Sending test notification...\n\n";

        $result = send_fcm_notification(
            $token,
            'Test Notification',
            'This is a test from E-FMS backend - ' . date('H:i:s'),
            0,
            'test'
        );

        if ($result) {
            echo "[SUCCESS] Notification sent successfully!\n";
            echo "Check the device for the push notification.\n";
        } else {
            echo "[FAILED] Notification failed to send.\n";
            echo "Check the CI log files for details.\n";
        }
    }

    // --- Test Groups ---

    private function test_composer()
    {
        echo "--- Environment ---\n";

        // Check PHP openssl extension (needed for JWT signing)
        if (extension_loaded('openssl')) {
            $this->pass('PHP openssl extension loaded');
        } else {
            $this->fail('PHP openssl extension loaded', 'openssl is required for FCM JWT signing');
        }

        // Check cURL extension (needed for FCM API calls)
        if (extension_loaded('curl')) {
            $this->pass('PHP curl extension loaded');
        } else {
            $this->fail('PHP curl extension loaded', 'curl is required for FCM API calls');
        }

        echo "\n";
    }

    private function test_firebase_config()
    {
        echo "--- Firebase Configuration ---\n";

        $this->config->load('firebase', true);
        $path = $this->config->item('firebase_service_account', 'firebase');
        $pid  = $this->config->item('firebase_project_id', 'firebase');

        if (!empty($pid)) {
            $this->pass("Firebase project ID: $pid");
        } else {
            $this->fail('Firebase project ID configured');
        }

        if (!empty($path)) {
            $this->pass("Service account path: $path");
            if (file_exists($path)) {
                $this->pass('Service account file exists');
                $sa = json_decode(file_get_contents($path), true);
                if ($sa && isset($sa['project_id'])) {
                    $this->pass('Service account JSON valid (project: ' . $sa['project_id'] . ')');
                } else {
                    $this->fail('Service account JSON valid', 'Missing project_id field');
                }
            } else {
                $this->skip('Service account file exists', "Not found at: $path");
            }
        } else {
            $this->fail('Service account path configured');
        }
        echo "\n";
    }

    private function test_helper_functions()
    {
        echo "--- FCM Helper Functions ---\n";
        $fns = [
            '_fcm_check_prerequisites',
            'send_fcm_notification',
            'send_fcm_to_multiple',
            'get_driver_fcm_token',
            'get_company_driver_fcm_tokens',
            'get_job_driver_id',
            'log_notification',
            '_fcm_get_access_token',
        ];
        foreach ($fns as $fn) {
            if (function_exists($fn)) {
                $this->pass("$fn() exists");
            } else {
                $this->fail("$fn() exists");
            }
        }
        echo "\n";
    }

    private function test_database_queries()
    {
        echo "--- Database Schema ---\n";

        // UserLogin.FirebaseToken column
        try {
            $r = $this->db->query("SHOW COLUMNS FROM UserLogin LIKE 'FirebaseToken'")->row();
            $r ? $this->pass('UserLogin.FirebaseToken column exists') : $this->fail('UserLogin.FirebaseToken column exists');
        } catch (Exception $e) {
            $this->fail('UserLogin.FirebaseToken column', $e->getMessage());
        }

        // Notif table
        try {
            $r = $this->db->query("SHOW TABLES LIKE 'Notif'")->row();
            $r ? $this->pass('Notif table exists') : $this->fail('Notif table exists');
        } catch (Exception $e) {
            $this->fail('Notif table', $e->getMessage());
        }

        // Query functions with non-existent IDs
        try {
            $tok = get_driver_fcm_token(0);
            $tok === null ? $this->pass('get_driver_fcm_token(0) returns null') : $this->fail('Expected null', "Got: $tok");
        } catch (Exception $e) {
            $this->fail('get_driver_fcm_token(0)', $e->getMessage());
        }

        try {
            $toks = get_company_driver_fcm_tokens(0);
            (is_array($toks) && empty($toks)) ? $this->pass('get_company_driver_fcm_tokens(0) returns []') : $this->fail('Expected []');
        } catch (Exception $e) {
            $this->fail('get_company_driver_fcm_tokens(0)', $e->getMessage());
        }

        try {
            $did = get_job_driver_id(0);
            $did === null ? $this->pass('get_job_driver_id(0) returns null') : $this->fail('Expected null', "Got: $did");
        } catch (Exception $e) {
            $this->fail('get_job_driver_id(0)', $e->getMessage());
        }
        echo "\n";
    }

    private function test_real_data()
    {
        echo "--- Real Data ---\n";

        $driver = $this->db->select('lu.UserID, lu.Fullname, ul.FirebaseToken, lu.ListCompanyID')
                            ->from('ListUser lu')
                            ->join('UserLogin ul', 'lu.UserLoginID = ul.UserLoginID', 'inner')
                            ->where('lu.StatusActive', 0)
                            ->where('lu.deleted_at IS NULL', null, false)
                            ->limit(1)
                            ->get()
                            ->row();

        if (!$driver) {
            $this->skip('Real data tests', 'No active drivers in database');
            echo "\n";
            return;
        }

        echo "Driver: {$driver->Fullname} (UserID: {$driver->UserID}, Company: {$driver->ListCompanyID})\n";

        $tok = get_driver_fcm_token($driver->UserID);
        if ($tok) {
            $this->pass('FCM token found: ' . substr($tok, 0, 25) . '...');
        } else {
            $this->skip('FCM token for driver', 'No FirebaseToken stored (driver must log in to app first)');
        }

        $ctoks = get_company_driver_fcm_tokens($driver->ListCompanyID);
        $this->pass('Company #' . $driver->ListCompanyID . ' has ' . count($ctoks) . ' driver(s) with FCM tokens');

        // Count total active drivers vs drivers with tokens
        $totalDrivers = $this->db->from('ListUser')
                                  ->where('ListCompanyID', $driver->ListCompanyID)
                                  ->where('StatusActive', 0)
                                  ->where('deleted_at IS NULL', null, false)
                                  ->count_all_results();
        echo "  Total active drivers in company: $totalDrivers, With FCM tokens: " . count($ctoks) . "\n";

        echo "\n";
    }

    private function test_oauth2()
    {
        echo "--- OAuth2 Token ---\n";

        $this->config->load('firebase', true);
        $path = $this->config->item('firebase_service_account', 'firebase');

        if (empty($path) || !file_exists($path)) {
            $this->skip('OAuth2 token fetch', 'Service account file not found');
            echo "\n";
            return;
        }

        try {
            $token = _fcm_get_access_token($path);
            if ($token !== false && !empty($token)) {
                $this->pass('OAuth2 access token obtained: ' . substr($token, 0, 25) . '...');
            } else {
                $this->fail('OAuth2 access token', 'Returned false');
            }
        } catch (Exception $e) {
            $this->fail('OAuth2 access token', $e->getMessage());
        }
        echo "\n";
    }

    private function test_edge_cases()
    {
        echo "--- Edge Cases ---\n";

        $r = send_fcm_notification('', 'Test', 'Body');
        $r === false ? $this->pass('Empty token returns false') : $this->fail('Empty token should return false');

        $r = send_fcm_notification(null, 'Test', 'Body');
        $r === false ? $this->pass('Null token returns false') : $this->fail('Null token should return false');

        $r = send_fcm_to_multiple([], 'Test', 'Body');
        $r === 0 ? $this->pass('Empty array returns 0') : $this->fail('Empty array should return 0');

        echo "\n";
    }

    // --- Helpers ---

    private function pass($msg) { $this->passed++; echo "[PASS] $msg\n"; }
    private function fail($msg, $reason = '') { $this->failed++; echo "[FAIL] $msg" . ($reason ? " - $reason" : "") . "\n"; }
    private function skip($msg, $reason = '') { $this->skipped++; echo "[SKIP] $msg" . ($reason ? " - $reason" : "") . "\n"; }
}
