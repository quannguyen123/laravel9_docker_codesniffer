<?php

namespace App\Http\Controllers\Api\Partner;

use App\Http\Controllers\BaseController;
use App\Services\TagService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TagController extends BaseController
{
    public function __construct(
        protected TagService $tagService
    ) {
        $this->tagService = $tagService;
    }

    /**
     * @OA\Get(
     *     path="/api/public/tag/index",
     *     summary="Danh sách từ khóa",
     *     tags={"Public-Tag"},
     *     security={{"bearer":{}}},
     *     @OA\Parameter(in="query", name="search", required=false, description="Từ khóa", @OA\Schema(type="string")),
     *     @OA\Parameter(in="query", name="orderBy", required=false, description="Cột sắp xếp", @OA\Schema(type="string")),
     *     @OA\Parameter(in="query", name="orderType", required=false, description="Loại sắp xếp: DESC or ASC", @OA\Schema(type="string")),
     *     @OA\Response(response="200", description="An example endpoint")
     * )
     */
    public function index(Request $request) {
        try {
            $tags = $this->tagService->publicSearchTag($request);

            $res['tags'] = $tags;

            return $this->sendResponse($res, 'Success.');
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage());
        }
    }

    /**
     * @OA\Post(
     *     path="/api/public/tag/suggest",
     *     tags={"Public-Tag"},
     *     summary="Danh sách từ khóa gợi ý",
     *     description="",
     *     security={{"bearer":{}}},
     *     @OA\RequestBody(
     *          @OA\JsonContent(),
     *          @OA\MediaType(
     *              mediaType="multipart/form-data",
     *              @OA\Schema(
     *                  type="object",
     *                  required={"occupation_id", "job_title_id"},
     *                  @OA\Property(property="occupation_id", type="integer", format="int64"),
     *                  @OA\Property(property="job_title_id", type="integer", format="int64")
     *              )
     *          ),
     *     ),
     *     @OA\Response(response="200", description="An example endpoint")
     * )
     */
    public function suggest(Request $request) {
        try {
            $validator = Validator::make($request->all(), [
                'occupation_id' => 'required|exists:occupations,id',
                'job_title_id' => 'required|exists:job_titles,id',
            ]);

            if($validator->fails()){
                return $this->sendError('Validation Error.', $validator->errors());
            }

            $tags = $this->tagService->getTagSuggest($request);

            $res['tags'] = $tags;

            return $this->sendResponse($res, 'Success.');
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage());
        }
    }
}
