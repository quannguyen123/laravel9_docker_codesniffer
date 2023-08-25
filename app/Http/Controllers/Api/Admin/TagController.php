<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\BaseController;
use App\Models\Tag;
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
     *     path="/api/admin/tag/index",
     *     summary="Danh sách từ khóa",
     *     tags={"Admin-Tag"},
     *     security={{"bearer":{}}},
     *     @OA\Parameter(in="query", name="search", required=false, description="Từ khóa cần search", @OA\Schema(type="string")),
     *     @OA\Parameter(in="query", name="orderBy", required=false, description="Cột sắp xếp", @OA\Schema(type="string")),
     *     @OA\Parameter(in="query", name="orderType", required=false, description="Loại sắp xếp: DESC or ASC", @OA\Schema(type="string")),
     *     @OA\Response(response="200", description="An example endpoint")
     * )
     */
    public function index(Request $request)
    {
        try {
            $tagAll = $this->tagService->index($request);

            $res['tagAll'] = $tagAll;

            return $this->sendResponse($res, 'Success.');
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage());
        }
    }

    /**
     * @OA\Post(
     *     path="/api/admin/tag/store",
     *     tags={"Admin-Tag"},
     *     summary="Thêm từ khóa",
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
     *                  @OA\Property(property="occupation_ids[]", type="string", format="string"),
     *                  @OA\Property(property="job_title_ids[]", type="string", format="string")
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
                'name' => 'required|unique:tags,name',
                'occupation_ids' => 'nullable|array',
                'job_title_ids' => 'nullable|array',
            ]);
    
            if($validator->fails()){
                return $this->sendError('Validation Error.', $validator->errors());
            }
    
            $tag = $this->tagService->store($request);
    
            return $this->sendResponse($tag, 'Success.');
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage());
        }
    }

    /**
     * @OA\Get(
     *     path="/api/admin/tag/detail/{tag}",
     *     summary="Thông tin chi tiết từ khóa",
     *     tags={"Admin-Tag"},
     *     security={{"bearer":{}}},
     *     @OA\Parameter(in="path", name="tag", required=true, description="Id từ khóa", @OA\Schema(type="integer")),
     *     @OA\Response(response="200", description="An example endpoint")
     * )
     */
    public function detail(Tag $tag)
    {
        try {            
            $res['tag'] = $tag;

            return $this->sendResponse($res, 'Success.');
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage());
        }
    }

    /**
     * @OA\Post(
     *     path="/api/admin/tag/update/{tag}",
     *     tags={"Admin-Tag"},
     *     summary="Sửa từ khóa",
     *     description="",
     *     security={{"bearer":{}}},
     *     @OA\Parameter(in="path", name="tag", required=true, description="Id từ khóa", @OA\Schema(type="integer")),
     *     @OA\RequestBody(
     *          @OA\JsonContent(),
     *          @OA\MediaType(
     *              mediaType="multipart/form-data",
     *              @OA\Schema(
     *                  type="object",
     *                  required={"name"},
     *                  @OA\Property(property="name", type="string", format="string"),
     *                  @OA\Property(property="occupation_ids[]", type="string", format="string"),
     *                  @OA\Property(property="job_title_ids[]", type="string", format="string")
     *              )
     *          ),
     *     ),
     *     @OA\Response(response="200", description="An example endpoint")
     * )
     */
    public function update(Request $request, Tag $tag)
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required|unique:tags,name,' . $tag['id'],
                'occupation_ids' => 'nullable|array',
                'job_title_ids' => 'nullable|array',
            ]);
            
            if($validator->fails()){
                return $this->sendError('Validation Error.', $validator->errors());
            }
            
            [$status, $res['tag'], $mess] = $this->tagService->update($request, $tag);

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
     *     path="/api/admin/tag/change-status/{tag}/{status}",
     *     summary="Thay đổi trạng thái từ khóa",
     *     tags={"Admin-Tag"},
     *     security={{"bearer":{}}},
     *     @OA\Parameter(in="path", name="tag", required=true, description="Id từ khóa", @OA\Schema(type="integer")),
     *     @OA\Parameter(in="path", name="status", required=true, description="trạng thái từ khóa: lock or active", @OA\Schema(type="string")),
     *     @OA\Response(response="200", description="An example endpoint")
     * )
     */
    public function changeStatus(Tag $tag, $status) {
        try {
            if (!in_array($status, array_keys(config('custom.status')))) {
                return $this->sendError('Status không đúng');
            }

            [$status, $res['tag'], $mess] = $this->tagService->changeStatus($tag, $status);
            
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
     *     path="/api/admin/tag/destroy/{tag}",
     *     summary="Xóa từ khóa",
     *     tags={"Admin-Tag"},
     *     security={{"bearer":{}}},
     *     @OA\Parameter(in="path", name="tag", required=true, description="Id từ khóa", @OA\Schema(type="integer")),
     *     @OA\Response(response="200", description="An example endpoint")
     * )
     */
    public function destroy(Tag $tag)
    {
        try {
            [$status, $res['tag'], $mess] = $this->tagService->destroy($tag);
            
            if ($status) {
                return $this->sendResponse($res, $mess);
            }

            return $this->sendError($mess);
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage());
        }
    }
}
