<?php

namespace App\Http\Controllers\Api\Public;

use App\Http\Controllers\BaseController;
use App\Services\JobTitleService;
use Illuminate\Http\Request;

class JobTitleController extends BaseController
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
     *     path="/api/public/job-title/index",
     *     summary="Danh sách tiêu đề",
     *     tags={"Public-Job Title"},
     *     security={{"bearer":{}}},
     *     @OA\Parameter(in="query", name="search", required=false, description="Tiêu đề công việc", @OA\Schema(type="string")),
     *     @OA\Parameter(in="query", name="orderBy", required=false, description="Cột sắp xếp", @OA\Schema(type="string")),
     *     @OA\Parameter(in="query", name="orderType", required=false, description="Loại sắp xếp: DESC or ASC", @OA\Schema(type="string")),
     *     @OA\Response(response="200", description="An example endpoint")
     * )
     */
    public function index(Request $request)
    {
        try {
            $jobTitles = $this->jobTitleService->publicSearchJobTitle($request);

            $res['jobTitles'] = $jobTitles;

            return $this->sendResponse($res, 'Success.');
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage());
        }
    }
}
