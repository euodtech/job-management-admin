<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use App\Models\JobModel;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Collection;
use Illuminate\Http\Request;


class DapaController extends Controller
{

	public function farhan()
	{
		$test = JobModel::where('status', 2)->get();

		return response()->json([
			"Success" => true,
			"Message" => "Job berhasil ditambahkan",
			"Data" => $test
		]);
	}


	public function InsertJob(Request $request)
	{
		$this->validate($request, [
			'job_name' => 'required',
			'customer_id' => 'required',
			'type_job' => 'required',
			'created_by' => 'required',
			'job_date' => 'required',
			'created_by' => 'required',
			'assign_when' => 'required'
		]);

		$JobName = $request->input('job_name');
		$customerID = $request->input('customer_id');
		$typeJob = $request->input('type_job');
		$createdBy = $request->input('created_by');
		$jobDate = $request->input('job_date');
		$createdAt = $request->input('created_at');
		$assignWhen = $request->input('assign_when');

		$job = JobModel::create([
			"JobName"    => $JobName,
			"CustomerID" => $customerID,
			"TypeJob"    => $typeJob,
			"CreatedBy"  => $createdBy,
			"JobDate"    => $jobDate,
			"created_at" => $createdAt,
			"AssignWhen" => $assignWhen,
		]);

		return response()->json([
			"Success" => true,
			"Message" => "Job berhasil ditambahkan",
			"Data" => $job
		], 201);
	}

	public function DeleteJob($id)
	{

		$job = JobModel::where('JobID', $id)->delete();

		if ($job) {
			return response()->json([
				"Success" => true,
				"Message" => "Job berhasil dihapus",
				"Data" => $job
			], 200);
		} else {
			return response()->json([
				"Success" => false,
				"Message" => "Job tidak ditemukan",
				"Data" => false
			], 404);
		}
	}

	public function UpdateJob(Request $request, $id)
	{
		$job = JobModel::find($id);

		if (!$job) {
			return response()->json([
				"Success" => false,
				"Message" => "Data dengan ID $id tidak ditemukan",
				"Data"    => null
			], 404);
		}

		$job->update($request->only(['JobName', 'UserID', 'TypeJob', 'CustomerID', 'CreatedBy']));

		return response()->json([
			"Success" => true,
			"Message" => "Data berhasil diupdate",
			"Data"    => $job
		], 200);
	}
}
