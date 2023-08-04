<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\BaseController;
use App\Services\JobService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class JobController extends BaseController
{
    public function __construct(
        protected JobService $jobService
    ) {
        $this->jobService = $jobService;
    }

    public function index(Request $request) {
        try {
            $validator = Validator::make($request->all(), [
                'occupation_ids' => 'nullable|exists:occupations,id',
                'location_id' => 'nullable|exists:provinces,id',
                'welfare_ids' => 'nullable|exists:welfares,id',
                'search' => 'nullable',
                'rank' => 'nullable',
                'job_type' => 'nullable',
                'salary_type' => 'nullable',
                'urgent' => 'nullable',
            ]);

            if($validator->fails()){
                return $this->sendError('Validation Error.', $validator->errors());
            }

            [$status, $jobs, $mess] = $this->jobService->searchJob($request);

            $res['jobs'] = $jobs;

            return $this->sendResponse($jobs, $mess);
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage());
        }
    }

    public function detail($id) {
        try {
            [$status, $job, $mess] = $this->jobService->jobDetail($id);

            $res['job'] = $job;

            return $this->sendResponse($res, $mess);
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage());
        }
    }

    public function applyJob(Request $request, $id) {
        try {
            // TODO:: send mail cho user và partner khi có job apply
            $validator = Validator::make($request->all(), [
                'position' => 'required',
                'number_phone' => 'required',
                'file_cv' => 'nullable|mimes:pdf|max:5120'
            ]);

            if($validator->fails()){
                return $this->sendError('Validation Error.', $validator->errors());
            }

            [$status, $job, $mess] = $this->jobService->applyJob($request, $id);

            $res['job'] = $job;

            return $this->sendResponse($res, $mess);
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage());
        }
    }

    public function jobFavourite($id) {
        try {
            [$status, $job, $mess] = $this->jobService->jobFavourite($id);

            $res['job'] = $job;

            return $this->sendResponse($res, $mess);
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage());
        }
    }
    
    
}
