<?php

namespace App\Http\Controllers\Api\Public;

use App\Http\Controllers\BaseController;
use App\Http\Controllers\Controller;
use App\Services\DistrictService;
use App\Services\ProvinceService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ProvinceController extends BaseController
{
    public function __construct(
        protected ProvinceService $provinceService,
        protected DistrictService $districtService
    ) {
        $this->provinceService = $provinceService;
        $this->districtService = $districtService;
    }

    public function listProvince(Request $request) {
        try {
            $province = $this->provinceService->listProvince($request);

            $res['province'] = $province;

            return $this->sendResponse($res, 'Success.');
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage());
        }
    }
    
    public function listDistrictByProvince(Request $request) {
        try {
            $validator = Validator::make($request->all(), [
                'provinceId' => 'required|exists:provinces,id'
            ]);
            
            if($validator->fails()){
                return $this->sendError('Validation Error.', $validator->errors());
            }

            $province = $this->districtService->listDistrictByProvince($request);

            $res['province'] = $province;

            return $this->sendResponse($res, 'Success.');
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage());
        }
    }
}
