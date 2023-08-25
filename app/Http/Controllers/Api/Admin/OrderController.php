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

    /**
     * @OA\Get(
     *     path="/api/admin/order/index",
     *     summary="Danh sách đơn hàng",
     *     tags={"Admin-Order"},
     *     security={{"bearer":{}}},
     *     @OA\Parameter(in="query", name="filters['company_id']", required=false, description="Id công ty", @OA\Schema(type="integer")),
     *     @OA\Parameter(in="query", name="filters['service_id']", required=false, description="Id dịch vụ", @OA\Schema(type="integer")),
     *     @OA\Parameter(in="query", name="filters['payment_status']", required=false, description="Trạng thái thanh toán", @OA\Schema(type="integer")),
     *     @OA\Parameter(in="query", name="orderBy", required=false, description="Cột sắp xếp", @OA\Schema(type="string")),
     *     @OA\Parameter(in="query", name="orderType", required=false, description="Loại sắp xếp: DESC or ASC", @OA\Schema(type="string")),
     *     @OA\Response(response="200", description="An example endpoint")
     * )
     */
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

    /**
     * @OA\Get(
     *     path="/api/admin/order/{order}",
     *     summary="Chi tiết đơn hàng",
     *     tags={"Admin-Order"},
     *     security={{"bearer":{}}},
     *     @OA\Parameter(in="path", name="order", required=false, description="Id đơn hàng", @OA\Schema(type="integer")),
     *     @OA\Response(response="200", description="An example endpoint")
     * )
     */
    public function adminDetail(Order $order) {
        try {
            [$res['status'], $res['order'], $res['msg']] = $this->orderService->adminDetail($order);

            return $this->sendResponse($res, 'Success.');
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage());
        }
    }
}
