<?php

namespace App\Http\Controllers\Api\Partner;

use App\Http\Controllers\BaseController;
use App\Models\Company;
use App\Models\CompanyJobTitle;
use App\Models\CompanyOccupation;
use App\Models\CompanyRank;
use App\Models\CompanyUser;
use App\Models\RecruitmentRank;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class AuthController extends BaseController
{
    public function register(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'first_name' => 'required',
                'last_name' => 'required',
                'number_phone' => 'required',
                'email' => 'required|email|unique:users,email',
                'password' => 'required',
                'c_password' => 'required|same:password',
                'company_name' => 'required',
                'occupations' => 'required|array',
                'company_address' => 'required',
                'purpose' => 'required',
                'job_titles' => 'required|array',
                'ranks' => 'required|array',
                'sum_budget_recruitment' => 'required',
            ]);
            
            if($validator->fails()){
                return $this->sendError('Validation Error.', $validator->errors());       
            }

            $requestData = $request->only([
                'first_name',
                'last_name',
                'number_phone',
                'email',
                'password',
                'c_password',
                'company_name',
                'occupations',
                'company_address',
                'purpose',
                'job_titles', // vị trí tuyển dụng
                'occupations',
                'ranks',
                'sum_budget_recruitment',
            ]);
    
            $requestData['password'] = bcrypt($requestData['password']);

            DB::beginTransaction();
            /**
             * start create user và phân quyền
             */
            $userData = [
                'first_name' => $requestData['first_name'],
                'last_name' => $requestData['last_name'],
                'number_phone' => $requestData['number_phone'],
                'email' => $requestData['email'],
                'password' => $requestData['password'],
                'type' => config('custom.user-type.type-partner'),
                'status' => config('custom.status.active')
            ];
            
            $user = User::create($userData);
            $user->assignRole('partner');
            /**
             * end create user
             */

            /**
             * start create company
             */

            $dataCompany = [
                'name' => $requestData['first_name'],
                'company_address' => $requestData['company_address'],
                'purpose' => $requestData['purpose'],
                'sum_budget_recruitment' => $requestData['sum_budget_recruitment'],
                'created_by' => $user->id,
            ];
            $company = Company::create($dataCompany);

            if(!empty($requestData['job_titles'])) {
                $jobTitle = [];
                foreach($requestData['job_titles'] as $jobTitleId) {
                    $jobTitle[] = [
                        'company_id' => $company->id,
                        'job_title_id' => $jobTitleId,
                    ];
                }

                CompanyJobTitle::insert($jobTitle);
            }

            // company occupation
            if(!empty($requestData['occupations'])) {
                $companyOccupaiton = [];
                foreach($requestData['occupations'] as $itemId) {
                    $companyOccupaiton[] = [
                        'company_id' => $company->id,
                        'occupation_id' => $itemId,
                    ];
                }

                CompanyOccupation::insert($companyOccupaiton);
            }

            // company rank
            if(!empty($requestData['ranks'])) {
                $recruitmentRank = [];
                foreach($requestData['ranks'] as $itemId) {
                    $recruitmentRank[] = [
                        'company_id' => $company->id,
                        'rank' => $itemId,
                    ];
                }

                RecruitmentRank::insert($recruitmentRank);
            }

            // company_user
            $companyUser = [
                'company_id' => $company->id,
                'user_id' => $user->id
            ];
            CompanyUser::create($companyUser);

            /**
             * end create company
             */
            DB::commit();

            $success['token'] =  $user->createToken('MyApp-Partner')->accessToken;
            $success['name'] =  $user->name;
    
            return $this->sendResponse($success, 'Partner register successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->sendError($e->getMessage());
        }
    }
    
    /**
     * Login api
     *
     * @return \Illuminate\Http\Response
     */
    public function login(Request $request)
    {
        try {
            if((Auth::attempt(['email' => $request->email, 'password' => $request->password, 'type' => config('custom.user-type.type-partner')]))){
                $userId = Auth::user()->id;
                $user = User::find($userId);

                if ($user->status == config('custom.status.lock')) {
                    Auth::user()->tokens->each(function($token, $key) {
                        $token->delete();
                    });

                    return $this->sendResponse([], 'Tài khoản đã bị khóa. Vui lòng liên hệ admin để được hỗ trợ');
                }
                
                $success['token'] = $user->createToken('MyApp-Partner')->accessToken;
                $success['name'] = $user->name;
                
                return $this->sendResponse($success, 'Partner login successfully.');
            } 
            else{ 
                return $this->sendError('Unauthorised.', ['error'=>'Unauthorised']);
            }
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->sendError($e->getMessage());
        }
    }

    public function logout() {
        try {
            Auth::user()->tokens->each(function($token, $key) {
                $token->delete();
            });

            $success = [];
            return $this->sendResponse($success, 'Partner logout success');
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage());
        }
    }
}
