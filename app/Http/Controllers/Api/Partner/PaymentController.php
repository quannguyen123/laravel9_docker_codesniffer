<?php

namespace App\Http\Controllers\Api\Partner;

use App\Http\Controllers\BaseController;
use App\Models\Order;
use App\Services\PaymentService;
use Illuminate\Http\Request;

class PaymentController extends BaseController
{
    public function __construct(
        protected readonly PaymentService $paymentService
    ) {
    }

    public function pay($orderId) {
        try {
            [$status, $mess, $vnpUrl] = $this->paymentService->pay($orderId);

            if (!$status) {
                return $this->sendError($mess);
            }

            $res['vnpUrl'] = $vnpUrl;

            return $this->sendResponse($res, $mess);
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage());
        }
    }

    public function paymentReturn(Request $request) {
        try {
            [$status, $mess] = $this->paymentService->paymentReturn($request);

            if ($status) {
                return $this->sendResponse([], $mess);
            }

            return $this->sendError($mess);
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage());
        }
    }

    public function callback(Request $request) {
        try {
            $data = $this->paymentService->callback();

            $res['data'] = $data;

            return $this->sendResponse($res, 'Success.');
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage());
        }
    }
}
