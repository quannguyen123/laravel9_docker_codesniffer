<?php

namespace App\Services;

use App\Models\Company;
use App\Models\CompanyOccupation;
use App\Models\CompanyWelfare;
use App\Repositories\CompanyRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CompanyService {
    /**
     * function contructer
     *
     * @param CompanyRepository $repository
     */
    public function __construct(
        protected readonly CompanyRepository $repository
    ) {
    }

    public function update(Request $request, $company)
    {
        $requestData = $request->only([
            'name',
            'number_phone',
            'address',
            'size',
            'recipients_of_cv',
            'info',
            'logo',
            'banner',
            'video',
            'occupation_ids',
            'welfare_ids'
        ]);
        
        $companyData['name'] = $requestData['name'];
        $companyData['number_phone'] = $requestData['number_phone'];
        $companyData['address'] = $requestData['address'];
        $companyData['size'] = $requestData['size'];
        $companyData['recipients_of_cv'] = $requestData['recipients_of_cv'];
        $companyData['info'] = $requestData['info'];
        $companyData['video'] = $requestData['video'];
        $companyData['updated_by'] = Auth::guard('api-user')->user()->id;

        if (!empty($request->file('logo'))) {
            removeImage('img', $company['logo']);

            $image = $request->file('logo');
            $image_name = time().'.'.$image->getClientOriginalExtension();
            $destinationPath = public_path('/images/img');
            $image->move($destinationPath, $image_name);
            $companyData['logo'] = $image_name;
        }

        if (!empty($request->file('banner'))) {
            removeImage('img', $company['banner']);

            $image = $request->file('banner');
            $image_name = time().'.'.$image->getClientOriginalExtension();
            $destinationPath = public_path('/images/img');
            $image->move($destinationPath, $image_name);
            $companyData['banner'] = $image_name;
        }

        $data = $this->repository->update($companyData, $company['id']);

        // save occupation
        $occupationIds = (array)$requestData['occupation_ids'];
        if (!empty($occupationIds)) {
            foreach ($occupationIds as $occupationId) {
                $companyOccupation[] = [
                    'company_id' => $company['id'],
                    'occupation_id' => $occupationId,
                ];
            }

            if (!empty($companyOccupation)) {
                CompanyOccupation::where('company_id', $company['id'])->delete();
            }

            CompanyOccupation::insert($companyOccupation);
        }

        // save welfare
        $welfareIds = (array)$requestData['welfare_ids'];
        if (!empty($welfareIds)) {
            $companyWelfare = [];

            foreach ($welfareIds as $key => $value) {
                $companyWelfare[] = [
                    'company_id' => $company['id'],
                    'welfare_id' => $key,
                    'content' => $value,
                ];
            }

            if (!empty($companyWelfare)) {
                CompanyWelfare::where('company_id', $company['id'])->delete();
            }

            CompanyWelfare::insert($companyWelfare);
        }

        return $data;
    }

    public function detail($companyId) {
        $company = Company::where('id', $companyId)->with(['occupations', 'welfares'])->get();

        return $company;
    }

}