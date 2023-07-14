<?php

namespace App\Services;

use App\Repositories\WelfareRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WelfareService {
    /**
     * function contructer
     *
     * @param WelfareRepository $repository
     */
    public function __construct(
        protected readonly WelfareRepository $repository
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

        $image_name = '';
        if (!empty($request->file('icon'))) {
            $icon = $request->file('icon');
            $image_name = time().'.'.$icon->getClientOriginalExtension();
            $destinationPath = public_path('/images/icon');
            $icon->move($destinationPath, $image_name);
        }
        
        $welfare = [
            'name' => $requestData['name'],
            'icon' => $image_name,
            'status' => config('custom.status.active'),
            // 'created_by' => Auth::guard('api-admin')->user()->id
        ];

        return $this->repository->create($welfare);
    }

    public function changeStatus($welfare, $status) {
        $welfare['status'] = config('custom.status.' . $status);
        $welfare->save();

        return [];
    }

    public function update(Request $request, $welfare)
    {
        $requestData = $request->only(['name', 'slug', 'parent_id']);
        
        $welfare['name'] = $requestData['name'];
        // $welfare['updated_by'] = Auth::guard('api-admin')->user()->id;

        if (!empty($request->file('icon'))) {
            removeImage('icon', $welfare['icon']);

            $icon = $request->file('icon');
            $image_name = time().'.'.$icon->getClientOriginalExtension();
            $destinationPath = public_path('/images/icon');
            $icon->move($destinationPath, $image_name);
            $welfare['icon'] = $image_name;
        }

        $welfare->save();

        return $welfare;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($welfare)
    {
        // $welfare['deleted_by'] = Auth::guard('api-admin')->user()->id;
        $welfare['deleted_at'] = date("Y-m-d H:i:s", time());
        $welfare->save();

        return [];
    }
}