<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\BaseController;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\UserService;
use Illuminate\Http\Request;

class UserController extends BaseController
{
    public function __construct(
        protected UserService $userService
    ) {
        $this->userService = $userService;
    }
    
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    /**
     * @OA\Get(
     *     path="/api/admin/user/index",
     *     summary="Danh sách người dùng",
     *     tags={"Admin-Managerment User"},
     *     security={{"bearer":{}}},
     *     @OA\Parameter(in="query", name="search", required=false, description="Tên user", @OA\Schema(type="string")),
     *     @OA\Parameter(in="query", name="orderBy", required=false, description="Cột sắp xếp", @OA\Schema(type="string")),
     *     @OA\Parameter(in="query", name="orderType", required=false, description="Loại sắp xếp: DESC or ASC", @OA\Schema(type="string")),
     *     @OA\Response(response="200", description="An example endpoint")
     * )
     */
    public function index(Request $request)
    {
        try {
            $userAll = $this->userService->index($request);

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
     *     path="/api/admin/user/detail/{id}",
     *     summary="Thông tin chi tiết người dùng",
     *     tags={"Admin-Managerment User"},
     *     security={{"bearer":{}}},
     *     @OA\Parameter(in="path", name="id", required=true, description="Id người dùng", @OA\Schema(type="integer")),
     *     @OA\Response(response="200", description="An example endpoint")
     * )
     */
    public function detail($id)
    {
        try {
            [$status, $res['user'], $mess] = $this->userService->detail($id);

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
     *     path="/api/admin/user/change-status/{id}/{status}",
     *     summary="Thay đổi trạng thái người dùng",
     *     tags={"Admin-Managerment User"},
     *     security={{"bearer":{}}},
     *     @OA\Parameter(in="path", name="id", required=true, description="Id người dùng", @OA\Schema(type="integer")),
     *     @OA\Parameter(in="path", name="status", required=true, description="trạng thái người dùng: lock or active", @OA\Schema(type="string")),
     *     @OA\Response(response="200", description="An example endpoint")
     * )
     */
    public function changeStatus($id, $status) {
        try {
            if (!in_array($status, array_keys(config('custom.status')))) {
                return $this->sendError('Status không đúng');
            }

            [$status, $res['user'], $mess] = $this->userService->changeStatus($id, $status);
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
     *     path="/api/admin/user/destroy/{id}",
     *     summary="Xóa người dùng",
     *     tags={"Admin-Managerment User"},
     *     security={{"bearer":{}}},
     *     @OA\Parameter(in="path", name="id", required=true, description="Id người dùng", @OA\Schema(type="integer")),
     *     @OA\Response(response="200", description="An example endpoint")
     * )
     */
    public function destroy($id)
    {
        try {
            [$status, $res['user'], $mess] = $this->userService->destroy($id);
            if ($status) {
                return $this->sendResponse($res, $mess);
            }

            return $this->sendError($mess);
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage());
        }
    }
}
