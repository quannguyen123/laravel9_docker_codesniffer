<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\BaseController;
use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Services\OrderService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class OrderController extends BaseController
{
    public function __construct(
        protected OrderService $orderService
    ) {
        $this->orderService = $orderService;
    }

    public function adminIndex(Request $request) {
        try {
            $validator = Validator::make($request->all(), [
                'filters' => 'nullable|array',
                'filters.company_id' => 'nullable|exists:companies,id',
                'filters.service_id' => 'nullable|exists:services,id',
                'filters.payment_status' => 'nullable|numeric',
            ]);
            
            if($validator->fails()){
                return $this->sendError('Validation Error.', $validator->errors());
            }
            
            [$status, $res['orders'], $mess] = $this->orderService->adminIndex($request);

            return $this->sendResponse($res, $mess);
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage());
        }
    }

    public function adminDetail(Order $order) {
        try {
            [$res['status'], $res['order'], $res['msg']] = $this->orderService->adminDetail($order);

            return $this->sendResponse($res, 'Success.');
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage());
        }
    }
}
