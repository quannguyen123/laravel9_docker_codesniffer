<?php

namespace App\Services;

use App\Repositories\DistrictRepository;

class DistrictService {
    /**
     * function contructer
     *
     * @param TagRepository $repository
     */
    public function __construct(
        protected readonly DistrictRepository $repository
    ) {
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function listDistrictByProvince($request)
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

        if (!empty($request->provinceId)) {
            $filters['provinceId'] = $request->provinceId;
        }

        return $this->repository->listDistrictByProvince($filters);
    }
}