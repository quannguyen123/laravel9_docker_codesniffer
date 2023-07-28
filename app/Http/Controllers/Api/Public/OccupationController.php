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
