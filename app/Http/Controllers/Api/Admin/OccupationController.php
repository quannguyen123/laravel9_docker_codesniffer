<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\BaseController;
use App\Models\Occupation;
use App\Services\OccupationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class OccupationController extends BaseController
{
    public function __construct(
        protected OccupationService $occupationService
    ) {
        $this->occupationService = $occupationService;
    }
    
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        try {
            $occupationAll = $this->occupationService->index($request);

            $res['occupationAll'] = $occupationAll;

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
                'name' => 'required|unique:occupations,name',
                'slug' => 'nullable|unique:occupations,slug',
            ]);
    
            if($validator->fails()){
                return $this->sendError('Validation Error.', $validator->errors());
            }
    
            $occupation = $this->occupationService->store($request);
    
            return $this->sendResponse($occupation, 'Success.');
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
    public function detail($occupationId)
    {
        try {      
            [$status, $res['occupation'], $mess] = $this->occupationService->detail($occupationId);

            if ($status) {

                return $this->sendResponse($res, 'Success.');
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
    public function update(Request $request, $occupationId)
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required|unique:occupations,name,' . $occupationId,
                'slug' => 'nullable|required|unique:occupations,slug,' . $occupationId,
            ]);
            
            if($validator->fails()){
                return $this->sendError('Validation Error.', $validator->errors());
            }
            
            [$status, $res, $mess] = $this->occupationService->update($request, $occupationId);

            if ($status) {
                return $this->sendResponse($res, $mess);
            }

            return $this->sendError($mess);
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage());
        }
    }

    public function changeStatus($occupationId, $status) {
        try {
            if (!in_array($status, array_keys(config('custom.status')))) {
                return $this->sendError('Status không đúng');
            }

            [$status, $res, $mess] = $this->occupationService->changeStatus($occupationId, $status);
            
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
    public function destroy($occupationId)
    {
        try {
            [$status, $res, $mess] = $this->occupationService->destroy($occupationId);
            
            if ($status) {
                return $this->sendResponse($res, $mess);
            }

            return $this->sendError($mess);
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage());
        }
    }
}
