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

    public function detail($serviceId)
    {
        try {         
            [$status, $res['service'], $mess] = $this->serviceService->detail($serviceId);
            
            if ($status) {
                return $this->sendResponse($res, $mess);
            }

            return $this->sendError($mess);
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage());
        }
    }

    public function update(Request $request, $serviceId)
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required|unique:services,name,' . $serviceId,
                'type' => 'required|numeric',
                'price' => 'required|numeric',
                'used_time' => 'required|numeric',
                'image' => 'nullable|mimes:jpeg,jpg,png,gif|max:5120'
            ]);
            
            if($validator->fails()){
                return $this->sendError('Validation Error.', $validator->errors());
            }

            [$status, $res['service'], $mess] = $this->serviceService->update($request, $serviceId);

            if ($status) {
                return $this->sendResponse($res, $mess);
            }

            return $this->sendError($mess);
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage());
        }
    }

    public function changeStatus($serviceId, $status) {
        try {
            if (!in_array($status, array_keys(config('custom.status')))) {
                return $this->sendError('Status không đúng');
            }

            [$status, $res['service'], $mess] = $this->serviceService->changeStatus($serviceId, $status);
            
            if ($status) {
                return $this->sendResponse($res, $mess);
            }

            return $this->sendError($mess);
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage());
        }
    }

    public function destroy($serviceId)
    {
        try {
            [$status, $res['service'], $mess] = $this->serviceService->destroy($serviceId);
            
            if ($status) {
                return $this->sendResponse($res, $mess);
            }

            return $this->sendError($mess);
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage());
        }
    }
}
