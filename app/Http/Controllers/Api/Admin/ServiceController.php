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

    /**
     * @OA\Get(
     *     path="/api/admin/service/index",
     *     summary="Danh sách dịch vụ",
     *     tags={"Admin-Service"},
     *     security={{"bearer":{}}},
     *     @OA\Parameter(in="query", name="search", required=false, description="Dịch vụ cần tìm", @OA\Schema(type="string")),
     *     @OA\Parameter(in="query", name="orderBy", required=false, description="Cột sắp xếp", @OA\Schema(type="string")),
     *     @OA\Parameter(in="query", name="orderType", required=false, description="Loại sắp xếp: DESC or ASC", @OA\Schema(type="string")),
     *     @OA\Response(response="200", description="An example endpoint")
     * )
     */
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

    /**
     * @OA\Post(
     *     path="/api/admin/service/store",
     *     tags={"Admin-Service"},
     *     summary="Thêm dịch vụ",
     *     description="",
     *     security={{"bearer":{}}},
     *     @OA\RequestBody(
     *          @OA\JsonContent(),
     *          @OA\MediaType(
     *              mediaType="multipart/form-data",
     *              @OA\Schema(
     *                  type="object",
     *                  required={"name", "type", "price", "used_time"},
     *                  @OA\Property(property="name", type="string", format="string"),
     *                  @OA\Property(property="type", type="integer", format="int"),
     *                  @OA\Property(property="price", type="integer", format="int"),
     *                  @OA\Property(property="used_time", type="integer", format="int"),
     *                  @OA\Property(property="image", type="string", format="binary"),
     *                  @OA\Property(property="description", type="string", format="string"),
     *                  @OA\Property(property="content", type="string", format="string"),
     *                  @OA\Property(property="note", type="string", format="string")
     *              )
     *          ),
     *     ),
     *     @OA\Response(response="200", description="An example endpoint")
     * )
     */
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

    /**
     * @OA\Get(
     *     path="/api/admin/service/detail/{serviceId}",
     *     summary="Thông tin chi tiết dịch vụ",
     *     tags={"Admin-Service"},
     *     security={{"bearer":{}}},
     *     @OA\Parameter(in="path", name="serviceId", required=true, description="Id dịch vụ", @OA\Schema(type="integer")),
     *     @OA\Response(response="200", description="An example endpoint")
     * )
     */
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

    /**
     * @OA\Post(
     *     path="/api/admin/service/update/{serviceId}",
     *     tags={"Admin-Service"},
     *     summary="Sửa dịch vụ",
     *     description="",
     *     security={{"bearer":{}}},
     *     @OA\Parameter(in="path", name="serviceId", required=true, description="Id dịch vụ", @OA\Schema(type="integer")),
     *     @OA\RequestBody(
     *          @OA\JsonContent(),
     *          @OA\MediaType(
     *              mediaType="multipart/form-data",
     *              @OA\Schema(
     *                  type="object",
     *                  required={"name", "type", "price", "used_time"},
     *                  @OA\Property(property="name", type="string", format="string"),
     *                  @OA\Property(property="type", type="integer", format="int"),
     *                  @OA\Property(property="price", type="integer", format="int"),
     *                  @OA\Property(property="used_time", type="integer", format="int"),
     *                  @OA\Property(property="image", type="string", format="binary"),
     *                  @OA\Property(property="description", type="string", format="string"),
     *                  @OA\Property(property="content", type="string", format="string"),
     *                  @OA\Property(property="note", type="string", format="string")
     *              )
     *          ),
     *     ),
     *     @OA\Response(response="200", description="An example endpoint")
     * )
     */
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

    /**
     * @OA\Get(
     *     path="/api/admin/service/change-status/{serviceId}/{status}",
     *     summary="Thay đổi trạng thái dịch vụ",
     *     tags={"Admin-Service"},
     *     security={{"bearer":{}}},
     *     @OA\Parameter(in="path", name="serviceId", required=true, description="Id dịch vụ", @OA\Schema(type="integer")),
     *     @OA\Parameter(in="path", name="status", required=true, description="trạng thái dịch vụ: lock or active", @OA\Schema(type="string")),
     *     @OA\Response(response="200", description="An example endpoint")
     * )
     */
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

    /**
     * @OA\Delete(
     *     path="/api/admin/service/destroy/{serviceId}",
     *     summary="Xóa dịch vụ",
     *     tags={"Admin-Service"},
     *     security={{"bearer":{}}},
     *     @OA\Parameter(in="path", name="serviceId", required=true, description="Id dịch vụ", @OA\Schema(type="integer")),
     *     @OA\Response(response="200", description="An example endpoint")
     * )
     */
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
