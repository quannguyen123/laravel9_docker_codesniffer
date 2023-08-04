<?php

namespace App\Http\Controllers\Api\Partner;

use App\Http\Controllers\BaseController;
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

    public function list() {
        try {
            $serviceAll = $this->serviceService->list();

            $res['serviceAll'] = $serviceAll;

            return $this->sendResponse($res, 'Success.');
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage());
        }
    }

    public function detail(Service $service) {
        try {
            $res['service'] = $service;

            return $this->sendResponse($res, 'Success.');
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage());
        }
    }

    public function addToCart(Request $request) {
        try {
            $validator = Validator::make($request->all(), [
                'service_id' => 'required|exists:services,id',
                'quantity' => 'required|numeric|min:1',
            ]);

            if($validator->fails()){
                return $this->sendError('Validation Error.', $validator->errors());
            }
    
            $res['cart'] = $this->serviceService->addToCart($request);
    
            return $this->sendResponse($res, 'Success.');
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage());
        }
    }

    public function editCartItem(Request $request) {
        try {
            $validator = Validator::make($request->all(), [
                'row_id' => 'required',
                'quantity' => 'required|numeric|min:1',
            ]);

            if($validator->fails()){
                return $this->sendError('Validation Error.', $validator->errors());
            }
    
            $res['cart'] = $this->serviceService->editCartItem($request);

            return $this->sendResponse($res, 'Success.');
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage());
        }
    }

    public function cartInfo() {
        try {
            $res['cart'] = $this->serviceService->cartInfo();

            return $this->sendResponse($res, 'Success.');
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage());
        }
    }

    public function deleteCartItem(Request $request) {
        try {
            $validator = Validator::make($request->all(), [
                'row_id' => 'required'
            ]);

            if($validator->fails()){
                return $this->sendError('Validation Error.', $validator->errors());
            }
    
            $res['cart'] = $this->serviceService->deleteCartItem($request);

            return $this->sendResponse($res, 'Success.');
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage());
        }
    }

    public function deleteCart() {
        try {
            $this->serviceService->deleteCart();

            return $this->sendResponse([], 'Success.');
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage());
        }
    }

}
