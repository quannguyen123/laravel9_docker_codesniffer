<?php

namespace App\Services;

use App\Models\AlertJob;
use App\Models\Industry;
use App\Models\Occupation;
use App\Models\Province;
use App\Repositories\AlertJobRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AlertJobService {
    /**
     * function contructer
     *
     * @param AlertJobRepository $repository
     */
    public function __construct(
        protected readonly AlertJobRepository $repository
    ) {
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($request)
    {
        $alertJob = AlertJob::where('user_id', Auth::guard('api-user')->user()->id)->get();
        $alertJob = $this->convertData($alertJob);

        return [true, $alertJob, 'Success'];
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
            'position',
            'salary_min',
            'rank',
            'province',
            'occupation',
            'industry',
            'interval',
            'notification_by',
            'status',
        ]);
        
        $alertJobData = [
            'user_id' => Auth::guard('api-user')->user()->id,
            'position' => !empty($requestData['position']) ? $requestData['position'] : null,
            'salary_min' => !empty($requestData['salary_min']) ? $requestData['salary_min'] : null,
            'rank' => !empty($requestData['rank']) ? $requestData['rank'] : null,
            'province' => !empty($requestData['province']) ? $requestData['province'] : null,
            'occupation' => !empty($requestData['occupation']) ? $requestData['occupation'] : null,
            'industry' => !empty($requestData['industry']) ? $requestData['industry'] : null,
            'interval' => !empty($requestData['interval']) ? $requestData['interval'] : null,
            'notification_by' => !empty($requestData['notification_by']) ? $requestData['notification_by'] : null,
            'status' => !empty($requestData['status']) ? config('custom.status.active') : config('custom.status.lock'),
        ];

        $alertJob =  $this->repository->create($alertJobData);


        return [true, $alertJob, 'Success'];

    }

    public function detail($id) {
        $alertJob = AlertJob::where('id', $id)
                            ->where('user_id', Auth::guard('api-user')->user()->id)
                            ->first();
        
        $alertJob = $this->convertData([$alertJob]);

        if (empty($alertJob)) {
            return [false, [], 'Thông báo việc làm không tồn tại'];
        }

        return [true, $alertJob[0], 'Success'];
    }

    public function update(Request $request, $id)
    {
        $requestData = $request->only([
            'position',
            'salary_min',
            'rank',
            'province',
            'occupation',
            'industry',
            'interval',
            'notification_by',
            'status',
        ]);

        $alertJob = AlertJob::where('id', $id)
                            ->where('user_id', Auth::guard('api-user')->user()->id)
                            ->first();

        if (empty($alertJob)) {
            return [false, [], 'Thông báo việc làm không tồn tại'];
        }

        $alertJob['position'] = $requestData['position'];
        $alertJob['salary_min'] = $requestData['salary_min'];
        $alertJob['rank'] = $requestData['rank'];
        $alertJob['province'] = $requestData['province'];
        $alertJob['occupation'] = $requestData['occupation'];
        $alertJob['industry'] = $requestData['industry'];
        $alertJob['interval'] = $requestData['interval'];
        $alertJob['notification_by'] = $requestData['notification_by'];
        $alertJob['status'] = $requestData['status'];

        $alertJob->save();

        return [true, $alertJob, 'Success'];
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $isDelete = AlertJob::where('id', $id)
                            ->where('user_id', Auth::guard('api-user')->user()->id)
                            ->delete();
        
        if ($isDelete) {
            return [true, 'Success'];
        }
        
        return [false, 'Có lỗi sảy ra. Vui lòng kiểm tra lại.'];
    }

    public function convertData($alertJob) {
        $listProvince = [];
        $listOccupation = [];
        $listIndustry = [];

        foreach ($alertJob as $item) {
            // $item = $item->rank;
            if (!empty($item['province'])) {
                $listProvince = array_merge($listProvince, $item['province']);
            }

            if (!empty($item['occupation'])) {
                $listOccupation = array_merge($listOccupation, $item['occupation']);
            }

            if (!empty($item['industry'])) {
                $listIndustry = array_merge($listIndustry, $item['industry']);
            }
        }

        $provinceArr = [];
        $occupationArr = [];
        $industryArr = [];

        if (!empty($listProvince)) {
            $provinces = Province::whereIn('id', $listProvince)->select('id', 'name')->get()->toArray();
            foreach ($provinces as $item) {
                $provinceArr[$item['id']] = $item;
            }
        }

        if (!empty($listOccupation)) {
            $occupations = Occupation::whereIn('id', $listOccupation)->select('id', 'name')->get()->toArray();
            foreach ($occupations as $item) {
                $occupationArr[$item['id']] = $item;
            }
        }

        if (!empty($listIndustry)) {
            $industries = Industry::whereIn('id', $listIndustry)->select('id', 'name')->get()->toArray();
            foreach ($industries as $item) {
                $industryArr[$item['id']] = $item;
            }
        }

        foreach ($alertJob as $key => $alertJobItem) {
            $provinceData = [];
            foreach ($alertJobItem['province'] as $item) {
                $provinceData[] = $provinceArr[$item];
            }
            $alertJobItem['provinceData'] = $provinceData;

            $occupationData = [];
            foreach ($alertJobItem['occupation'] as $item) {
                $occupationData[] = $occupationArr[$item];
            }
            $alertJobItem['occupationData'] = $occupationData;

            $industryData = [];
            foreach ($alertJobItem['industry'] as $item) {
                $industryData[] = $industryArr[$item];
            }
            $alertJobItem['industryData'] = $industryData;
        }

        return $alertJob;
    }
}