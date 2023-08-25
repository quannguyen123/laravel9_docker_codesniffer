<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\BaseController;
use App\Models\JobTitle;
use App\Services\JobTitleService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TitleJobController extends BaseController
{
    public function __construct(
        protected JobTitleService $jobTitleService
    ) {
        $this->jobTitleService = $jobTitleService;
    }
    
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    /**
     * @OA\Get(
     *     path="/api/admin/job-title/index",
     *     summary="Danh sách tiêu đề job",
     *     tags={"Admin-Job Title"},
     *     security={{"bearer":{}}},
     *     @OA\Parameter(in="query", name="search", required=false, description="Id tiêu đề", @OA\Schema(type="string")),
     *     @OA\Parameter(in="query", name="orderBy", required=false, description="Cột sắp xếp", @OA\Schema(type="string")),
     *     @OA\Parameter(in="query", name="orderType", required=false, description="Loại sắp xếp: DESC or ASC", @OA\Schema(type="string")),
     *     @OA\Response(response="200", description="An example endpoint")
     * )
     */
    public function index(Request $request)
    {
        try {
            $jobTitleAll = $this->jobTitleService->index($request);

            $res['jobTitleAll'] = $jobTitleAll;

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
     *     path="/api/admin/job-title/store",
     *     tags={"Admin-Job Title"},
     *     summary="Thêm tiêu đề job",
     *     description="",
     *     security={{"bearer":{}}},
     *     @OA\RequestBody(
     *          @OA\JsonContent(),
     *          @OA\MediaType(
     *              mediaType="multipart/form-data",
     *              @OA\Schema(
     *                  type="object",
     *                  required={"name"},
     *                  @OA\Property(property="name", type="string", format="string")
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
                'name' => 'required|unique:job_titles,name',
            ]);
    
            if($validator->fails()){
                return $this->sendError('Validation Error.', $validator->errors());
            }
    
            $jobTitle = $this->jobTitleService->store($request);
    
            return $this->sendResponse($jobTitle, 'Success.');
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
     *     path="/api/admin/job-title/detail/{job_title_id}",
     *     summary="Thông tin chi tiết tiêu đề job",
     *     tags={"Admin-Job Title"},
     *     security={{"bearer":{}}},
     *     @OA\Parameter(in="path", name="job_title_id", required=true, description="Id tiêu đề job", @OA\Schema(type="integer")),
     *     @OA\Response(response="200", description="An example endpoint")
     * )
     */
    public function detail($job_title_id)
    {
        try {            
            [$status, $res['jobTitle'], $mess] = $this->jobTitleService->detail($job_title_id);

            if ($status) {
                return $this->sendResponse($res, $mess);
            }

            return $this->sendError($mess);
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
     *     path="/api/admin/job-title/update/{job_title_id}",
     *     tags={"Admin-Job Title"},
     *     summary="Sửa tiêu đề job",
     *     description="",
     *     security={{"bearer":{}}},
     *     @OA\Parameter(in="path", name="job_title_id", required=true, description="Id loại công việc", @OA\Schema(type="integer")),
     *     @OA\RequestBody(
     *          @OA\JsonContent(),
     *          @OA\MediaType(
     *              mediaType="multipart/form-data",
     *              @OA\Schema(
     *                  type="object",
     *                  required={"name"},
     *                  @OA\Property(property="name", type="string", format="string")
     *              )
     *          ),
     *     ),
     *     @OA\Response(response="200", description="An example endpoint")
     * )
     */
    public function update(Request $request, $job_title_id)
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required|unique:job_titles,name,' . $job_title_id,
                'icon' => 'nullable|mimes:jpeg,jpg,png,gif|max:5120'
            ]);
            
            if($validator->fails()){
                return $this->sendError('Validation Error.', $validator->errors());
            }
            
            [$status, $res['jobTitle'], $mess] = $this->jobTitleService->update($request, $job_title_id);

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
     *     path="/api/admin/job-title/change-status/{job_title_id}/{status}",
     *     summary="Thay đổi trạng thái tiêu đề job",
     *     tags={"Admin-Job Title"},
     *     security={{"bearer":{}}},
     *     @OA\Parameter(in="path", name="job_title_id", required=true, description="Id tiêu đề job", @OA\Schema(type="integer")),
     *     @OA\Parameter(in="path", name="status", required=true, description="trạng thái loại công việc: lock or active", @OA\Schema(type="string")),
     *     @OA\Response(response="200", description="An example endpoint")
     * )
     */
    public function changeStatus($job_title_id, $status) {
        try {
            if (!in_array($status, array_keys(config('custom.status')))) {
                return $this->sendError('Status không đúng');
            }

            [$status, $res['jobTitle'], $mess] = $this->jobTitleService->changeStatus($job_title_id, $status);

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
     *     path="/api/admin/job-title/destroy/{job_title_id}",
     *     summary="Xóa tiêu đề job",
     *     tags={"Admin-Job Title"},
     *     security={{"bearer":{}}},
     *     @OA\Parameter(in="path", name="job_title_id", required=true, description="Id tiêu đề job", @OA\Schema(type="integer")),
     *     @OA\Response(response="200", description="An example endpoint")
     * )
     */
    public function destroy($job_title_id)
    {
        try {
            [$status, $res['jobTitle'], $mess] = $this->jobTitleService->destroy($job_title_id);

            if ($status) {
                return $this->sendResponse($res, $mess);
            }

            return $this->sendError($mess);
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage());
        }
    }
}
