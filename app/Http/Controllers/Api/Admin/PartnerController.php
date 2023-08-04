<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\BaseController;
use App\Services\PartnerService;
use Illuminate\Http\Request;

class PartnerController extends BaseController
{
    public function __construct(
        protected PartnerService $partnerService
    ) {
        $this->partnerService = $partnerService;
    }
    
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        try {
            $userAll = $this->partnerService->index($request);

            $res['userAll'] = $userAll;

            return $this->sendResponse($res, 'Success.');
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function detail($id)
    {
        try {
            [$status, $user, $mess] = $this->partnerService->detail($id);

            $res['user'] = $user;

            return $this->sendResponse($res, $mess);
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage());
        }
    }

    public function changeStatus($id, $status) {
        try {
            if (!in_array($status, array_keys(config('custom.status')))) {
                return $this->sendError('Status không đúng');
            }

            [$status, $data, $mess] = $this->partnerService->changeStatus($id, $status);
            
            return $this->sendResponse($data, $mess) ;
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            [$status, $data, $mess] = $this->partnerService->destroy($id);
            
            return $this->sendResponse($data, $mess);
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage());
        }
    }
}
