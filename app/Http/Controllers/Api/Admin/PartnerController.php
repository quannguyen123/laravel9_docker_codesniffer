<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\BaseController;
use App\Services\PartnerService;
use Illuminate\Http\Request;

class PartnerController extends BaseController
{
    public function __construct(
        protected PartnerService $partnerService
    ) {
        $this->partnerService = $partnerService;
    }
    
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    /**
     * @OA\Get(
     *     path="/api/admin/partner/index",
     *     summary="Danh sách partner",
     *     tags={"Admin-Managerment Partner"},
     *     security={{"bearer":{}}},
     *     @OA\Parameter(in="query", name="search", required=false, description="Tên partner", @OA\Schema(type="string")),
     *     @OA\Parameter(in="query", name="orderBy", required=false, description="Cột sắp xếp", @OA\Schema(type="string")),
     *     @OA\Parameter(in="query", name="orderType", required=false, description="Loại sắp xếp: DESC or ASC", @OA\Schema(type="string")),
     *     @OA\Response(response="200", description="An example endpoint")
     * )
     */
    public function index(Request $request)
    {
        try {
            $userAll = $this->partnerService->index($request);

            $res['userAll'] = $userAll;

            return $this->sendResponse($res, 'Success.');
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     *
     * @return \Illuminate\Http\Response
     */
    /**
     * @OA\Get(
     *     path="/api/admin/partner/detail/{id}",
     *     summary="Thông tin chi tiết partner",
     *     tags={"Admin-Managerment Partner"},
     *     security={{"bearer":{}}},
     *     @OA\Parameter(in="path", name="id", required=true, description="Id partner", @OA\Schema(type="integer")),
     *     @OA\Response(response="200", description="An example endpoint")
     * )
     */
    public function detail($id)
    {
        try {
            [$status, $res['partner'], $mess] = $this->partnerService->detail($id);
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
     *     path="/api/admin/partner/change-status/{id}/{status}",
     *     summary="Thay đổi trạng thái partner",
     *     tags={"Admin-Managerment Partner"},
     *     security={{"bearer":{}}},
     *     @OA\Parameter(in="path", name="id", required=true, description="Id partner", @OA\Schema(type="integer")),
     *     @OA\Parameter(in="path", name="status", required=true, description="trạng thái partner: lock or active", @OA\Schema(type="string")),
     *     @OA\Response(response="200", description="An example endpoint")
     * )
     */
    public function changeStatus($id, $status) {
        try {
            if (!in_array($status, array_keys(config('custom.status')))) {
                return $this->sendError('Status không đúng');
            }

            [$status, $res['partner'], $mess] = $this->partnerService->changeStatus($id, $status);
            if ($status) {
                return $this->sendResponse($res, $mess);
            }

            return $this->sendError($mess);
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    /**
     * @OA\Delete(
     *     path="/api/admin/partner/destroy/{id}",
     *     summary="Xóa partner",
     *     tags={"Admin-Managerment Partner"},
     *     security={{"bearer":{}}},
     *     @OA\Parameter(in="path", name="id", required=true, description="Id partner", @OA\Schema(type="integer")),
     *     @OA\Response(response="200", description="An example endpoint")
     * )
     */
    public function destroy($id)
    {
        try {
            [$status, $res['partner'], $mess] = $this->partnerService->destroy($id);
            if ($status) {
                return $this->sendResponse($res, $mess);
            }

            return $this->sendError($mess);
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage());
        }
    }
}
