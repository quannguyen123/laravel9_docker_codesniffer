<?php

namespace App\Services;

use App\Repositories\ProvinceRepository;

class ProvinceService {
    /**
     * function contructer
     *
     * @param TagRepository $repository
     */
    public function __construct(
        protected readonly ProvinceRepository $repository
    ) {
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function listProvince($request)
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

        return $this->repository->listProvince($filters);
    }
}