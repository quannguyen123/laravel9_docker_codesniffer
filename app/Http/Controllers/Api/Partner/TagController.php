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

    public function index(Request $request) {
        try {
            $tags = $this->tagService->publicSearchTag($request);

            $res['tags'] = $tags;

            return $this->sendResponse($res, 'Success.');
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage());
        }
    }

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
