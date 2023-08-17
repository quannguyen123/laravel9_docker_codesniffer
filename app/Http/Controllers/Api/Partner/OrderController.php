<?php

namespace App\Http\Controllers\Api\Partner;

use App\Http\Controllers\BaseController;
use App\Services\OrderService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderController extends BaseController
{
    public function __construct(
        protected OrderService $orderService
    ) {
        $this->orderService = $orderService;
    }

    public function index() {
        try {
            [$status, $res['order'], $mess] = $this->orderService->index();

            if ($status) {
                return $this->sendResponse($res, $mess);
            }

            return $this->sendError($mess);
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage());
        }
    }

    public function store() {
        try {
            [$status, $res['order'], $mess] = $this->orderService->store();

            if ($status) {
                return $this->sendResponse($res, $mess);
            }

            return $this->sendError($mess);
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage());
        }
    }

    public function orderInfo($id) {
        try {
            [$status, $res['order'], $mess] = $this->orderService->orderInfo($id);

            if ($status) {
                return $this->sendResponse($res, $mess);
            }

            return $this->sendError($mess);
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage());
        }
    }

}
