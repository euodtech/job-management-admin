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
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class JobController extends Controller {

    private function getAuthenticatedUser(Request $request)
    {
        $apiKey = $request->header('X-API-Key') ?? $request->input('x-key');
        return UserLogin::leftJoin('ListUser', 'UserLogin.UserLoginID', '=', 'ListUser.UserLoginID')
            ->where('ApiKey', $apiKey)
            ->first();
    }

    public function get_job(Request $request)
    {

        $dnow = date('Y-m-d');

        $dataLogin = $this->getAuthenticatedUser($request);

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

    public function get_job_by_user(Request $request, $userID)
    {
        $authUser = $this->getAuthenticatedUser($request);
        if (!$authUser) {
            return response()->json(["Success" => false, "Message" => "Unauthorized"], 401);
        }

        $targetUser = DriverModel::where('UserID', $userID)->first();
        if (!$targetUser || $targetUser->ListCompanyID !== $authUser->ListCompanyID) {
            return response()->json(["Success" => false, "Message" => "Access denied"], 403);
        }

        $dataJob = JobModel::with('details')
        ->leftJoin('Customer', 'ListJob.CustomerID', '=', 'Customer.CustomerID')
        ->where('ListJob.UserID', $userID)
        ->where('ListJob.CompanyID', $authUser->ListCompanyID)
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

        $dataLogin = DriverModel::where('UserID', $userID)->first();

        DB::beginTransaction();
        try {
            // Lock the job row to prevent concurrent assignment
            $cek_data_job = JobModel::where('JobID', $jobID)->lockForUpdate()->first();

            if (!$cek_data_job) {
                DB::rollBack();
                return response()->json([
                    "Success" => false,
                    "Message" => "Job Not Found"
                ], 404);
            }

            if ($cek_data_job->UserID != null && $cek_data_job->Status != null) {
                DB::rollBack();
                return response()->json([
                    "Success" => false,
                    "Message" => "Failed Get Job"
                ], 400);
            }

            // Check if driver already has an active job (lock to prevent double-assign)
            $data_all_job_by_user = JobModel::where('UserID', $userID)
                ->where("Status", 1)
                ->lockForUpdate()
                ->get();

            if (count($data_all_job_by_user) == 0) {

                if ($dataLogin->ListCompanyID == $cek_data_job->CompanyID) {
                    $cek_data_job->update([
                        "UserID" => $userID,
                        "AssignWhen" => date('Y-m-d H:i:s'),
                        "Status" => 1
                    ]);

                    DB::commit();
                    $success = true;
                    $message = "Success Driver Get The Job";
                    $code = 200;
                } else {
                    DB::rollBack();
                    $success = false;
                    $message = "Failed To get Job, because this job not for you";
                    $code = 400;
                }

            } else {
                DB::rollBack();
                $success = false;
                $message = "Failed to get job, because this driver already has an active job.";
                $code = 400;
            }
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('driver_get_job Error: ' . $e->getMessage());
            return response()->json([
                "Success" => false,
                "Message" => "An error occurred while getting the job"
            ], 500);
        }

       return response()->json([
            "Success" => $success,
            "Message" => $message
        ], $code);

    }

    public function get_job_ongoing(Request $request, $user_id)
    {
        $authUser = $this->getAuthenticatedUser($request);
        if (!$authUser) {
            return response()->json(["Success" => false, "Message" => "Unauthorized"], 401);
        }

        $dataJob = JobModel::leftJoin('Customer', 'ListJob.CustomerID', '=', 'Customer.CustomerID')
            ->where("UserID", $user_id)
            ->where('ListJob.CompanyID', $authUser->ListCompanyID)
            ->whereIn('Status', [1, 3])
            ->orderBy('ListJob.created_at', 'DESC')
            ->get();

        $return = [];
        $today = date('Y-m-d');

        foreach ($dataJob as $val) {
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

            $val->TypeJobName = $typeJobName;

            // Attach latest reschedule info
            $latestReschedule = RescheduleJobModel::where('JobID', $val->JobID)
                ->orderBy('RescheduledID', 'DESC')
                ->first();

            if ($latestReschedule) {
                $val->RescheduleStatus = (int) $latestReschedule->StatusApproved;
                $val->RescheduledDateJob = $latestReschedule->RescheduledDateJob;
                $val->ReasonReject = $latestReschedule->ReasonReject;

                $canFinish = false;
                if ($latestReschedule->StatusApproved == 3) {
                    $canFinish = true;
                } elseif ($latestReschedule->StatusApproved == 2 && $val->JobDate <= $today) {
                    $canFinish = true;
                }
                $val->CanFinish = $canFinish;
            } else {
                $val->RescheduleStatus = null;
                $val->RescheduledDateJob = null;
                $val->ReasonReject = null;
                $val->CanFinish = ($val->Status == 1) ? true : null;
            }

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

        $authUser = $this->getAuthenticatedUser($request);
        if (!$authUser) {
            return response()->json(["Success" => false, "Message" => "Unauthorized"], 401);
        }

        DB::beginTransaction();
        try {
            $dataJob = JobModel::where('JobID', $jobID)->lockForUpdate()->first();

            if (!$dataJob || $dataJob->CompanyID !== $authUser->ListCompanyID) {
                DB::rollBack();
                return response()->json([
                    "Success" => false,
                    "Message" => "Job Not Found"
                ], 404);
            }

            // Job must be ongoing (1) or already rescheduled (3) to request reschedule
            if (!in_array($dataJob->Status, [1, 3])) {
                DB::rollBack();
                return response()->json([
                    "Success" => false,
                    "Message" => "Job is not in a valid state for rescheduling"
                ]);
            }

            if ($request->input('new_date') <= $dataJob->JobDate) {
                DB::rollBack();
                return response()->json([
                    "Success" => false,
                    "Message" => "Request Date must be greater than the current job date."
                ]);
            }

            // Block if there's already a pending reschedule for this job
            $pendingReschedule = RescheduleJobModel::where('JobID', $jobID)
                ->where('StatusApproved', 1)
                ->first();

            if ($pendingReschedule) {
                DB::rollBack();
                return response()->json([
                    "Success" => false,
                    "Message" => "A reschedule request is already pending approval."
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

            if ($createdData) {
                // Move job to Status 3 so the driver can accept other jobs
                $dataJob->update(["Status" => 3]);

                DB::commit();
                return response()->json([
                    "Success" => true,
                    "Message" => "Success Request Reschedule Job",
                    "Data" => ["RescheduledID" => $createdData->RescheduledID]
                ]);
            } else {
                DB::rollBack();
                return response()->json([
                    "Success" => false,
                    "Message" => "Failed Request !"
                ]);
            }
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('reschedule_job Error: ' . $e->getMessage());
            return response()->json([
                "Success" => false,
                "Message" => "An error occurred while rescheduling the job"
            ], 500);
        }
    }

    // Kian: Bug fix: Not submitting when trying to finish the job.
    public function finished_job(Request $request)
    {
        // Read JSON input (Flutter sends application/json)
        $inputJSON = file_get_contents('php://input');
        $input = json_decode($inputJSON, true);

        $jobId  = isset($input['job_id']) ? (int) $input['job_id'] : 0;
        $images = isset($input['images']) ? $input['images'] : [];
        $notes  = isset($input['notes']) ? $input['notes'] : '';

        // Ensure images is an array
        if (!is_array($images)) {
            $images = [$images];
        }

        // Validate required data
        if ($jobId <= 0 || empty($images)) {
            return response()->json([
                'Success' => false,
                'Message' => 'job_id and images are required'
            ], 400);
        }

        $savedFiles = [];

        foreach ($images as $index => $base64Image) {
            try {
                // Remove data URI prefix if present
                if (preg_match('/^data:image\/(\w+);base64,/', $base64Image, $type)) {
                    $base64Image = substr($base64Image, strpos($base64Image, ',') + 1);
                    $ext = strtolower($type[1]);
                } else {
                    $ext = "png";
                }

                // Clean the base64 string
                $base64Image = str_replace(' ', '+', $base64Image);
                $base64Image = preg_replace('/[^A-Za-z0-9\+\/=]/', '', $base64Image);

                // Decode
                $imageData = base64_decode($base64Image, true);

                if ($imageData === false || empty($imageData)) {
                    \Log::error("Invalid base64 image at index $index");
                    return response()->json([
                        'Success' => false,
                        'Message' => "Invalid base64 image at position " . ($index + 1)
                    ], 400);
                }

                // Validate it's actually an image
                $finfo = new \finfo(FILEINFO_MIME_TYPE);
                $mimeType = $finfo->buffer($imageData);

                if (!in_array($mimeType, ['image/jpeg', 'image/png', 'image/jpg'])) {
                    return response()->json([
                        'Success' => false,
                        'Message' => "File at position " . ($index + 1) . " is not a valid image"
                    ], 400);
                }

                // Use uniqid to prevent filename collision under concurrent requests
                $fileName = 'job_'.$jobId.'_'.uniqid('', true).'_'.$index.'.'.$ext;
                $filePath = storage_path('app/finished_jobs/'.$fileName);

                // Ensure directory exists (suppress error if concurrent request creates it)
                $directory = dirname($filePath);
                if (!is_dir($directory)) {
                    @mkdir($directory, 0755, true);
                }

                file_put_contents($filePath, $imageData, LOCK_EX);
                $savedFiles[] = $fileName;

            } catch (\Exception $e) {
                \Log::error("Error processing image $index: " . $e->getMessage());
                return response()->json([
                    'Success' => false,
                    'Message' => "Error processing image at position " . ($index + 1) . ": " . $e->getMessage()
                ], 400);
            }
        }

        $authUser = $this->getAuthenticatedUser($request);
        if (!$authUser) {
            return response()->json(['Success' => false, 'Message' => 'Unauthorized'], 401);
        }

        DB::beginTransaction();
        try {
            $dataJob = JobModel::where("JobID", $jobId)->lockForUpdate()->first();

            if (!$dataJob || $dataJob->CompanyID !== $authUser->ListCompanyID) {
                DB::rollBack();
                return response()->json([
                    'Success' => false,
                    'Message' => 'Job not found'
                ], 404);
            }

            // Status validation
            if ($dataJob->Status === null) {
                DB::rollBack();
                return response()->json([
                    'Success' => false,
                    'Message' => 'Job is not assigned'
                ], 400);
            }

            if ($dataJob->Status == 2) {
                DB::rollBack();
                return response()->json([
                    'Success' => false,
                    'Message' => 'Job is already finished'
                ], 400);
            }

            // Status 3: check reschedule conditions
            if ($dataJob->Status == 3) {
                $latestReschedule = RescheduleJobModel::where('JobID', $jobId)
                    ->orderBy('RescheduledID', 'DESC')
                    ->first();

                if ($latestReschedule) {
                    if ($latestReschedule->StatusApproved == 1) {
                        DB::rollBack();
                        return response()->json([
                            'Success' => false,
                            'Message' => 'Reschedule is pending admin approval'
                        ], 400);
                    }

                    if ($latestReschedule->StatusApproved == 2 && $dataJob->JobDate > date('Y-m-d')) {
                        DB::rollBack();
                        return response()->json([
                            'Success' => false,
                            'Message' => 'Cannot finish until ' . date('d M Y', strtotime($dataJob->JobDate))
                        ], 400);
                    }
                }
                // StatusApproved == 3 (rejected): allow finish
                // StatusApproved == 2 AND JobDate <= today: allow finish
            }

            // Status 1 or validated Status 3: allow finish
            $dataJob->update([
                "Status" => 2,
                "Notes" => $notes,
                "FinishWhen" => date('Y-m-d H:i:s')
            ]);

            foreach ($savedFiles as $val) {
                $fullUrl = 'storage/app/finished_jobs/' . $val;

                $data_insert_job_detail = [
                    "ListJobID" => $jobId,
                    "Photo" => $fullUrl,
                    "created_at" => date("Y-m-d H:i:s")
                ];

                JobDetailModel::create($data_insert_job_detail);
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('finished_job Error: ' . $e->getMessage());
            return response()->json([
                'Success' => false,
                'Message' => 'An error occurred while finishing the job'
            ], 500);
        }

        return response()->json([
            'Success' => true,
            'Message' => 'This Job is finished',
        ]);
    }

    public function reschedule_status(Request $request, $jobID)
    {
        $authUser = $this->getAuthenticatedUser($request);
        if (!$authUser) {
            return response()->json(["Success" => false, "Message" => "Unauthorized"], 401);
        }

        $dataJob = JobModel::where('JobID', $jobID)
            ->where('CompanyID', $authUser->ListCompanyID)
            ->first();

        if (!$dataJob) {
            return response()->json([
                "Success" => false,
                "Message" => "Job Not Found"
            ], 404);
        }

        $latestReschedule = RescheduleJobModel::where('JobID', $jobID)
            ->orderBy('RescheduledID', 'DESC')
            ->first();

        if (!$latestReschedule) {
            return response()->json([
                "Success" => true,
                "Data" => null
            ]);
        }

        $statusLabels = [1 => 'pending', 2 => 'approved', 3 => 'rejected'];
        $canFinish = false;

        if ($latestReschedule->StatusApproved == 3) {
            $canFinish = true;
        } elseif ($latestReschedule->StatusApproved == 2 && $dataJob->JobDate <= date('Y-m-d')) {
            $canFinish = true;
        }

        return response()->json([
            "Success" => true,
            "Data" => [
                "RescheduledID" => $latestReschedule->RescheduledID,
                "StatusApproved" => (int) $latestReschedule->StatusApproved,
                "StatusLabel" => $statusLabels[$latestReschedule->StatusApproved] ?? 'unknown',
                "RescheduledDateJob" => $latestReschedule->RescheduledDateJob,
                "ReasonReject" => $latestReschedule->ReasonReject,
                "CanFinish" => $canFinish
            ]
        ]);
    }

    public function cancel_job(Request $request, $jobID)
    {
        $authUser = $this->getAuthenticatedUser($request);
        if (!$authUser) {
            return response()->json(["Success" => false, "Message" => "Unauthorized"], 401);
        }

        DB::beginTransaction();
        try {
            $data = JobModel::where("JobID", $jobID)->lockForUpdate()->first();

            if ($data != null && $data->CompanyID !== $authUser->ListCompanyID) {
                $data = null;
            }

            if ($data != null) {

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

                DB::commit();

                return response()->json([
                    "Success" => true,
                    "Message" => "Success Cancel Job"
                ], 200);

            } else {
                DB::rollBack();
                return response()->json([
                    "Success" => false,
                    "Message" => "Job Not Found!!"
                ], 404);
            }
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('cancel_job Error: ' . $e->getMessage());
            return response()->json([
                "Success" => false,
                "Message" => "An error occurred while cancelling the job"
            ], 500);
        }
    }

}
