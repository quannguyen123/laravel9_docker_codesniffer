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

    /**
     * @OA\Get(
     *     path="/api/partner/service/index",
     *     summary="Danh sách dịch vụ",
     *     tags={"Partner-Service"},
     *     security={{"bearer":{}}},
     *     @OA\Response(response="200", description="An example endpoint")
     * )
     */
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

    /**
     * @OA\Get(
     *     path="/api/partner/service/detail/{id}",
     *     summary="Thông tin chi tiết service",
     *     tags={"Partner-Service"},
     *     security={{"bearer":{}}},
     *     @OA\Parameter(in="path", name="id", required=true, description="Id vị trí làm việc", @OA\Schema(type="integer")),
     *     @OA\Response(response="200", description="An example endpoint")
     * )
     */
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

    /**
     * @OA\Post(
     *     path="/api/partner/service/add-to-cart",
     *     tags={"Partner-Service"},
     *     summary="Thêm dịch vụ vào giỏ hàng",
     *     description="",
     *     security={{"bearer":{}}},
     *     @OA\RequestBody(
     *          @OA\JsonContent(),
     *          @OA\MediaType(
     *              mediaType="multipart/form-data",
     *              @OA\Schema(
     *                  type="object",
     *                  required={"service_id", "quantity"},
     *                  @OA\Property(property="service_id", type="integer", format="int64"),
     *                  @OA\Property(property="quantity", type="integer", format="int")
     *              )
     *          ),
     *     ),
     *     @OA\Response(response="200", description="An example endpoint")
     * )
     */
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

    /**
     * @OA\Post(
     *     path="/api/partner/service/edit-cart-item",
     *     tags={"Partner-Service"},
     *     summary="Thêm dịch vụ vào giỏ hàng",
     *     description="",
     *     security={{"bearer":{}}},
     *     @OA\RequestBody(
     *          @OA\JsonContent(),
     *          @OA\MediaType(
     *              mediaType="multipart/form-data",
     *              @OA\Schema(
     *                  type="object",
     *                  required={"row_id", "quantity"},
     *                  @OA\Property(property="row_id", type="string", format="string"),
     *                  @OA\Property(property="quantity", type="integer", format="int")
     *              )
     *          ),
     *     ),
     *     @OA\Response(response="200", description="An example endpoint")
     * )
     */
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

    /**
     * @OA\Get(
     *     path="/api/partner/service/cart-info",
     *     summary="Thông tin giỏ hàng",
     *     tags={"Partner-Service"},
     *     security={{"bearer":{}}},
     *     @OA\Response(response="200", description="An example endpoint")
     * )
     */
    public function cartInfo() {
        try {
            $res['cart'] = $this->serviceService->cartInfo();

            return $this->sendResponse($res, 'Success.');
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage());
        }
    }

    /**
     * @OA\Post(
     *     path="/api/partner/service/delete-cart-item",
     *     tags={"Partner-Service"},
     *     summary="Xóa dịch vụ khỏi giỏ hàng",
     *     description="",
     *     security={{"bearer":{}}},
     *     @OA\RequestBody(
     *          @OA\JsonContent(),
     *          @OA\MediaType(
     *              mediaType="multipart/form-data",
     *              @OA\Schema(
     *                  type="object",
     *                  required={"row_id"},
     *                  @OA\Property(property="row_id", type="string", format="string")
     *              )
     *          ),
     *     ),
     *     @OA\Response(response="200", description="An example endpoint")
     * )
     */
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

    /**
     * @OA\Get(
     *     path="/api/partner/service/delete-cart",
     *     summary="Xóa giỏ hàng",
     *     tags={"Partner-Service"},
     *     security={{"bearer":{}}},
     *     @OA\Response(response="200", description="An example endpoint")
     * )
     */
    public function deleteCart() {
        try {
            $this->serviceService->deleteCart();

            return $this->sendResponse([], 'Success.');
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage());
        }
    }

}
