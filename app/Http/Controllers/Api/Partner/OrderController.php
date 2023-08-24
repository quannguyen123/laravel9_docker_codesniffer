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
    
    /**
     * @OA\Get(
     *     path="/api/partner/order/index",
     *     summary="Danh sách đơn hàng",
     *     tags={"Partner-Order"},
     *     security={{"bearer":{}}},
     *     @OA\Response(response="200", description="An example endpoint")
     * )
     */
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

    /**
     * @OA\Get(
     *     path="/api/partner/order/store",
     *     summary="Đặt hàng",
     *     tags={"Partner-Order"},
     *     security={{"bearer":{}}},
     *     @OA\Response(response="200", description="An example endpoint")
     * )
     */
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

    /**
     * @OA\Get(
     *     path="/api/partner/order/detail/{id}",
     *     summary="Thông tin chi tiết service",
     *     tags={"Partner-Order"},
     *     security={{"bearer":{}}},
     *     @OA\Parameter(in="path", name="id", required=true, description="Id vị trí làm việc", @OA\Schema(type="integer")),
     *     @OA\Response(response="200", description="An example endpoint")
     * )
     */
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
