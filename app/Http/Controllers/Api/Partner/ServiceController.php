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
            [$status, $serviceAll, $mess] = $this->serviceService->list();

            if ($status) {
                $res['serviceAll'] = $serviceAll;
                return $this->sendResponse($res, 'Success.');
            }

            return $this->sendError($mess);
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage());
        }
    }

    public function detail($serviceId) {
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

    public function addToCart(Request $request) {
        try {
            $validator = Validator::make($request->all(), [
                'service_id' => 'required|exists:services,id',
                'quantity' => 'required|numeric|min:1',
            ]);

            if($validator->fails()){
                return $this->sendError('Validation Error.', $validator->errors());
            }
    
            [$status, $res['cart'], $mess] = $this->serviceService->addToCart($request);
    
            if ($status) {
                return $this->sendResponse($res, $mess);
            }

            return $this->sendError($mess);

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
    
            [$status, $res['cart'], $mess] = $this->serviceService->editCartItem($request);

            if ($status) {
                return $this->sendResponse($res, 'Success.');
            }

            return $this->sendError($mess);
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
