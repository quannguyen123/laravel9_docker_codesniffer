<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\BaseController;
use App\Http\Controllers\Controller;
use App\Models\Service;
use App\Services\ServiceService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ServiceController extends BaseController
{
    public function __construct(
        protected ServiceService $serviceService
    ) {
        $this->serviceService = $serviceService;
    }
    
    public function index(Request $request)
    {
        try {
            $serviceAll = $this->serviceService->index($request);

            $res['serviceAll'] = $serviceAll;

            return $this->sendResponse($res, 'Success.');
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage());
        }
    }

    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required|unique:services,name',
                'type' => 'required|numeric',
                'price' => 'required|numeric',
                'used_time' => 'required|numeric',
                'image' => 'nullable|mimes:jpeg,jpg,png,gif|max:5120'
            ]);
    
            if($validator->fails()){
                return $this->sendError('Validation Error.', $validator->errors());
            }
    
            $service = $this->serviceService->store($request);
    
            return $this->sendResponse($service, 'Success.');
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage());
        }
    }

    public function detail(Service $service)
    {
        try {            
            $res['service'] = $service;

            return $this->sendResponse($res, 'Success.');
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage());
        }
    }

    public function update(Request $request, Service $service)
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required|unique:services,name,' . $service['id'],
                'type' => 'required|numeric',
                'price' => 'required|numeric',
                'used_time' => 'required|numeric',
                'image' => 'nullable|mimes:jpeg,jpg,png,gif|max:5120'
            ]);
            
            if($validator->fails()){
                return $this->sendError('Validation Error.', $validator->errors());
            }
            
            $data = $this->serviceService->update($request, $service);

            return $this->sendResponse($data, 'Success.');
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage());
        }
    }

    public function changeStatus(Service $service, $status) {
        try {
            if (!in_array($status, array_keys(config('custom.status')))) {
                return $this->sendError('Status không đúng');
            }

            $data = $this->serviceService->changeStatus($service, $status);
            
            return $this->sendResponse($data, 'Success.');
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage());
        }
    }

    public function destroy(Service $service)
    {
        try {
            $data = $this->serviceService->destroy($service);
            
            return $this->sendResponse($data, 'Success.');
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage());
        }
    }
}
