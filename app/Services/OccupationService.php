<?php

namespace App\Services;

use App\Models\Occupation;
use App\Repositories\OccupationRepository;
use App\Repositories\UserRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class OccupationService {
    /**
     * function contructer
     *
     * @param OccupationRepository $repository
     */
    public function __construct(
        protected readonly OccupationRepository $repository
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

    public function publicSearchOccupation($request) {
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

        return $this->repository->publicSearchOccupation($filters);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $requestData = $request->only(['name', 'slug', 'parent_id']);

        $occupation = [
            'name' => $requestData['name'],
            'slug' => empty($requestData['slug']) ? createSlug($requestData['name']) : createSlug($requestData['slug']),
            'parent_id' => empty($requestData['parent_id']) ? null : $requestData['parent_id'],
            'status' => config('custom.status.active'),
            'created_by' => Auth::guard('api-admin')->user()->id
        ];

        return $this->repository->create($occupation);
    }

    public function changeStatus($occupation, $status) {
        $occupation['status'] = config('custom.status.' . $status);
        $occupation->save();

        return [];
    }

    public function update(Request $request, $occupation)
    {
        $requestData = $request->only(['name', 'slug', 'parent_id']);
        
        $occupation['name'] = $requestData['name'];
        $occupation['slug'] = !empty($requestData['slug']) ? $requestData['slug'] : $occupation->slug;
        $occupation['parent_id'] = !empty($requestData['parent_id']) ? $requestData['parent_id'] : $occupation->parent_id;
        $occupation['updated_by'] = Auth::guard('api-admin')->user()->id;

        $occupation->save();

        return $occupation;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($occupation)
    {
        $occupation['deleted_by'] = Auth::guard('api-admin')->user()->id;
        $occupation['deleted_at'] = date("Y-m-d H:i:s", time());
        $occupation->save();

        return [];
    }
}