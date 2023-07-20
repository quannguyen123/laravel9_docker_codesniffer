<?php

namespace App\Http\Controllers\Api\Partner;

use App\Http\Controllers\BaseController;
use App\Models\Company;
use App\Services\CompanyService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class CompanyController extends BaseController
{
    public function __construct(
        protected CompanyService $companyService
    ) {
        $this->companyService = $companyService;
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function detail()
    {
        try {            
            $companyId = Auth::guard('api-user')->user()->company[0]['id'];
            // $res['company'] = $company;
            $data = $this->companyService->detail($companyId);
            return $this->sendResponse($data, 'Success.');
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage());
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        $company = Auth::guard('api-user')->user()->company[0];

        // return $request->all();
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required|unique:companies,name,' . $company['id'],
                'number_phone' => 'required|unique:companies,number_phone,' . $company['id'],
                'address' => 'required',
                'welfare_ids' => 'required|array',
                'occupation_ids' => 'required|array|exists:occupations,id',
                'logo' => 'nullable|mimes:jpeg,jpg,png,gif|max:5120',
                'banner' => 'nullable|mimes:jpeg,jpg,png,gif|max:5120',
                'video' => 'nullable|url',
            ]);
            
            if($validator->fails()){
                return $this->sendError('Validation Error.', $validator->errors());
            }
            
            $data = $this->companyService->update($request, $company);

            return $this->sendResponse($data, 'Success.');
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage());
        }
    }
}
