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

    public function detail(Tag $tag)
    {
        try {            
            $res['tag'] = $tag;

            return $this->sendResponse($res, 'Success.');
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage());
        }
    }

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
