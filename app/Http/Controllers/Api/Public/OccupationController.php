<?php

namespace App\Http\Controllers\Api\Public;

use App\Http\Controllers\BaseController;
use App\Services\OccupationService;
use Illuminate\Http\Request;

class OccupationController extends BaseController
{
    public function __construct(
        protected OccupationService $occupationService
    ) {
        $this->occupationService = $occupationService;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    /**
     * @OA\Get(
     *     path="/api/public/occupation/index",
     *     summary="Danh sách ngành nghề",
     *     tags={"Public-Occupation"},
     *     security={{"bearer":{}}},
     *     @OA\Parameter(in="query", name="search", required=false, description="Ngành nghề cần tìm", @OA\Schema(type="string")),
     *     @OA\Parameter(in="query", name="orderBy", required=false, description="Cột sắp xếp", @OA\Schema(type="string")),
     *     @OA\Parameter(in="query", name="orderType", required=false, description="Loại sắp xếp: DESC or ASC", @OA\Schema(type="string")),
     *     @OA\Response(response="200", description="An example endpoint")
     * )
     */
    public function index(Request $request)
    {
        try {
            $occupations = $this->occupationService->publicSearchOccupation($request);

            $res['occupations'] = $occupations;

            return $this->sendResponse($res, 'Success.');
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage());
        }
    }
}
