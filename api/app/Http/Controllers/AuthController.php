<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use App\Models\UserLogin;
use App\Models\RegistrationModel;
use App\Models\AdminModel;
use App\Models\LevelModel;
use App\Models\ListCompanyModel;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Collection;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;


class AuthController extends Controller {

    // public function registration(Request $request)
    // {
    //     $user = UserLogin::create([
    //         "Username" => $request->input("username"),
    //         "Password" => password_hash($request->input("password"), PASSWORD_BCRYPT),
    //         "ApiKey"   => base64_encode(str_random(40)), // langsung generate API key
    //     ]);

    //     return response()->json([
    //         "Success" => true,
    //         "Message" => "User created successfully",
    //         "Data"    => $user
    //     ], 201);
    // }

    public function login(Request $request)
    {
        try {
            // Validate input
            $this->validate($request, [
                'email' => 'required|email|max:255',
                'password' => 'required|string|min:5'
            ]);

            // Sanitize email input
            $email = strtolower(trim($request->input('email')));
            $password = $request->input('password');
            $firebaseToken = $request->input('firebasetoken');

            // Find user with active status
            $user = UserLogin::leftJoin('ListUser', 'UserLogin.UserLoginID', '=', 'ListUser.UserLoginID')
                ->where('UserLogin.Email', $email)
                ->where("ListUser.StatusActive", 0)
                ->first();

            if ($user != null) {

                if (password_verify($password, $user->Password)) {
                    
                    // Generate API key
                    $api_key = base64_encode(Str::random(40));

                    // Start transaction
                    DB::beginTransaction();

                    // Update API key and last login
                    UserLogin::where('Email', $email)
                        ->update([
                            'ApiKey' => $api_key,
                            'FirebaseToken' => $firebaseToken,
                            "LastLogin" => date('Y-m-d H:i:s')
                        ]);

                    // Get full customer data
                    $customer = UserLogin::leftJoin('ListUser', 'UserLogin.UserLoginID', '=', 'ListUser.UserLoginID')
                        ->leftJoin('ListCompany', 'ListUser.ListCompanyID', '=', 'ListCompany.ListCompanyID')
                        ->where("UserLogin.Email", $email)
                        ->first();

                    // Check if customer data exists
                    if (!$customer) {
                        DB::rollBack();
                        return response()->json([
                            'Success' => false,
                            'Message' => "Error retrieving user data"
                        ], 500);
                    }

                    DB::commit();

                    $return = [
                        "UserID" => $customer->UserID,
                        "ApiKey" => $customer->ApiKey,
                        "Company" => $customer->CompanyName,
                        "CompanyID" => $customer->ListCompanyID,
                        "CompanyType" => $customer->CompanySubscribe,
                        "CompanyLabel" => ($customer->CompanySubscribe == 1) ? "Basic" : "Pro",
                        "UsernameTraxrooot" => $customer->username_traxroot,
                        "PasswordTraxrooot" => $customer->password_traxroot,
                        "UserRole" => $customer->UserRole,
                    ];

                    return response()->json([
                        'Success' => true,
                        'Data' => $return
                    ], 200);

                } else {

                    return response()->json([
                        'Success' => false,
                        'Message' => "Incorrect Password"
                    ], 401);

                }

            } else {

                return response()->json([
                    'Success' => false,
                    'Message' => "Email Not Found, Please Create Account"
                ], 401);

            }

        } catch (ValidationException $e) {
            return response()->json([
                'Success' => false,
                'Message' => "Validation failed",
                'Errors' => $e->errors()
            ], 422);

        } catch (\Exception $e) {
            DB::rollBack();
            
            Log::error('Login Error: ' . $e->getMessage(), [
                'email' => $email ?? 'unknown',
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'Success' => false,
                'Message' => "An error occurred during login"
            ], 500);
        }
    }

    public function check_company_driver($companyID)
    {
        try {
            // Sanitize input
            $companyID = (int) $companyID;

            // Validate company ID
            if ($companyID <= 0) {
                return response()->json([
                    'Success' => false,
                    'Message' => 'Invalid company ID'
                ], 400);
            }

            // Get company data
            $dataReturn = ListCompanyModel::where("ListCompanyID", $companyID)->first();

            // Check if company exists
            if (!$dataReturn) {
                return response()->json([
                    'Success' => false,
                    'Message' => 'Company not found'
                ], 404);
            }

            return response()->json([
                'Success' => true,
                'CompanySubscribe' => $dataReturn->CompanySubscribe
            ], 200);

        } catch (\Exception $e) {
            Log::error('check_company_driver Error: ' . $e->getMessage(), [
                'company_id' => $companyID ?? 'unknown',
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'Success' => false,
                'Message' => 'An error occurred while checking company'
            ], 500);
        }
    }

