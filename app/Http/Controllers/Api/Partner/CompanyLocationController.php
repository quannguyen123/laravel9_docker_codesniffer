<?php

namespace App\Http\Controllers\Api\Partner;

use App\Http\Controllers\BaseController;
use App\Http\Controllers\Controller;
use App\Services\CompanyLocationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CompanyLocationController extends BaseController
{
    public function __construct(
        protected CompanyLocationService $companyLocationService
    ) {
        $this->companyLocationService = $companyLocationService;
    }
    
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        try {
            [$status, $companyLocationAll, $mess] = $this->companyLocationService->index($request);

            if ($status) {
                $res['companyLocationAll'] = $companyLocationAll;

                return $this->sendResponse($res, $mess);
            }

            return $this->sendResponse([], $mess);
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
                'name' => 'required|unique:company_location,name',
                'address' => 'required',
                'province_id' => 'required|exists:provinces,id',
            ]);
    
            if($validator->fails()){
                return $this->sendError('Validation Error.', $validator->errors());
            }
    
            [$status, $companyLocation, $mess] = $this->companyLocationService->store($request);

            if ($status) {
                $res['companyLocation'] = $companyLocation;

                return $this->sendResponse($res, $mess);
            }

            return $this->sendResponse([], $mess);
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
            [$status, $companyLocation, $mess] = $this->companyLocationService->detail($id);

            if ($status) {
                $res['companyLocation'] = $companyLocation;

                return $this->sendResponse($res, $mess);
            }

            return $this->sendResponse([], $mess);
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
                'name' => 'required|unique:company_location,name,' . $id,
                'address' => 'required',
                'province_id' => 'required|exists:provinces,id',
            ]);
            
            if($validator->fails()){
                return $this->sendError('Validation Error.', $validator->errors());
            }
            
            [$status, $companyLocation, $mess] = $this->companyLocationService->update($request, $id);

            if ($status) {
                $res['companyLocation'] = $companyLocation;

                return $this->sendResponse($res, $mess);
            }

            return $this->sendResponse([], $mess);
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
            [$status, $mess] = $this->companyLocationService->destroy($id);

            return [$status, $mess];
            if ($status) {
                return $this->sendResponse($status, $mess);
            }
            
            return $this->sendResponse($status, $mess);
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage());
        }
    }
}
