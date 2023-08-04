<?php

namespace App\Services;

use App\Repositories\PartnerRepository;
use App\Repositories\UserRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PartnerService {
    /**
     * function contructer
     *
     * @param UserRepository $repository
     */
    public function __construct(
        protected readonly PartnerRepository $repository
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

    public function detail($id)
    {
        $user = $this->repository->where('type', config('custom.user-type.type-partner'))->where('id', $id)->first();

        if (empty($user)) {
            return [true, [], 'User không tồn tại'];
        }
        
        return [true, $user, 'Success'];
    }

    public function changeStatus($id, $status) {
        $user = $this->repository->where('type', config('custom.user-type.type-partner'))->where('id', $id)->first();

        if (empty($user)) {
            return [true, [], 'User không tồn tại'];
        }

        $user = $this->repository->update([
            'status' => config('custom.status.' . $status),
            'updated_by' => Auth::guard('api-admin')->user()->id
        ], $id);

        return [true, $user, 'Success'];
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {

        $user = $this->repository->where('type', config('custom.user-type.type-partner'))->where('id', $id)->first();

        if (empty($user)) {
            return [true, [], 'User không tồn tại'];
        }

        $this->repository->update([
            'deleted_at' => date("Y-m-d H:i:s", time()),
            'deleted_by' => Auth::guard('api-admin')->user()->id
        ], $id);

        return [true, [], 'Success'];
    }
}