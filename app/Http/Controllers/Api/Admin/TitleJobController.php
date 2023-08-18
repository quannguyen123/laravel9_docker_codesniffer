<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\BaseController;
use App\Models\JobTitle;
use App\Services\JobTitleService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TitleJobController extends BaseController
{
    public function __construct(
        protected JobTitleService $jobTitleService
    ) {
        $this->jobTitleService = $jobTitleService;
    }
    
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        try {
            $jobTitleAll = $this->jobTitleService->index($request);

            $res['jobTitleAll'] = $jobTitleAll;

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
                'name' => 'required|unique:job_titles,name',
            ]);
    
            if($validator->fails()){
                return $this->sendError('Validation Error.', $validator->errors());
            }
    
            $jobTitle = $this->jobTitleService->store($request);
    
            return $this->sendResponse($jobTitle, 'Success.');
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function detail($job_title_id)
    {
        try {            
            [$status, $res['jobTitle'], $mess] = $this->jobTitleService->detail($job_title_id);

            if ($status) {
                return $this->sendResponse($res, $mess);
            }

            return $this->sendError($mess);
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
    public function update(Request $request, $job_title_id)
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required|unique:job_titles,name,' . $job_title_id,
                'icon' => 'nullable|mimes:jpeg,jpg,png,gif|max:5120'
            ]);
            
            if($validator->fails()){
                return $this->sendError('Validation Error.', $validator->errors());
            }
            
            [$status, $res['jobTitle'], $mess] = $this->jobTitleService->update($request, $job_title_id);

            if ($status) {
                return $this->sendResponse($res, $mess);
            }

            return $this->sendError($mess);
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage());
        }
    }

    public function changeStatus($job_title_id, $status) {
        try {
            if (!in_array($status, array_keys(config('custom.status')))) {
                return $this->sendError('Status không đúng');
            }

            [$status, $res['jobTitle'], $mess] = $this->jobTitleService->changeStatus($job_title_id, $status);

            if ($status) {
                return $this->sendResponse($res, $mess);
            }

            return $this->sendError($mess);
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
    public function destroy($job_title_id)
    {
        try {
            [$status, $res['jobTitle'], $mess] = $this->jobTitleService->destroy($job_title_id);

            if ($status) {
                return $this->sendResponse($res, $mess);
            }

            return $this->sendError($mess);
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage());
        }
    }
}
