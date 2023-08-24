<?php

namespace App\Services\Admins;

use App\Jobs\SendMailInvitePartner;
use App\Models\CompanyUser;
use App\Models\User;
use App\Repositories\PartnerInviteRepository;
use App\Repositories\PartnerRepository;
use App\Repositories\UserRepository;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PartnerManagementService {
    /**
     * function contructer
     *
     * @param UserRepository $repository
     */
    public function __construct(
        protected readonly PartnerRepository $repository,
        protected readonly PartnerInviteRepository $partnerInviteRepository,
    ) {
    }

    public function checkRole() {
        $userId = Auth::guard('api-user')->user()->id;
        $roleUser = User::find($userId)->getRoleNames()->toArray();

        if (!(in_array('partner', $roleUser) && in_array('partner_admin', $roleUser))) {
            return false;
        }

        return true;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($request)
    {
        $companyId = Auth::guard('api-user')->user()->company[0]['id'];
        $partners = User::leftJoin('company_user', 'company_user.user_id', '=', 'users.id')
                        ->select('users.*')
                        ->where('company_user.company_id', $companyId)->get()->toArray();

        return [true, $partners, 'Success'];
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function detail($id)
    {
        $statusCheckRole = $this->checkRole();
        if (!$statusCheckRole) {
            return [false, [], 'Tài khoản không có quyền thực hiện yêu cầu'];
        }
        
        $companyId = Auth::guard('api-user')->user()->company[0]['id'];
        $partner = User::leftJoin('company_user', 'company_user.user_id', '=', 'users.id')
                        ->select('users.*')
                        ->where('company_user.user_id', $id)
                        ->where('company_user.company_id', $companyId)->first();

        return [true, $partner, 'Success'];
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update($request, $id)
    {
        $statusCheckRole = $this->checkRole();
        if (!$statusCheckRole) {
            return [false, [], 'Tài khoản không có quyền thực hiện yêu cầu'];
        }

        $companyId = Auth::guard('api-user')->user()->company[0]['id'];
        $partnerCheck = User::leftJoin('company_user', 'company_user.user_id', '=', 'users.id')
                        ->select('users.*')
                        ->where('company_user.user_id', $id)
                        ->where('company_user.company_id', $companyId)->first();

        if (empty($partnerCheck)) {
            return [false, [], 'Không tồn tại partner'];
        }

        $partner = User::find($id);

        if (isset($request['first_name'])) {
            $partner['first_name'] = $request['first_name'];
        }
        if (isset($request['last_name'])) {
            $partner['last_name'] = $request['last_name'];
        }

        if (isset($request['role'])) {
            if($request['role'] == config('custom.user-role.role-hr')) {
                $partner->removeRole('partner_accountant');
                $partner->assignRole('partner_hr');
            }

            if($request['role'] == config('custom.user-role.role-accountant')) {
                $partner->removeRole('partner_hr');
                $partner->assignRole('partner_accountant');
            }
        }

        $partner->save();
        $res['partner'] = $partner;
        $res['token'] = $partner->createToken('MyApp-Partner')->accessToken;

        return [true, $partner, 'Success'];
    }

    public function changeStatus($id, $status) {
        $statusCheckRole = $this->checkRole();
        if (!$statusCheckRole) {
            return [false, [], 'Tài khoản không có quyền thực hiện yêu cầu'];
        }

        $companyId = Auth::guard('api-user')->user()->company[0]['id'];
        $partnerCheck = User::leftJoin('company_user', 'company_user.user_id', '=', 'users.id')
                        ->select('users.*')
                        ->where('company_user.user_id', $id)
                        ->where('company_user.company_id', $companyId)->first();

        if (empty($partnerCheck)) {
            return [false, [], 'Không tồn tại partner'];
        }

        $partner = $this->repository->update([
            'status' => config('custom.status.' . $status),
            'updated_by' => Auth::guard('api-user')->user()->id
        ], $id);

        return [true, $partner, 'Success'];
    }

    public function choosePartnerAdmin($id) {
        $statusCheckRole = $this->checkRole();

        $userId = Auth::guard('api-user')->user()->id;
 
        if ($userId == $id) {
            return [false, [], 'Không thể cài đặt quyền cho chính mình'];
        }

        if (!$statusCheckRole) {
            return [false, [], 'Tài khoản không có quyền thực hiện yêu cầu'];
        }

        $companyId = Auth::guard('api-user')->user()->company[0]['id'];
        $partnerCheck = User::leftJoin('company_user', 'company_user.user_id', '=', 'users.id')
                        ->select('users.*')
                        ->where('company_user.user_id', $id)
                        ->where('company_user.company_id', $companyId)->first();

        if (empty($partnerCheck)) {
            return [false, [], 'Không tồn tại partner'];
        }

        $partner = User::find($id);
        $partner->removeRole('partner_hr');
        $partner->removeRole('partner_accountant');

        $partner->assignRole('partner_admin');

        // Xóa quyền admin của user hiện tại
        $partnerOldId = Auth::guard('api-user')->user()->id;
        $partnerOld = User::find($partnerOldId);
        $partnerOld->removeRole('partner_admin');

        return [true, $partner, 'Success'];
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        return $this->repository->delete($id);
    }

    public function sendMailInvite($request) {
        $statusCheckRole = $this->checkRole();
        if (!$statusCheckRole) {
            return [false, [], 'Tài khoản không có quyền thực hiện yêu cầu'];
        }

        $requestData = $request->only([
            'first_name',
            'last_name',
            'email',
            'role',
        ]);

        $userId = Auth::guard('api-user')->user()->id;
        $roleUser = User::find($userId)->getRoleNames()->toArray();

        $partner = User::where('email', $requestData['email'])->first();
        if (!empty($partner)) {
            return [false, [], 'Người dùng đang là partner của công ty khác. Vui lòng kiểm tra lại'];
        }

        if (Auth::guard('api-user')->user()->type != config('custom.user-type.type-partner') || !in_array('partner', array_values($roleUser))) {
            return [false, [], 'Bạn không có quyền thêm partner'];
        }

        if (!in_array($requestData['role'], [3, 4])) {
            return [false, [], 'Role không hợp lệ'];
        }

        $partnerInvite = [
            'first_name' => $requestData['first_name'],
            'last_name' => $requestData['last_name'],
            'email' => $requestData['email'],
            'role' => $requestData['role'],
            'company_id' => Auth::guard('api-user')->user()->company[0]['id'],
            'token' => Str::random(60),
            'created_by' => Auth::guard('api-user')->user()->id,
            'expiration_date' => Carbon::today()->addDays(7)
        ];

        $partnerInviteData = $this->partnerInviteRepository->create($partnerInvite);

        $mailData = [
            'author' => Auth::guard('api-user')->user()->email,
            'company_name' => Auth::guard('api-user')->user()->company[0]['name'],
            'name' => $partnerInviteData['first_name'] . ' ' .$partnerInviteData['last_name'],
            'email' => $partnerInviteData['email'],
            'token' => $partnerInviteData['token'],
            'url' => env('DOMAIN_FRONTEND') . 'link-invite' . '?' . 'token=' . $partnerInviteData['token'] . '&email=' . $partnerInviteData['email']
        ];

        SendMailInvitePartner::dispatch($mailData)->delay((now()->addMilliseconds(10)));

        return [true, $mailData, 'Success'];
    }

    public function accessInvite($request) {
        $requestData = $request->only([
            'token',
            'email',
            'password',
            'c_password',
        ]);

        $whereArr = [
            'email' => $requestData['email'],
            'token' => $requestData['token'],
            ['expiration_date','DATE >=', Carbon::today()]
        ];
        $accessInvite = $this->partnerInviteRepository->findWhere($whereArr)->first();

        if (empty($accessInvite)) {
            return [false, [], 'Không hợp lệ'];
        }

        DB::beginTransaction();
        /**
         * start create user và phân quyền
         */

        $partner = User::where('email', $requestData['email'])->first();
        if (empty($partner)) {
            $userData = [
                'first_name' => $accessInvite['first_name'],
                'last_name' => $accessInvite['last_name'],
                'email' => $accessInvite['email'],
                'password' => bcrypt($requestData['password']),
                'type' => config('custom.user-type.type-partner'),
                'status' => config('custom.status.active')
            ];
            
            $user = User::create($userData);
            if ($accessInvite['role'] == config('custom.user-role.role-hr')) {
                $user->assignRole('partner');
                $user->assignRole('partner_hr');
            }else if($accessInvite['role'] == config('custom.user-role.role-accountant')) {
                $user->assignRole('partner');
                $user->assignRole('partner_accountant');
            }
            /**
             * end create user
             */
    
            // company_user
            $companyUser = [
                'company_id' => $accessInvite['company_id'],
                'user_id' => $user->id
            ];
            CompanyUser::create($companyUser);

            $res['token'] =  $user->createToken('MyApp-Partner')->accessToken;
            $mess = 'Success';
        } else {
            $res =  [];
            $mess = 'Người dùng đang là partner của công ty khác. Vui lòng kiểm tra lại';
        }

        $accessInvite->delete();
        /**
         * end create company
         */
        DB::commit();

        return [true, $res, $mess];
    }

    
    public function rejectInvite($request) {
        $requestData = $request->only([
            'token',
            'email'
        ]);

        $whereArr = [
            'email' => $requestData['email'],
            'token' => $requestData['token']
        ];
        $accessInvite = $this->partnerInviteRepository->findWhere($whereArr)->first();

        if (empty($accessInvite)) {
            return [false, [], 'Không hợp lệ'];
        }

        $accessInvite->delete();

        return [true, [], 'Success'];
    }

    public function listInvitePartner() {
        $statusCheckRole = $this->checkRole();
        if (!$statusCheckRole) {
            return [false, [], 'Tài khoản không có quyền thực hiện yêu cầu'];
        }

        $whereArr = [
            'company_id' => Auth::guard('api-user')->user()->company[0]['id'],
        ];

        $select = [
            'id',
            'email',
            'first_name',
            'last_name',
            'role',
            'token',
            'expiration_date',
            'company_id',
            'created_by'
        ];

        $partnerInvite = $this->partnerInviteRepository->findWhere($whereArr, $select);

        return [true, $partnerInvite, 'Success'];
    }

    public function cancelInvitePartner($request) {
        $partnerInviteIds = $request['partner_invite_ids'];
        
        $statusCheckRole = $this->checkRole();
        if (!$statusCheckRole) {
            return [false, [], 'Tài khoản không có quyền thực hiện yêu cầu'];
        }

        $where = [
            ['id', 'IN', $partnerInviteIds],
            'company_id' => Auth::guard('api-user')->user()->company[0]['id']
        ];
        $partnerInvite = $this->partnerInviteRepository->findWhere($where);

        if (count($partnerInviteIds) == count($partnerInvite)) {
            $this->partnerInviteRepository->deleteWhere($where);

            return [true, [], 'Success'];
        } else {
            return [false, [], 'Có lỗi sảy ra'];
        }
    }

    public function reInvitePartner($request) {

    }
}