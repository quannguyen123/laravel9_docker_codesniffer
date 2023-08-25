<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\BaseController;
use App\Models\Welfare;
use App\Services\WelfareService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class WelfareController extends BaseController
{
    public function __construct(
        protected WelfareService $welfareService
    ) {
        $this->welfareService = $welfareService;
    }
    
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    /**
     * @OA\Get(
     *     path="/api/admin/welfare/index",
     *     summary="Danh sách phúc lợi",
     *     tags={"Admin-Welfare"},
     *     security={{"bearer":{}}},
     *     @OA\Parameter(in="query", name="search", required=false, description="Id phúc lợi", @OA\Schema(type="string")),
     *     @OA\Parameter(in="query", name="orderBy", required=false, description="Cột sắp xếp", @OA\Schema(type="string")),
     *     @OA\Parameter(in="query", name="orderType", required=false, description="Loại sắp xếp: DESC or ASC", @OA\Schema(type="string")),
     *     @OA\Response(response="200", description="An example endpoint")
     * )
     */
    public function index(Request $request)
    {
        try {
            $welfareAll = $this->welfareService->index($request);

            $res['welfareAll'] = $welfareAll;

            return $this->sendResponse($res, 'Success.');
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage());
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    /**
     * @OA\Post(
     *     path="/api/admin/welfare/store",
     *     tags={"Admin-Welfare"},
     *     summary="Thêm phúc lợi",
     *     description="",
     *     security={{"bearer":{}}},
     *     @OA\RequestBody(
     *          @OA\JsonContent(),
     *          @OA\MediaType(
     *              mediaType="multipart/form-data",
     *              @OA\Schema(
     *                  type="object",
     *                  required={"name"},
     *                  @OA\Property(property="name", type="string", format="string"),
     *                  @OA\Property(property="icon", type="string", format="binary")
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
                'name' => 'required|unique:welfares,name',
                'icon' => 'required|mimes:jpeg,jpg,png,gif|max:5120'
            ]);
    
            if($validator->fails()){
                return $this->sendError('Validation Error.', $validator->errors());
            }
    
            $welfare = $this->welfareService->store($request);
    
            return $this->sendResponse($welfare, 'Success.');
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    /**
     * @OA\Get(
     *     path="/api/admin/welfare/detail/{welfare}",
     *     summary="Thông tin chi tiết phúc lợi",
     *     tags={"Admin-Welfare"},
     *     security={{"bearer":{}}},
     *     @OA\Parameter(in="path", name="welfare", required=true, description="Id phúc lợi", @OA\Schema(type="integer")),
     *     @OA\Response(response="200", description="An example endpoint")
     * )
     */
    public function detail(Welfare $welfare)
    {
        try {            
            $res['welfare'] = $welfare;

            return $this->sendResponse($res, 'Success.');
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage());
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    /**
     * @OA\Post(
     *     path="/api/admin/welfare/update/{welfare}",
     *     tags={"Admin-Welfare"},
     *     summary="Sửa phúc lợi",
     *     description="",
     *     security={{"bearer":{}}},
     *     @OA\Parameter(in="path", name="welfare", required=true, description="Id phúc lợi", @OA\Schema(type="integer")),
     *     @OA\RequestBody(
     *          @OA\JsonContent(),
     *          @OA\MediaType(
     *              mediaType="multipart/form-data",
     *              @OA\Schema(
     *                  type="object",
     *                  required={"name"},
     *                  @OA\Property(property="name", type="string", format="string"),
     *                  @OA\Property(property="icon", type="string", format="binary")
     *              )
     *          ),
     *     ),
     *     @OA\Response(response="200", description="An example endpoint")
     * )
     */
    public function update(Request $request, Welfare $welfare)
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required|unique:welfares,name,' . $welfare['id'],
                'icon' => 'nullable|mimes:jpeg,jpg,png,gif|max:5120'
            ]);
            
            if($validator->fails()){
                return $this->sendError('Validation Error.', $validator->errors());
            }
            
            [$status, $res['welfare'], $mess] = $this->welfareService->update($request, $welfare);

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
     *     path="/api/admin/welfare/change-status/{welfare}/{status}",
     *     summary="Thay đổi trạng thái phúc lợi",
     *     tags={"Admin-Welfare"},
     *     security={{"bearer":{}}},
     *     @OA\Parameter(in="path", name="welfare", required=true, description="Id phúc lợi", @OA\Schema(type="integer")),
     *     @OA\Parameter(in="path", name="status", required=true, description="trạng thái loại công việc: lock or active", @OA\Schema(type="string")),
     *     @OA\Response(response="200", description="An example endpoint")
     * )
     */
    public function changeStatus(Welfare $welfare, $status) {
        try {
            if (!in_array($status, array_keys(config('custom.status')))) {
                return $this->sendError('Status không đúng');
            }

            [$status, $res['welfare'], $mess] = $this->welfareService->changeStatus($welfare, $status);
            
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
     *     path="/api/admin/welfare/destroy/{welfare}",
     *     summary="Xóa phúc lợi",
     *     tags={"Admin-Welfare"},
     *     security={{"bearer":{}}},
     *     @OA\Parameter(in="path", name="welfare", required=true, description="Id phúc lợi", @OA\Schema(type="integer")),
     *     @OA\Response(response="200", description="An example endpoint")
     * )
     */
    public function destroy(Welfare $welfare)
    {
        try {
            [$status, $res['welfare'], $mess] = $this->welfareService->destroy($welfare);
            
            if ($status) {
                return $this->sendResponse($res, $mess);
            }

            return $this->sendError($mess);
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage());
        }
    }
}
