<?php

namespace App\Services;

use App\Models\CompanyLocation;
use App\Repositories\CompanyLocationRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CompanyLocationService {
    /**
     * function contructer
     *
     * @param CompanyLocationRepository $repository
     */
    public function __construct(
        protected readonly CompanyLocationRepository $repository
    ) {
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($request)
    {
        $companyLocaltion = CompanyLocation::where('company_id', Auth::guard('api-user')->user()->company[0]['id'])->get();

        return $companyLocaltion;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $requestData = $request->only([
            'name',
            'address',
            'province_id',
        ]);
        
        $companyLocaltion = [
            'name' => $requestData['name'],
            'address' => $requestData['address'],
            'status' => config('custom.status.active'),
            'company_id' => Auth::guard('api-user')->user()->company[0]['id'],
            'province_id' => $requestData['province_id'],
        ];

        return $this->repository->create($companyLocaltion);
    }

    public function detail($id) {
        $companyLocaltion = CompanyLocation::where('id', $id)
                                    ->where('company_id', Auth::guard('api-user')->user()->company[0]['id'])
                                    ->first();

        return $companyLocaltion;
    }

    public function update(Request $request, $id)
    {
        $requestData = $request->only([
            'name',
            'address',
            'province_id',
        ]);

        $companyLocaltion = CompanyLocation::where('id', $id)
                                    ->where('company_id', Auth::guard('api-user')->user()->company[0]['id'])
                                    ->first();

        if (empty($companyLocaltion)) {
            return [];
        }

        $companyLocaltion['name'] = $requestData['name'];
        $companyLocaltion['address'] = $requestData['address'];
        $companyLocaltion['province_id'] = $requestData['province_id'];

        $companyLocaltion->save();

        return $companyLocaltion;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $isDelete = CompanyLocation::where('id', $id)
                                    ->where('company_id', Auth::guard('api-user')->user()->company[0]['id'])
                                    ->delete();
        
        if ($isDelete) {
            return true;
        }

        return false;
    }
}