<?php

namespace App\Http\Controllers\Api\Partner;

use App\Http\Controllers\BaseController;
use App\Http\Controllers\Controller;
use App\Models\Job;
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
    
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'status' => 'required'
            ]);
    
            if($validator->fails()){
                return $this->sendError('Validation Error.', $validator->errors());
            }
            
            $status = $request->status;
            if (!in_array($status, array_keys(config('custom.job-status')))) {
                return $this->sendError('Status không đúng');
            }
            $jobAll = $this->jobService->index($request, $status);

            $res['jobAll'] = $jobAll;

            return $this->sendResponse($res, 'Success.');
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage());
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'job_title' => 'required',
                'rank' => 'required|numeric',
                'job_type' => 'nullable|numeric',
                'description' => 'required',
                'job_require' => 'required',
                'salary_min' => 'required|numeric',
                'salary_max' => 'required|numeric',
                'show_salary' => 'nullable|boolean',
                'introducing_letter' => 'nullable|boolean',
                'language_cv' => 'nullable|numeric',
                'recipients_of_cv' => 'required',
                'show_recipients_of_cv' => 'nullable|boolean',
                'email_recipients_of_cv' => 'email|required',
                'post_anonymously' => 'nullable|boolean',

                'tag_ids' => 'required|array',
                'tag_ids.*' => 'numeric|exists:tags,id',
                'occupation_ids' => 'required|array',
                'occupation_ids.*' => 'numeric|exists:occupations,id',
                'company_location_ids' => 'required|array',
                'company_location_ids.*' => 'numeric|exists:company_location,id',
                'is_draft' => 'nullable|boolean'
            ]);
    
            if($validator->fails()){
                return $this->sendError('Validation Error.', $validator->errors());
            }
    
            $job = $this->jobService->store($request);
    
            return $this->sendResponse($job, 'Success.');
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function detail($id)
    {
        try {
            $res['job'] = $this->jobService->detail($id);

            return $this->sendResponse($res, 'Success.');
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage());
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        try {
            $validator = Validator::make($request->all(), [
                'job_title' => 'required',
                'rank' => 'required|numeric',
                'job_type' => 'nullable|numeric',
                'description' => 'required',
                'job_require' => 'required',
                'salary_min' => 'required|numeric',
                'salary_max' => 'required|numeric',
                'show_salary' => 'nullable|boolean',
                'introducing_letter' => 'nullable|boolean',
                'language_cv' => 'nullable|numeric',
                'recipients_of_cv' => 'required',
                'show_recipients_of_cv' => 'nullable|boolean',
                'email_recipients_of_cv' => 'email|required',
                'post_anonymously' => 'nullable|boolean',

                'tag_ids' => 'required|array',
                'tag_ids.*' => 'numeric|exists:tags,id',
                'occupation_ids' => 'required|array',
                'occupation_ids.*' => 'numeric|exists:occupations,id',
                'company_location_ids' => 'required|array',
                'company_location_ids.*' => 'numeric|exists:company_location,id',
                'is_draft' => 'nullable|boolean'
            ]);
    
            if($validator->fails()){
                return $this->sendError('Validation Error.', $validator->errors());
            }
            
            // return $request->all();
            [$status, $data, $mess] = $this->jobService->update($request, $id);

            return $this->sendResponse($data, $mess);
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            [$status, $mess] = $this->jobService->destroy($id);
            
            return $this->sendResponse($status, $mess);
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage());
        }
    }

    public function changeStatus(Request $request, $id) {
        try {
            $validator = Validator::make($request->all(), [
                'status' => 'required'
            ]);
    
            if($validator->fails()){
                return $this->sendError('Validation Error.', $validator->errors());
            }

            $status = $request['status'];
            if (!in_array($status, array_keys(config('custom.job-status')))) {
                return $this->sendError('Status không đúng');
            }

            [$code, $res['job'], $mess] = $this->jobService->changeStatus($id, $status);
            
            return $this->sendResponse($res, $mess);
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage());
        }
    }
}
