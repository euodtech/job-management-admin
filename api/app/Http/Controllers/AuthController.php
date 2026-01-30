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

        $this->validate($request, [

            'email' => 'required',

            'password' => 'required'

        ]);

        $user = UserLogin::leftJoin('ListUser', 'UserLogin.UserLoginID', '=', 'ListUser.UserLoginID')->where('UserLogin.Email', $request->input('email'))->where("ListUser.StatusActive", 0)->first();


        if($user != null){

            if(password_verify($request->input('password'),$user->Password)){
                $api_key = base64_encode(Str::random(40));

                // update apikey
                UserLogin::where('Email', $request->input('email'))
                  ->update(
                    [
                      'ApiKey' => $api_key,
                      'FirebaseToken' => $request->input('firebasetoken'),
                      "LastLogin" => date('Y-m-d H:i:s')
                    ]
                  );

                $customer = UserLogin::leftJoin('ListUser', 'UserLogin.UserLoginID', '=', 'ListUser.UserLoginID')->leftJoin('ListCompany', 'ListUser.ListCompanyID', '=', 'ListCompany.ListCompanyID')->where("UserLogin.Email", $request->input('email'))->first();

                $return = [
                    "UserID" => $customer->UserID,
                    "ApiKey" => $customer->ApiKey,
                    "Company" => $customer->CompanyName,
                    "CompanyID" => $customer->ListCompanyID,
                    "CompanyType" => $customer->CompanySubscribe,
                    "CompanyLabel" => ($customer->CompanySubscribe == 1) ? "Basic" : "Pro" ,
                    "UsernameTraxrooot" => $customer->username_traxroot,
                    "PasswordTraxrooot" => $customer->password_traxroot,
                    "UserRole" => $customer->UserRole,
                ];


               return response()->json([
                    'Success' => true,
                    'Data' => $return
                ],200);

            } else {

                return response()->json([
                    'Success' => false,
                    'Message'=>"Incorrect Password"
                ],401);

            }

        } else {

            return response()->json([
                'Success' => false,
                'Message'=>"Email Not Found, Please Create Account"
            ],401);

        }

    }

    public function check_company_driver($companyID)
    {

        $dataReturn = ListCompanyModel::where("ListCompanyID", $companyID)->first();

        return response()->json([
            'Success' => true,
            'CompanySubscribe' => $dataReturn->CompanySubscribe
        ],200);
    }

    public function logout(Request $request){

        $user = UserLogin::where('UserID', $request->user_id)->first();

        if($user){

            $user->ApiKey = null;

            //$user->logout_datetime = date("Y-m-d H:i:s");

            $saved = $user->save();

            if($saved){

                return response()->json([
                    'success' => true,
                    'message'=>"Berhasil keluar"
                ]);

            } else {

                return response()->json([
                    'success' => false,
                    'message'=>"Gagal keluar"
                ],401);

            }

        } else {
            return response()->json([
                'success' => false,
                'message' => 'User not found'
            ],404);
        }

    }

    public function reset_password(Request $request)
    {

      $this->validate($request, [

          'new_password' => 'required',

          'token' => 'required'
      ]);
      $new_password = $request->input('new_password');
      $token = $request->input('token');
      $user = RegistrationModel::where('key_resetpassword', $token)->first();


      if($user !== null) {

        $password_hash = Hash::make($new_password);


        $user->update([
          "Password" => $password_hash,
          "key_resetpassword" => null
        ]);

        return response()->json([
          "Success" => 200,
          "Message" => "Success Reset Password"
        ], 200);
      } else {

        return response()->json([
          "Success" => 404,
          "Message" => "User Not Found"
        ], 404);
      }
    }

    public function cek_token(Request  $request)
    {
        $cek_token = RegistrationModel::where("key_resetpassword", $request->input('token'))->get();

        if(count($cek_token) > 0) {
          return response()->json([
            "Success" => 200,
            "Message" => "Token Found"
          ], 200);
        } else {
          return response()->json([
            "Success" => 404,
            "Message" => "Token Not Found"
          ], 200);
        }
    }

    public function forgot_password(Request $request){

        $this->validate($request, [

            'email' => 'required'

        ]);
        
        $user = UserLogin::where('Email',$request->input('email'))->first();

        $length = 32;
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $encEmail = '';
        for ($i = 0; $i < $length; $i++) {
            $encEmail .= $characters[rand(0, $charactersLength - 1)];
        }

        UserLogin::where('Email', $request->input('email'))->update(
        [

            'key_resetpassword' => $encEmail,

        ]);


        if($user){

          $email = $request->input('email');
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
        </div>

        <!-- Footer -->
        <div class="email-footer">
            <strong>Copyright &copy; 2026</strong>
                All rights reserved.
        </div>
    </div>
</body>

</html>';
         try {
          Mail::send([], [], function ($message) use ($bodyNya, $request) {
              $message->to($request->input('email'))
                      ->from('support@primafit.co.id', 'PrimaFit')
                      ->subject('Your Password Request')
                      ->setBody($bodyNya, 'text/html')
                      ->addPart(strip_tags($bodyNya), 'text/plain'); // << Tambah ini
          });


          return response()->json([
              'Success' => 200,
              'Message' => 'Please check your email for your password',
          ], 200);

      } catch (\Exception $e) {
          return response()->json([
              'Success' => 500,
              'Message' => 'Failed to send email',
              'Error'   => $e->getMessage(), // Bisa di-hide saat production
          ], 500);
      }



        } else {

          return response()->json([
              'Success' => false,
              'Message' => 'User Not Found!'
          ],400);

          // return response()->json([
          //     'Success' => false,
          //     'Message' => 'User Not Found!'
          // ],401);

        }


      }


}