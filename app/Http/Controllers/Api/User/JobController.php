<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\BaseController;
use App\Services\JobService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class JobController extends BaseController
{
    public function __construct(
        protected JobService $jobService
    ) {
        $this->jobService = $jobService;
    }

    /**
     * @OA\Get(
     *     path="/api/job/index",
     *     summary="Danh sách các job",
     *     tags={"User-Job"},
     *     @OA\Parameter(
     *          in="query",
     *          name="occupation_ids[]",
     *          required=false,
     *          description="Id ngành nghề",
     *          @OA\Schema(
     *            type="string"
     *          )
     *     ),
     *     @OA\Parameter(
     *          in="query",
     *          name="location_id",
     *          required=false,
     *          description="Id vị trí công việc",
     *          @OA\Schema(
     *            type="number"
     *          )
     *     ),
     *     @OA\Parameter(
     *          in="query",
     *          name="welfare_ids[]",
     *          required=false,
     *          description="Id phúc lợi",
     *          @OA\Schema(
     *            type="number"
     *          )
     *     ),
     *     @OA\Parameter(
     *          in="query",
     *          name="search",
     *          required=false,
     *          description="Id phúc lợi",
     *          @OA\Schema(
     *            type="string"
     *          )
     *      ),
     *      @OA\Parameter(
     *          in="query",
     *          name="rank",
     *          required=false,
     *          description="Vị trí tuyển dụng",
     *          @OA\Schema(
     *            type="number"
     *          )
     *      ),
     *      @OA\Parameter(
     *          in="query",
     *          name="job_type",
     *          required=false,
     *          description="Loại công việc",
     *          @OA\Schema(
     *            type="number"
     *          )
     *      ),
     *      @OA\Parameter(
     *          in="query",
     *          name="salary_type",
     *          required=false,
     *          description="Mức lương",
     *          @OA\Schema(
     *            type="number"
     *          )
     *      ),
     *      @OA\Parameter(
     *          in="query",
     *          name="urgent",
     *          required=false,
     *          description="Công việc gấp",
     *          @OA\Schema(
     *            type="number"
     *          )
     *      ),
     *      @OA\Response(response="200", description="An example endpoint")
     * )
     */
    public function index(Request $request) {
        try {
            $validator = Validator::make($request->all(), [
                'occupation_ids' => 'nullable|exists:occupations,id',
                'location_id' => 'nullable|exists:provinces,id',
                'welfare_ids' => 'nullable|exists:welfares,id',
                'search' => 'nullable',
                'rank' => 'nullable',
                'job_type' => 'nullable',
                'salary_type' => 'nullable',
                'urgent' => 'nullable',
            ]);

            if($validator->fails()){
                return $this->sendError('Validation Error.', $validator->errors());
            }

            [$status, $jobs, $mess] = $this->jobService->searchJob($request);

            $res['jobs'] = $jobs;

            return $this->sendResponse($jobs, $mess);
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage());
        }
    }

    /**
     * @OA\Get(
     *     path="/api/job/detail/{id}",
     *     summary="Thông tin chi tiết job",
     *     tags={"User-Job"},
     *     @OA\Parameter(
     *          in="path",
     *          name="id",
     *          required=true,
     *          description="Order id",
     *          @OA\Schema(
     *            type="integer"
     *          )
     *     ),
     *     @OA\Response(response="200", description="An example endpoint")
     * )
     */
    public function detail($id) {
        try {
            [$status, $job, $mess] = $this->jobService->jobDetail($id);

            $res['job'] = $job;

            return $this->sendResponse($res, $mess);
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage());
        }
    }

    /**
     * @OA\Post(
     *     path="/api/job/job-apply/{id}",
     *     summary="Ứng tuyển Job",
     *     tags={"User-Job"},
     *     security={{"bearer":{}}},
     *     description="Nộp CV",
     *     @OA\Parameter(in="path", name="id", required=true, description="Id công việc", @OA\Schema(type="integer")),
     *     @OA\RequestBody(
     *          @OA\JsonContent(),
     *          @OA\MediaType(
     *              mediaType="multipart/form-data",
     *              @OA\Schema(
     *                  type="object",
     *                  required={"position", "number_phone", "file_cv"},
     *                  @OA\Property(property="position", type="string", format="string", example="Nhân viên", description ="Vị trí ứng tuyển"),
     *                  @OA\Property(property="number_phone", type="string", format="string"),
     *                  @OA\Property(property="file_cv", type="string", format="binary")
     *              )
     *          ),
     *     ),
     *     @OA\Response(response="200", description="An example endpoint")
     * )
     */
    public function applyJob(Request $request, $id) {
        try {
            // TODO:: send mail cho user và partner khi có job apply
            $validator = Validator::make($request->all(), [
                'position' => 'required',
                'number_phone' => 'required',
                'file_cv' => 'nullable|mimes:pdf|max:5120'
            ]);

            if($validator->fails()){
                return $this->sendError('Validation Error.', $validator->errors());
            }

            [$status, $job, $mess] = $this->jobService->applyJob($request, $id);

            $res['job'] = $job;

            return $this->sendResponse($res, $mess);
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage());
        }
    }

    /**
     * @OA\Get(
     *     path="/api/job/job-favourite/{id}",
     *     summary="Job yêu thích",
     *     tags={"User-Job"},
     *     security={{"bearer":{}}},
     *     description="User register",
     *     @OA\Parameter(in="path", name="id", required=true, description="Id công việc", @OA\Schema(type="integer")),
     *     @OA\Response(response="200", description="An example endpoint")
     * )
     */
    public function jobFavourite($id) {
        try {
            [$status, $job, $mess] = $this->jobService->jobFavourite($id);

            $res['job'] = $job;

            return $this->sendResponse($res, $mess);
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage());
        }
    }
    
    
}
