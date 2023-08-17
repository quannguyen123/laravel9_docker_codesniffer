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
            $res['order'] = $this->orderService->index();

            return $this->sendResponse($res, 'Success.');
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage());
        }
    }

    public function store() {
        try {
            [$status, $res['order'], $mess] = $this->orderService->store();

            return $this->sendResponse($res, $mess);
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage());
        }
    }

    public function orderInfo($id) {
        try {
            $res['order'] = $this->orderService->orderInfo($id);

            return $this->sendResponse($res, 'Success.');
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage());
        }
    }

}
