<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use App\Models\JobModel;
use App\Models\RescheduleJobModel;
use App\Models\JobDetailModel;
use App\Models\HistoryCancelJobModel;
use App\Models\UserLogin;
use App\Models\DriverModel;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Collection;
use Illuminate\Http\Request;


class JobController extends Controller {

    public function get_job(Request $request)
    {

        $apiKey = $request->query('x-key');
        $dnow = date('Y-m-d');

        $dataLogin = UserLogin::leftJoin('ListUser', 'UserLogin.UserLoginID', '=', 'ListUser.UserLoginID')->where('ApiKey', $apiKey)->first();

        $dataJob = JobModel::leftJoin('Customer', 'ListJob.CustomerID', '=', 'Customer.CustomerID')
            ->where('ListJob.CompanyID', $dataLogin->ListCompanyID)
            ->where('ListJob.JobDate', "<=" ,$dnow)
            ->whereNull('ListJob.UserID')
            ->orderBy('ListJob.JobDate', 'DESC')
            ->get();
        
        // echo json_encode($dataJob);
        // die;

        $return = [];

        foreach ($dataJob as $val) {
            // Default kosong dulu
            $typeJobName = null;

            $createdBy = UserLogin::where("UserLoginID" , $val->CreatedBy)->first('Fullname');

            $val->CreatedBy = $createdBy->Fullname;

            if ($val->TypeJob == "1") {
                $typeJobName = "Line Interrupt";
            } elseif ($val->TypeJob == "2") {
                $typeJobName = "Reconnection";
            } elseif ($val->TypeJob == "3") {
                $typeJobName = "Short Circuit";
            }

            // Tambahkan langsung ke object
            $val->TypeJobName = $typeJobName;
            // $val->JobDate = date('D, d M Y', strtotime($val->JobDate));

            // Push ke hasil
            $return[] = $val;
        }

        return response()->json([
            "Success" => true,
            "Data" => $return
        ]);

    }

    public function get_job_by_user($userID)
    {
        $dataJob = JobModel::with('details') 
        ->leftJoin('Customer', 'ListJob.CustomerID', '=', 'Customer.CustomerID')
        ->where('ListJob.UserID', $userID)
        ->where('ListJob.Status', 2)
        ->orderBy('ListJob.created_at', 'DESC')
        ->get();

        $return = [];

        foreach ($dataJob as $val) {

            // $createdBy = UserLogin::where("UserLoginID" , $val->CreatedBy)->first('Fullname');

            // $val->CreatedBy = $createdBy->Fullname;

            // Push ke hasil
            $return[] = $val;
        }

        


        return response()->json([
            "Success" => true,
            "Data" => $return
        ], 200);
    }

    public function driver_get_job(Request $request)
    {
        $this->validate($request, [
            'user_id' => 'required',
            'job_id' => 'required'
        ]);

       $userID = $request->input('user_id');
       $jobID = $request->input('job_id');

       $apiKey = $request->query('x-key');

        $dataLogin = DriverModel::where('UserID', $userID)->first();

        // cek apakah job ya sudah ada yangambil atau belum
        $cek_data_job = JobModel::where('JobID', $jobID)->first();

        if($cek_data_job->UserID != null && $cek_data_job->Status != null) {

            return response()->json([
                "Success" => false,
                "Message" => "Failed Get Job"
            ], 400);

        }


    //    cek apakah ada job yang masih nyangkut
        $data_all_job_by_user = JobModel::where('UserID', $userID)->where("Status", 1)->get();

        if(count($data_all_job_by_user) == 0) {

            $dataJob = JobModel::where("JobID", $jobID)->first();

            if($dataLogin->ListCompanyID == $dataJob->CompanyID) {
                $dataJob->update([
                    "UserID" => $userID,
                    "AssignWhen" => date('Y-m-d H:i:s'),
                    "Status" => 1
                ]);

                $success = true;
                $message = "Success Driver Get The Job";
                $code = 200;
            } else {

                $success = false;
                $message = "Failed To get Job, because this job not for you";
                $code = 400;

            }

        } else {
            $success = false;
            $message = "Failed to get job, because this driver already has an active job.";
            $code = 400;
        }

        

       return response()->json([
            "Success" => $success,
            "Message" => $message
        ], $code);

    }

