<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\BaseController;
use App\Services\AlertJobService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AlertJobController extends BaseController
{
    public function __construct(
        protected AlertJobService $alertJobService
    ) {
        $this->alertJobService = $alertJobService;
    }
    
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        try {
            [$status, $alertJobAll, $mess] = $this->alertJobService->index($request);

            $res['alertJobAll'] = $alertJobAll;

            return $this->sendResponse($res, $mess);
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
                'position' => 'nullable',
                'salary_min' => 'nullable|numeric',
                'rank' => 'nullable|array',
                'rank.*' => 'nullable|numeric',
                'province' => 'nullable',
                'province.*' => 'nullable|numeric|exists:provinces,id',
                'occupation' => 'nullable|array',
                'occupation.*' => 'nullable|numeric|exists:occupations,id',
                'industry' => 'nullable|array',
                'industry.*' => 'nullable|numeric|exists:industries,id',
                'interval' => 'nullable|numeric',
                'notification_by' => 'nullable|numeric',
                'status' => 'nullable|numeric',
            ]);
    
            if($validator->fails()){
                return $this->sendError('Validation Error.', $validator->errors());
            }
    
            [$status, $alertJob, $mess] = $this->alertJobService->store($request);

            $res['alertJob'] = $alertJob;
    
            return $this->sendResponse($res, $mess);
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
            [$status, $alertJob, $mess] = $this->alertJobService->detail($id);
            $res['alertJob'] = $alertJob;

            if ($status) {
                return $this->sendResponse($res, $mess);
            } else {
                return $this->sendError($mess);
            }
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
                'position' => 'nullable',
                'salary_min' => 'nullable|numeric',
                'rank' => 'nullable|array',
                'rank.*' => 'nullable|numeric',
                'province' => 'nullable',
                'province.*' => 'nullable|numeric|exists:provinces,id',
                'occupation' => 'nullable|array',
                'occupation.*' => 'nullable|numeric|exists:occupations,id',
                'industry' => 'nullable|array',
                'industry.*' => 'nullable|numeric|exists:industries,id',
                'interval' => 'nullable|numeric',
                'notification_by' => 'nullable|numeric',
                'status' => 'nullable|numeric',
            ]);
    
            if($validator->fails()){
                return $this->sendError('Validation Error.', $validator->errors());
            }
            
            [$status, $alertJob, $mess] = $this->alertJobService->update($request, $id);
            $res['alertJob'] = $alertJob;

            if ($status) {
                return $this->sendResponse($res, $mess);
            } else {
                return $this->sendError($mess);
            }
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
            [$status, $mess] = $this->alertJobService->destroy($id);

            if ($status) {
                return $this->sendResponse([], $mess);
            } else {
                return $this->sendError($mess);
            }
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage());
        }
    }
}
