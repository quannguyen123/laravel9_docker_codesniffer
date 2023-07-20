<?php

namespace App\Services;

use App\Models\JobLocation;
use App\Repositories\JobLocationRepository;
use App\Repositories\JobTitleRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class JobLocationService {
    /**
     * function contructer
     *
     * @param JobLocationRepository $repository
     */
    public function __construct(
        protected readonly JobLocationRepository $repository
    ) {
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($request)
    {
        $jobLocation = JobLocation::where('company_id', Auth::guard('api-user')->user()->company[0]['id'])->get();

        return $jobLocation;
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
        
        $jobLocation = [
            'name' => $requestData['name'],
            'address' => $requestData['address'],
            'status' => config('custom.status.active'),
            'company_id' => Auth::guard('api-user')->user()->company[0]['id'],
            'province_id' => $requestData['province_id'],
        ];

        return $this->repository->create($jobLocation);
    }

    public function detail($id) {
        $jobLocation = JobLocation::where('id', $id)
                                    ->where('company_id', Auth::guard('api-user')->user()->company[0]['id'])
                                    ->first();

        return $jobLocation;
    }

    public function update(Request $request, $id)
    {
        $requestData = $request->only([
            'name',
            'address',
            'province_id',
        ]);

        $jobLocation = JobLocation::where('id', $id)
                                    ->where('company_id', Auth::guard('api-user')->user()->company[0]['id'])
                                    ->first();

        if (empty($jobLocation)) {
            return [];
        }

        $jobLocation['name'] = $requestData['name'];
        $jobLocation['address'] = $requestData['address'];
        $jobLocation['province_id'] = $requestData['province_id'];

        $jobLocation->save();

        return $jobLocation;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $isDelete = JobLocation::where('id', $id)
                                    ->where('company_id', Auth::guard('api-user')->user()->company[0]['id'])
                                    ->delete();
        
        if ($isDelete) {
            return true;
        }

        return false;
    }
}