    public function logout(Request $request)
    {
        try {
            // Validate input
            $this->validate($request, [
                'user_id' => 'required|integer'
            ]);

            // Sanitize input
            $userID = (int) $request->input('user_id');

            // Validate user ID
            if ($userID <= 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid user ID'
                ], 400);
            }

            // Find user
            $user = UserLogin::where('UserID', $userID)->first();

            if ($user) {

                // Start transaction
                DB::beginTransaction();

                // Clear API key
                $user->ApiKey = null;

                //$user->logout_datetime = date("Y-m-d H:i:s");

                $saved = $user->save();

                if ($saved) {

                    DB::commit();

                    return response()->json([
                        'success' => true,
                        'message' => "Berhasil keluar"
                    ], 200);

                } else {

                    DB::rollBack();

                    Log::warning('Logout save failed', [
                        'user_id' => $userID
                    ]);

                    return response()->json([
                        'success' => false,
                        'message' => "Gagal keluar"
                    ], 500);

                }

            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'User not found'
                ], 404);
            }

        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);

        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Logout Error: ' . $e->getMessage(), [
                'user_id' => $userID ?? 'unknown',
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'An error occurred during logout'
            ], 500);
        }
    }

   public function reset_password(Request $request)
    {
        try {
            // Validate input
            $this->validate($request, [
                'new_password' => 'required|string|min:5|max:255',
                'token' => 'required|string'
            ]);

            // Sanitize input
            $new_password = $request->input('new_password');
            $token = trim($request->input('token'));

            // Validate token is not empty after trimming
            if (empty($token)) {
                return response()->json([
                    "Success" => false,
                    "Message" => "Invalid reset token"
                ], 400);
            }

            // Find user by reset token
            $user = RegistrationModel::where('key_resetpassword', $token)->first();

            if ($user !== null) {

                // Start transaction
                DB::beginTransaction();

                // Hash the new password
                $password_hash = Hash::make($new_password);

                // Update password and clear reset token
                $user->update([
                    "Password" => $password_hash,
                    "key_resetpassword" => null
                ]);

                DB::commit();

                return response()->json([
                    "Success" => true,
                    "Message" => "Success Reset Password"
                ], 200);

            } else {

                return response()->json([
                    "Success" => false,
                    "Message" => "Invalid or expired reset token"
                ], 404);
            }

        } catch (ValidationException $e) {
            return response()->json([
                "Success" => false,
                "Message" => "Validation failed",
                "Errors" => $e->errors()
            ], 422);

        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('reset_password Error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                "Success" => false,
                "Message" => "An error occurred while resetting password"
            ], 500);
        }
    }

    public function cek_token(Request $request)
    {
        try {
            // Validate input
            $this->validate($request, [
                'token' => 'required|string'
            ]);

            // Sanitize input
            $token = trim($request->input('token'));

            // Validate token is not empty after trimming
            if (empty($token)) {
                return response()->json([
                    "Success" => false,
                    "Message" => "Invalid token"
                ], 400);
            }

            // Check if token exists
            $cek_token = RegistrationModel::where("key_resetpassword", $token)->first();

            if ($cek_token !== null) {
                return response()->json([
                    "Success" => true,
                    "Message" => "Token Found"
                ], 200);
            } else {
                return response()->json([
                    "Success" => false,
                    "Message" => "Token Not Found"
                ], 404);
            }

        } catch (ValidationException $e) {
            return response()->json([
                "Success" => false,
                "Message" => "Validation failed",
                "Errors" => $e->errors()
            ], 422);

        } catch (\Exception $e) {
            Log::error('cek_token Error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                "Success" => false,
                "Message" => "An error occurred while checking token"
            ], 500);
        }
    }

    public function forgot_password(Request $request)
    {
        try {
            // Validate input
            $this->validate($request, [
                'email' => 'required|email|max:255'
            ]);

            // Sanitize email input
            $email = strtolower(trim($request->input('email')));

            // Find user
            $user = UserLogin::where('Email', $email)->first();

            if ($user) {

                // Generate secure random token
                $encEmail = bin2hex(random_bytes(32)); // More secure than custom random

                // Start transaction
                DB::beginTransaction();

                // Update reset token
                UserLogin::where('Email', $email)->update([
                    'key_resetpassword' => $encEmail,
                ]);

                DB::commit();

                // Prepare email body
                $bodyNya = '<!DOCTYPE html>
                    <html lang="en">

                    <head>
                        <meta charset="UTF-8">
                        <meta name="viewport" content="width=device-width, initial-scale=1.0">
                        <title>Forgot Password</title>
                        <style>
                            body {
                                font-family: Arial, sans-serif;
                                margin: 0;
                                padding: 0;
                                background-color: #f4f4f9;
                                color: #333;
                            }

                            .email-container {
                                max-width: 600px;
                                margin: 20px auto;
                                background: #ffffff;
                                border-radius: 8px;
                                box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
                                overflow: hidden;
                            }

                            .email-header {
                                background: #070f26 !important;
                                color: white;
                                text-align: center;
                                padding: 20px;
                            }

                            .email-header h1 {
                                margin: 0;
                                font-size: 24px;
                            }

                            .email-body {
                                padding: 20px;
                                text-align: center;
                            }

                            .email-body p {
                                font-size: 16px;
                                line-height: 1.6;
                            }

                            .reset-button {
                                display: inline-block;
                                margin: 20px 0;
                                padding: 12px 25px;
                                font-size: 16px;
                                background: #070f26 !important;
                                color: white !important;
                                text-decoration: none;
                                border-radius: 5px;
                                transition: background 0.3s;
                            }

                            .reset-button:hover {
                                background: #5548c8;
                            }

                            .email-footer {
                                background: #f4f4f9;
                                text-align: center;
                                padding: 15px;
                            }

                            .social-icons {
                                margin: 10px 0;
                            }

                            .social-icons a {
                                margin: 0 8px;
                                text-decoration: none;
                            }

                            .social-icons svg {
                                width: 24px;
                                height: 24px;
                                fill: #6c63ff;
                                transition: fill 0.3s;
                            }

                            .social-icons svg:hover {
                                fill: #5548c8;
                            }

                            /* Responsive Styles */
                            @media screen and (max-width: 600px) {
                                .email-container {
                                    width: 90%;
                                    margin: 10px auto;
                                }

                                .email-body p {
                                    font-size: 14px;
                                    line-height: 1.5;
                                }

                                .reset-button {
                                    font-size: 14px;
                                    padding: 10px 20px;
                                }

                                .social-icons svg {
                                    width: 20px;
                                    height: 20px;
                                }

                                .email-header h1 {
                                    font-size: 20px;
                                }
                            }
                        </style>
                    </head>

                    <body>
                        <div class="email-container">
                            <!-- Header -->
                            <div class="email-header">
                                <h1>Reset Your Password</h1>
                            </div>

                            <!-- Body -->
                            <div class="email-body">
                                <img src="http://quetraverse.pro/efms/internal/assets/dist/logo_efms.jpg" width="100" style="border-radius: 5px;" alt="">
                                <p>Hi there,</p>
                                <p>We received a request to reset your password. Click the button below to proceed. If you did not request this, you can safely ignore this email.</p>
                                <a href="http://quetraverse.pro/efms/internal/forgot-password?token=' . $encEmail . '" class="reset-button">Reset Password</a>
                                <p style="margin-top: 20px; font-size: 14px; color: #666;">This link will expire in 24 hours for security reasons.</p>
                            </div>

                            <!-- Footer -->
                            <div class="email-footer">
                                <strong>Copyright &copy; 2026</strong>
                                All rights reserved.
                            </div>
                        </div>
                    </body>

                    </html>';

                // Send email
                try {
                    Mail::send([], [], function ($message) use ($bodyNya, $email) {
                        $message->to($email)
                            ->from('support@primafit.co.id', 'PrimaFit')
                            ->subject('Password Reset Request')
                            ->setBody($bodyNya, 'text/html')
                            ->addPart(strip_tags($bodyNya), 'text/plain');
                    });

                    return response()->json([
                        'Success' => true,
                        'Message' => 'Please check your email for password reset instructions',
                    ], 200);

                } catch (\Exception $e) {
                    DB::rollBack();
                    
                    Log::error('forgot_password Email Error: ' . $e->getMessage(), [
                        'email' => $email,
                        'trace' => $e->getTraceAsString()
                    ]);

                    return response()->json([
                        'Success' => false,
                        'Message' => 'Failed to send email. Please try again later.',
                    ], 500);
                }

            } else {

                // Security: Don't reveal if email exists or not
                // Return same message as success to prevent email enumeration
                return response()->json([
                    'Success' => true,
                    'Message' => 'If your email is registered, you will receive a password reset link.',
                ], 200);

            }

        } catch (ValidationException $e) {
            return response()->json([
                'Success' => false,
                'Message' => 'Validation failed',
                'Errors' => $e->errors()
            ], 422);

        } catch (\Exception $e) {
            DB::rollBack();
            
            Log::error('forgot_password Error: ' . $e->getMessage(), [
                'email' => $email ?? 'unknown',
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'Success' => false,
                'Message' => 'An error occurred. Please try again later.'
            ], 500);
        }
    }


}