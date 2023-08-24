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
    /**
     * @OA\Get(
     *     path="/api/partner/partner-management/index",
     *     summary="Danh sách partner",
     *     tags={"Partner-Management Partner"},
     *     security={{"bearer":{}}},
     *     @OA\Response(response="200", description="An example endpoint")
     * )
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

    /**
     * @OA\Get(
     *     path="/api/partner/partner-management/detail/{id}",
     *     summary="Thông tin chi tiết partner",
     *     tags={"Partner-Management Partner"},
     *     security={{"bearer":{}}},
     *     @OA\Parameter(in="path", name="id", required=true, description="Id partner", @OA\Schema(type="string")),
     *     @OA\Response(response="200", description="An example endpoint")
     * )
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

    /**
     * @OA\Post(
     *     path="/api/partner/partner-management/update/{id}",
     *     tags={"Partner-Management Partner"},
     *     summary="Cập nhật thông tin partner",
     *     description="",
     *     security={{"bearer":{}}},
     *     operationId="update",
     *     @OA\Parameter(in="path", name="id", required=true, description="Id partner", @OA\Schema(type="string")),
     *     @OA\RequestBody(
     *          @OA\JsonContent(),
     *          @OA\MediaType(
     *              mediaType="multipart/form-data",
     *              @OA\Schema(
     *                  type="object",
     *                  required={"first_name", "last_name", "role"},
     *                  @OA\Property(property="first_name", type="string", format="string"),
     *                  @OA\Property(property="last_name", type="string", format="string"),
     *                  @OA\Property(property="role", type="integer", format="int64", description="3 or 4")
     *              )
     *          ),
     *     ),
     *     @OA\Response(response="200", description="An example endpoint")
     * )
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
    
    /**
     * @OA\Get(
     *     path="/api/partner/partner-management/change-status/{id}/{status}",
     *     summary="Thay đổi trạng thái partner",
     *     tags={"Partner-Management Partner"},
     *     security={{"bearer":{}}},
     *     @OA\Parameter(in="path", name="id", required=true, description="Id partner", @OA\Schema(type="integer")),
     *     @OA\Parameter(in="path", name="status", required=true, description="Trạng thái (lock or active)", @OA\Schema(type="string")),
     *     @OA\Response(response="200", description="An example endpoint")
     * )
     */
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

    /**
     * @OA\Get(
     *     path="/api/partner/partner-management/choose-partner-admin/{id}",
     *     summary="Thay đổi admin của công ty",
     *     tags={"Partner-Management Partner"},
     *     security={{"bearer":{}}},
     *     @OA\Parameter(in="path", name="id", required=true, description="Id partner", @OA\Schema(type="string")),
     *     @OA\Response(response="200", description="An example endpoint")
     * )
     */
    public function choosePartnerAdmin($id) {
        try {
            DB::beginTransaction();
            [$status, $data, $mess] = $this->partnerManagementService->choosePartnerAdmin($id);
            DB::commit();
            
            $res['partner'] = $data;
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

    /**
     * @OA\Post(
     *     path="/api/partner/partner-management/send-mail-invite",
     *     tags={"Partner-Management Partner"},
     *     summary="Gửi link mời làm partner",
     *     description="",
     *     security={{"bearer":{}}},
     *     @OA\RequestBody(
     *          @OA\JsonContent(),
     *          @OA\MediaType(
     *              mediaType="multipart/form-data",
     *              @OA\Schema(
     *                  type="object",
     *                  required={"email", "first_name", "last_name", "role"},
     *                  @OA\Property(property="first_name", type="string", format="string"),
     *                  @OA\Property(property="last_name", type="string", format="string"),
     *                  @OA\Property(property="email", type="string", format="string"),
     *                  @OA\Property(property="role", type="integer", format="int", description="3 or 4")
     *              )
     *          ),
     *     ),
     *     @OA\Response(response="200", description="An example endpoint")
     * )
     */
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

    /**
     * @OA\Post(
     *     path="/api/partner/partner-management/accept-invite",
     *     tags={"Partner-Management Partner"},
     *     summary="Chấp nhận lời mời partner",
     *     description="",
     *     @OA\RequestBody(
     *          @OA\JsonContent(),
     *          @OA\MediaType(
     *              mediaType="multipart/form-data",
     *              @OA\Schema(
     *                  type="object",
     *                  required={"email", "token", "password", "c_password"},
     *                  @OA\Property(property="email", type="string", format="string"),
     *                  @OA\Property(property="token", type="string", format="string"),
     *                  @OA\Property(property="password", type="string", format="string"),
     *                  @OA\Property(property="c_password", type="string", format="string")
     *              )
     *          ),
     *     ),
     *     @OA\Response(response="200", description="An example endpoint")
     * )
     */
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

    /**
     * @OA\Post(
     *     path="/api/partner/partner-management/reject-invite",
     *     tags={"Partner-Management Partner"},
     *     summary="Hủy lời mời partner",
     *     description="",
     *     @OA\RequestBody(
     *          @OA\JsonContent(),
     *          @OA\MediaType(
     *              mediaType="multipart/form-data",
     *              @OA\Schema(
     *                  type="object",
     *                  required={"email", "token"},
     *                  @OA\Property(property="email", type="string", format="string"),
     *                  @OA\Property(property="token", type="string", format="string")
     *              )
     *          ),
     *     ),
     *     @OA\Response(response="200", description="An example endpoint")
     * )
     */
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

    /**
     * @OA\Get(
     *     path="/api/partner/partner-management/list-invite-partner",
     *     summary="Danh sách lời mời",
     *     tags={"Partner-Management Partner"},
     *     security={{"bearer":{}}},
     *     @OA\Response(response="200", description="An example endpoint")
     * )
     */
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

    /**
     * @OA\Post(
     *     path="/api/partner/partner-management/cancel-invite-partner",
     *     tags={"Partner-Management Partner"},
     *     summary="Hủy lời mời partner",
     *     description="",
     *     security={{"bearer":{}}},
     *     @OA\RequestBody(
     *          @OA\JsonContent(),
     *          @OA\MediaType(
     *              mediaType="multipart/form-data",
     *              @OA\Schema(
     *                  type="object",
     *                  required={"partner_invite_ids[]"},
     *                  @OA\Property(property="partner_invite_ids[]", type="string"),
     *              )
     *          ),
     *     ),
     *     @OA\Response(response="200", description="An example endpoint")
     * )
     */
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
