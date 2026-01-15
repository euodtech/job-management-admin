<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use App\Models\UserLogin;
use App\Models\DriverModel;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Collection;
use Illuminate\Http\Request;


class UserController extends Controller {

    public function get_user($userID)
    {
        $dataUser = DriverModel::where("UserID", $userID)->first();

        if ($dataUser) {
            $return = [
                "Fullname" => $dataUser->Fullname,
                "Email" => $dataUser->Email,
                "PhoneNumber" => $dataUser->PhoneNumber
            ];

            return response()->json([
                "Success" => true,
                "Data" => $return
            ], 200);
        } else {
            return response()->json([
                "Success" => false,
                "Message" => "User tidak ditemukan"
            ], 404);
        }

    }

}
