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
