<?php

namespace App\Services;

use App\Repositories\ServiceRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ServiceService {
    /**
     * function contructer
     *
     * @param JobTitleRepository $repository
     */
    public function __construct(
        protected readonly ServiceRepository $repository
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
        $requestData = $request->only([
            'name',
            'type',
            'price',
            'used_time',
            'description',
            'content',
            'image',
            'note'
        ]);
        $image_name = '';
        if (!empty($request->file('image'))) {
            $icon = $request->file('image');
            $image_name = time().'.'.$icon->getClientOriginalExtension();
            $destinationPath = public_path('/images/service');
            $icon->move($destinationPath, $image_name);
        }

        $service = [
            'name' => $requestData['name'],
            'type' => $requestData['type'],
            'price' => $requestData['price'],
            'used_time' => $requestData['used_time'],
            'description' => $requestData['description'],
            'content' => $requestData['content'],
            'image' => $image_name,
            'note' => $requestData['note'],
            'status' => config('custom.status.active'),
            'created_by' => Auth::guard('api-admin')->user()->id
        ];

        return $this->repository->create($service);
    }

    public function changeStatus($service, $status) {
        $service['status'] = config('custom.status.' . $status);
        $service->save();

        return [];
    }

    public function update(Request $request, $service)
    {
        $requestData = $request->only([
            'name',
            'type',
            'price',
            'used_time',
            'description',
            'content',
            'image',
            'note'
        ]);
        
        $service['name'] = $requestData['name'];
        $service['type'] = $requestData['type'];
        $service['price'] = $requestData['price'];
        $service['used_time'] = $requestData['used_time'];
        $service['description'] = $requestData['description'];
        $service['content'] = $requestData['content'];
        $service['note'] = $requestData['note'];
        $service['updated_by'] = Auth::guard('api-admin')->user()->id;

        if (!empty($request->file('image'))) {
            removeImage('service', $service['image']);

            $icon = $request->file('image');
            $image_name = time().'.'.$icon->getClientOriginalExtension();
            $destinationPath = public_path('/images/service');
            $icon->move($destinationPath, $image_name);
            $service['image'] = $image_name;
        }
        $service->save();

        return $service;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($service)
    {
        $service['deleted_by'] = Auth::guard('api-admin')->user()->id;
        $service['deleted_at'] = date("Y-m-d H:i:s", time());
        $service->save();

        return [];
    }
}