<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\BaseController;
use App\Services\AlertJobService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AlertJobController extends BaseController
{
    public function __construct(
        protected AlertJobService $alertJobService
    ) {
        $this->alertJobService = $alertJobService;
    }
    
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    /**
     * @OA\Get(
     *     path="/api/user/alert-job/index",
     *     summary="Danh sách thông báo",
     *     tags={"User-Alert Job"},
     *     security={{"bearer":{}}},
     *     @OA\Parameter(in="query", name="orderBy", required=false, description="Cột sắp xếp", @OA\Schema(type="string")),
     *     @OA\Parameter(in="query", name="orderType", required=false, description="Loại sắp xếp: DESC or ASC", @OA\Schema(type="string")),
     *     @OA\Response(response="200", description="An example endpoint")
     * )
     */
    public function index(Request $request)
    {
        try {
            [$status, $alertJobAll, $mess] = $this->alertJobService->index($request);

            $res['alertJobAll'] = $alertJobAll;

            return $this->sendResponse($res, $mess);
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
     *     path="/api/user/alert-job/store",
     *     tags={"User-Alert Job"},
     *     summary="Thêm thông báo",
     *     description="",
     *     security={{"bearer":{}}},
     *     @OA\RequestBody(
     *          @OA\JsonContent(),
     *          @OA\MediaType(
     *              mediaType="multipart/form-data",
     *              @OA\Schema(
     *                  type="object",
     *                  @OA\Property(property="position", type="string", format="string"),
     *                  @OA\Property(property="salary_min", type="integer", format="int"),
     *                  @OA\Property(property="rank[]", type="integer", format="int"),
     *                  @OA\Property(property="province[]", type="integer", format="int"),
     *                  @OA\Property(property="occupation[]", type="integer", format="int"),
     *                  @OA\Property(property="industry[]", type="integer", format="int"),
     *                  @OA\Property(property="interval", type="integer", format="int"),
     *                  @OA\Property(property="notification_by", type="integer", format="int"),
     *                  @OA\Property(property="status", type="integer", format="int")
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
                'position' => 'nullable',
                'salary_min' => 'nullable|numeric',
                'rank' => 'nullable|array',
                'rank.*' => 'nullable|numeric',
                'province' => 'nullable|array',
                'province.*' => 'nullable|numeric|exists:provinces,id',
                'occupation' => 'nullable|array',
                'occupation.*' => 'nullable|numeric|exists:occupations,id',
                'industry' => 'nullable|array',
                'industry.*' => 'nullable|numeric|exists:industries,id',
                'interval' => 'nullable|numeric',
                'notification_by' => 'nullable|numeric',
                'status' => 'nullable|numeric',
            ]);
    
            if($validator->fails()){
                return $this->sendError('Validation Error.', $validator->errors());
            }
    
            [$status, $alertJob, $mess] = $this->alertJobService->store($request);

            $res['alertJob'] = $alertJob;
    
            return $this->sendResponse($res, $mess);
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
     *     path="/api/user/alert-job/detail/{id}",
     *     summary="Chi tiết thông báo",
     *     tags={"User-Alert Job"},
     *     security={{"bearer":{}}},
     *     @OA\Parameter(in="path", name="id", required=true, description="Id thông báo", @OA\Schema(type="integer")),
     *     @OA\Response(response="200", description="An example endpoint")
     * )
     */
    public function detail($id)
    {
        try {            
            [$status, $alertJob, $mess] = $this->alertJobService->detail($id);
            $res['alertJob'] = $alertJob;

            if ($status) {
                return $this->sendResponse($res, $mess);
            } else {
                return $this->sendError($mess);
            }
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
     *     path="/api/user/alert-job/update/{id}",
     *     tags={"User-Alert Job"},
     *     summary="Cập nhật thông báo",
     *     description="",
     *     security={{"bearer":{}}},
     *     @OA\Parameter(in="path", name="id", required=true, description="Id loại công việc", @OA\Schema(type="integer")),
     *     @OA\RequestBody(
     *          @OA\JsonContent(),
     *          @OA\MediaType(
     *              mediaType="multipart/form-data",
     *              @OA\Schema(
     *                  type="object",
     *                  @OA\Property(property="position", type="string", format="string"),
     *                  @OA\Property(property="salary_min", type="integer", format="int"),
     *                  @OA\Property(property="rank[]", type="integer", format="int"),
     *                  @OA\Property(property="province[]", type="integer", format="int"),
     *                  @OA\Property(property="occupation[]", type="integer", format="int"),
     *                  @OA\Property(property="industry[]", type="integer", format="int"),
     *                  @OA\Property(property="interval", type="integer", format="int"),
     *                  @OA\Property(property="notification_by", type="integer", format="int"),
     *                  @OA\Property(property="status", type="integer", format="int")
     *              )
     *          ),
     *     ),
     *     @OA\Response(response="200", description="An example endpoint")
     * )
     */
    public function update(Request $request, $id)
    {
        try {
            $validator = Validator::make($request->all(), [
                'position' => 'nullable',
                'salary_min' => 'nullable|numeric',
                'rank' => 'nullable|array',
                'rank.*' => 'nullable|numeric',
                'province' => 'nullable|array',
                'province.*' => 'nullable|numeric|exists:provinces,id',
                'occupation' => 'nullable|array',
                'occupation.*' => 'nullable|numeric|exists:occupations,id',
                'industry' => 'nullable|array',
                'industry.*' => 'nullable|numeric|exists:industries,id',
                'interval' => 'nullable|numeric',
                'notification_by' => 'nullable|numeric',
                'status' => 'nullable|numeric',
            ]);
    
            if($validator->fails()){
                return $this->sendError('Validation Error.', $validator->errors());
            }
            
            [$status, $alertJob, $mess] = $this->alertJobService->update($request, $id);
            $res['alertJob'] = $alertJob;

            if ($status) {
                return $this->sendResponse($res, $mess);
            } else {
                return $this->sendError($mess);
            }
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
     *     path="/api/user/alert-job/destroy/{id}",
     *     summary="Xóa loại công việc",
     *     tags={"User-Alert Job"},
     *     security={{"bearer":{}}},
     *     @OA\Parameter(in="path", name="id", required=true, description="Id thông báo", @OA\Schema(type="integer")),
     *     @OA\Response(response="200", description="An example endpoint")
     * )
     */
    public function destroy($id)
    {
        try {
            [$status, $mess] = $this->alertJobService->destroy($id);

            if ($status) {
                return $this->sendResponse([], $mess);
            } else {
                return $this->sendError($mess);
            }
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage());
        }
    }
}
