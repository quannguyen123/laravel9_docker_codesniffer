<?php

namespace App\Services;

use App\Repositories\JobTitleRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class JobTitleService {
    /**
     * function contructer
     *
     * @param JobTitleRepository $repository
     */
    public function __construct(
        protected readonly JobTitleRepository $repository
    ) {
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($request)
    {
        $filters = [];
        if (!empty($request->orderBy) && !empty($request->orderType)) {
            $filters = [
                'orderBy' => $request->orderBy,
                'orderType' => $request->orderType,
            ];
        }

        if (!empty($request->search)) {
            $filters['search'] = $request->search;
        }

        return $this->repository->search($filters);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $requestData = $request->only(['name']);
        
        $jobTitle = [
            'name' => $requestData['name'],
            'status' => config('custom.status.active'),
            'created_by' => Auth::guard('api-admin')->user()->id
        ];

        return $this->repository->create($jobTitle);
    }

    public function changeStatus($jobTitle, $status) {
        $jobTitle['status'] = config('custom.status.' . $status);
        $jobTitle->save();

        return [];
    }

    public function update(Request $request, $jobTitle)
    {
        $requestData = $request->only(['name']);
        
        $jobTitle['name'] = $requestData['name'];
        $jobTitle['updated_by'] = Auth::guard('api-admin')->user()->id;

        $jobTitle->save();

        return $jobTitle;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($jobTitle)
    {
        $jobTitle['deleted_by'] = Auth::guard('api-admin')->user()->id;
        $jobTitle['deleted_at'] = date("Y-m-d H:i:s", time());
        $jobTitle->save();

        return [];
    }
}