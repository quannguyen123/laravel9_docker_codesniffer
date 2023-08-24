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
    /**
     * @OA\Get(
     *     path="/api/partner/company/detail",
     *     summary="Chi tiết công ty",
     *     tags={"Partner-Company"},
     *     security={{"bearer":{}}},
     *     @OA\Response(response="200", description="An example endpoint")
     * )
     */
    public function detail()
    {
        try {            
            $companyId = Auth::guard('api-user')->user()->company[0]['id'];
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
        /**
     * @OA\Post(
     *     path="/api/partner/company/update",
     *     summary="Cập nhật thông tin công ty",
     *     tags={"Partner-Company"},
     *     security={{"bearer":{}}},
     *     description="Partner Company",
     *      @OA\RequestBody(
     *          @OA\JsonContent(),
     *          @OA\MediaType(
     *              mediaType="multipart/form-data",
     *              @OA\Schema(
     *                  type="object",
     *                  required={"name", "number_phone", "address","welfare_ids[1]", "occupation_ids[]"},
     *                  @OA\Property(property="name", type="string", format="string"),
     *                  @OA\Property(property="number_phone", type="string", format="string"),
     *                  @OA\Property(property="address", type="string", format="string"),
     *                  @OA\Property(property="welfare_ids[1]", type="string", format="string"),
     *                  @OA\Property(property="occupation_ids[]", type="integer", format="int64"),
     *                  @OA\Property(property="logo", type="string", format="binary"),
     *                  @OA\Property(property="banner", type="string", format="binary"),
     *                  @OA\Property(property="video", type="string", format="string"),
     *                  @OA\Property(property="size", type="integer", format="int"),
     *                  @OA\Property(property="recipients_of_cv", type="integer", format="int"),
     *                  @OA\Property(property="info", type="string", format="string")
     *              )
     *          ),
     *     ),
     *     @OA\Response(response="200", description="An example endpoint")
     * )
     */
    public function update(Request $request)
    {
        $company = Auth::guard('api-user')->user()->company[0];

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
                'size' => 'nullable|numeric',
                'recipients_of_cv' => 'nullable|numeric',
                'info' => 'nullable'
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