    public function get_job_ongoing($user_id)
    {
        $dataJob = JobModel::leftJoin('Customer', 'ListJob.CustomerID', '=', 'Customer.CustomerID')
            ->where("UserID", $user_id)
            ->whereIn('Status', [1, 3])
            ->orderBy('ListJob.created_at', 'DESC')
            ->get();

        $return = [];

        foreach ($dataJob as $val) {
            // Default kosong dulu
            $typeJobName = null;

            $createdBy = UserLogin::where("UserLoginID" , $val->CreatedBy)->first('Fullname');

            $val->CreatedBy = $createdBy->Fullname;

            if ($val->TypeJob == "1") {
                $typeJobName = "Line Interrupt";
            } elseif ($val->TypeJob == "2") {
                $typeJobName = "Reconnection";
            } elseif ($val->TypeJob == "3") {
                $typeJobName = "Short Circuit";
            }

            // Tambahkan langsung ke object
            $val->TypeJobName = $typeJobName;

            // Push ke hasil
            $return[] = $val;
        }

        return response()->json([
            "Success" => true,
            "Data" => $return
        ]);

    }

    public function reschedule_job(Request $request, $jobID)
    { 

        $this->validate($request, [
            'notes' => 'required',
            'new_date' => 'required'
        ]);

        $dataJob = JobModel::where('JobID', $jobID)->first();

        if($request->input('new_date') <= $dataJob->JobDate) {

            return response()->json([
                "Success" => false,
                "Message" => "Request Date must be greater than the current job date."
            ]);
        }

        $dataCreateReschedule = [
            "JobID" => $jobID,
            "CurrentDateJob" => $dataJob->JobDate,
            "RescheduledDateJob" => $request->input('new_date'),
            "Reason" => $request->input('notes'),
            "StatusApproved" => 1,
            "created_at" => date('Y-m-d H:i:s')
        ];

        $createdData = RescheduleJobModel::create($dataCreateReschedule);

        if($createdData) {

            return response()->json([
                "Success" => true,
                "Message" => "Success Request Reschedule Job"
            ]);
        } else {
            return response()->json([
                "Success" => false,
                "Message" => "Failed Request !"
            ]);
        }
    }

    public function finished_job(Request $request)
    {
        $jobId = $request->input('job_id');
        $images = $request->input('images');
        $notes = $request->input('notes');

        if (!$jobId || !$images) {
            return response()->json(['error' => 'job_id and images are required'], 400);
        }

        $savedFiles = [];

        foreach ($images as $index => $base64Image) {
        if (preg_match('/^data:image\/(\w+);base64,/', $base64Image, $type)) {
            $base64Image = substr($base64Image, strpos($base64Image, ',') + 1);
            $ext = strtolower($type[1]); // jpg, png, jpeg
        } else {
            $ext = "png";
        }

        $base64Image = str_replace(' ', '+', $base64Image);
        $imageData = base64_decode($base64Image);

        if ($imageData === false) {
            return response()->json(['error' => 'Invalid base64 image'], 400);
        }

        $fileName = 'job_'.$jobId.'_'.time().'_'.$index.'.'.$ext;
        $filePath = storage_path('app/finished_jobs/'.$fileName);

        // if (!file_exists(dirname($filePath))) {
        //     mkdir(dirname($filePath), 0777, true);
        // }

        file_put_contents($filePath, $imageData);

            $savedFiles[] = $fileName;
        }

        $dataJob = JobModel::where("JobID", $jobId)->first();

        $dataJob->update([
            "Status" => 2,
            "Notes" => $notes,
            "FinishWhen" => date('Y-m-d H:i:s')
        ]);

        foreach ($savedFiles as $val) {

            $fullUrl = url('storage/app/finished_jobs/' . $val);
            
            $data_insert_job_detail = [
                "ListJobID" => $jobId,
                "Photo" => $fullUrl,
                "created_at" => date("Y-m-d H:i:s")
            ];

            JobDetailModel::create($data_insert_job_detail);
        }



        return response()->json([
            'success' => true,
            'message' => 'This Job is finished',
            // 'files'   => $savedFiles
        ]);
    }

    public function cancel_job(Request $request,$jobID)
    {

        $data = JobModel::where("JobID" , $jobID)->first();

        if($data != null) {

            $dataCreateHistory = [
                "JobID" => $data->JobID,
                "UserBefore" => $data->UserID,
                "Reason" => $request->reason,
                "created_at" => date('Y-m-d H:i:s')
            ];

            HistoryCancelJobModel::create($dataCreateHistory);

            $data->update([
                "Status" => null,
                "UserID" => null,
                "AssignWhen" => null,
            ]);

            return response()->json([
                "Success" => true,
                "Message" => "Success Cancel Job"
            ], 200);

        } else {
            return response()->json([
                "Success" => false,
                "Message" => "Job Not Found!!"
            ], 404);
        }


        
    }

}
