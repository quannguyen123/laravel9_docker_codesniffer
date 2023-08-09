<?php

namespace App\Http\Controllers\Api\Partner;

use App\Http\Controllers\BaseController;
use App\Services\Admins\PartnerManagementService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class PartnerManagementController extends BaseController
{
    public function __construct(
        protected PartnerManagementService $partnerManagementService
    ) {
        $this->partnerManagementService = $partnerManagementService;
    }
    
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        try {
            [$status, $partner, $mess] = $this->partnerManagementService->index($request);

            $res['partners'] = $partner;

            return $this->sendResponse($res, $mess);
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function detail($id)
    {
        try {
            [$status, $partner, $mess] = $this->partnerManagementService->detail($id);

            $res['partner'] = $partner;

            return $this->sendResponse($res, $mess);
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage());
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        try {
            $validator = Validator::make($request->all(), [
                'first_name' => 'nullable',
                'last_name' => 'nullable',
                'role' => 'nullable|numeric',
            ]);
    
            if($validator->fails()){
                return $this->sendError('Validation Error.', $validator->errors());  
            }
    
            [$status, $res, $mess] = $this->partnerManagementService->update($request, $id);
    
            if ($status) {
                return $this->sendResponse($res, $mess) ;
            } else {
                return $this->sendError($mess);
            }
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->sendError($e->getMessage());
        }
    }
    
    public function changeStatus($id, $status)
    {
        try {
            if (!in_array($status, array_keys(config('custom.status')))) {
                return $this->sendError('Status không đúng');
            }

            [$status, $data, $mess] = $this->partnerManagementService->changeStatus($id, $status);
            
            $res['partner'] = $data;
            if ($status) {
                return $this->sendResponse($res, $mess) ;
            } else {
                return $this->sendError($mess);
            }
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage());
        }
    }

    public function choosePartnerAdmin($id) {
        try {
            [$status, $data, $mess] = $this->partnerManagementService->choosePartnerAdmin($id);
            
            $res['partner'] = $data;
            if ($status) {
                return $this->sendResponse($res, $mess) ;
            } else {
                return $this->sendError($mess);
            }
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
        //
    }

    public function sendMailInvite(Request $request) {
        try {
            $validator = Validator::make($request->all(), [
                'first_name' => 'required',
                'last_name' => 'required',
                'email' => 'required|email',
                'role' => 'required|numeric'
            ]);
    
            if($validator->fails()){
                return $this->sendError('Validation Error.', $validator->errors());       
            }
    
            [$status, $res, $mess] = $this->partnerManagementService->sendMailInvite($request);

            if (!$status) {
                return $this->sendError($mess);
            }
    
            return $this->sendResponse($res, $mess);
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->sendError($e->getMessage());
        }
    }

    public function accessInvite(Request $request) {
        try {
            $validator = Validator::make($request->all(), [
                'token' => 'required',
                'email' => 'required|email',
                'password' => 'required',
                'c_password' => 'required|same:password',
            ]);
    
            if($validator->fails()){
                return $this->sendError('Validation Error.', $validator->errors());       
            }
    
            [$status, $res, $mess] = $this->partnerManagementService->accessInvite($request);

            if ($status) {
                return $this->sendResponse($res, $mess);
            } else {
                return $this->sendError($mess);
            }
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->sendError($e->getMessage());
        }
    }

    public function rejectInvite(Request $request) {
        try {
            $validator = Validator::make($request->all(), [
                'token' => 'required',
                'email' => 'required|email'
            ]);
    
            if($validator->fails()){
                return $this->sendError('Validation Error.', $validator->errors());       
            }
    
            [$status, $res, $mess] = $this->partnerManagementService->rejectInvite($request);
    
            if ($status) {
                return $this->sendResponse($res, $mess);
            } else {
                return $this->sendError($mess);
            }
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->sendError($e->getMessage());
        }
    }

    public function listInvitePartner() {
        try {
            [$status, $res, $mess] = $this->partnerManagementService->listInvitePartner();
    
            if ($status) {
                return $this->sendResponse($res, $mess);
            } else {
                return $this->sendError($mess);
            }
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->sendError($e->getMessage());
        }
    }

    public function cancelInvitePartner(Request $request) {
        try {
            $validator = Validator::make($request->all(), [
                'partner_invite_ids' => 'required|array',
                'partner_invite_ids.*' => 'numeric|exists:partner_invite,id',
            ]);
    
            if($validator->fails()){
                return $this->sendError('Validation Error.', $validator->errors());       
            }

            [$status, $res, $mess] = $this->partnerManagementService->cancelInvitePartner($request);
    
            if ($status) {
                return $this->sendResponse($res, $mess);
            } else {
                return $this->sendError($mess);
            }
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->sendError($e->getMessage());
        }
    }

    public function reInvitePartner(Request $request) {
        try {
            $validator = Validator::make($request->all(), [
                'partner_invite_ids' => 'required|array',
                'partner_invite_ids.*' => 'numeric|exists:partner_invite,id',
            ]);
    
            if($validator->fails()){
                return $this->sendError('Validation Error.', $validator->errors());       
            }

            [$status, $res, $mess] = $this->partnerManagementService->reInvitePartner($request);
    
            if ($status) {
                return $this->sendResponse($res, $mess);
            } else {
                return $this->sendError($mess);
            }
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->sendError($e->getMessage());
        }
    }
}
