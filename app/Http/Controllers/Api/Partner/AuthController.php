<?php

namespace App\Http\Controllers\Api\Partner;

use App\Http\Controllers\BaseController;
use App\Jobs\SendMailResetPassword;
use App\Models\Company;
use App\Models\CompanyJobTitle;
use App\Models\CompanyOccupation;
use App\Models\CompanyRank;
use App\Models\CompanyUser;
use App\Models\RecruitmentRank;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class AuthController extends BaseController
{
    /**
     * @OA\Post(
     *     path="/api/partner/register",
     *     summary="Đăng ký partner",
     *     tags={"Partner-Authorization"},
     *     description="User register",
     *      @OA\RequestBody(
     *          @OA\JsonContent(),
     *          @OA\MediaType(
     *              mediaType="multipart/form-data",
     *              @OA\Schema(
     *                  type="object",
     *                  required={"email", "password", "c_password", "first_name", "last_name"},
     *                  @OA\Property(property="email", type="string", format="string"),
     *                  @OA\Property(property="password", type="string", format="string"),
     *                  @OA\Property(property="c_password", type="string", format="string"),
     *                  @OA\Property(property="first_name", type="string", format="string"),
     *                  @OA\Property(property="last_name", type="string", format="string")
     *              )
     *          ),
     *     ),
     *     @OA\Response(response="200", description="An example endpoint")
     * )
     */
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
            $user->assignRole('partner_admin');
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
    /**
     * @OA\Post(
     *     path="/api/partner/login",
     *     summary="Partner đăng nhập",
     *     tags={"Partner-Authorization"},
     *     description="User login",
     *      @OA\RequestBody(
     *          @OA\JsonContent(),
     *          @OA\MediaType(
     *              mediaType="multipart/form-data",
     *              @OA\Schema(
     *                  type="object",
     *                  required={"email", "password"},
     *                  @OA\Property(property="email", type="string", format="string", example="vaj08543@nezid.com"),
     *                  @OA\Property(property="password", type="string", format="string", example="12345678")
     *              )
     *          ),
     *     ),
     *     @OA\Response(response="200", description="An example endpoint")
     * )
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

    /**
     * @OA\Post(
     *     path="/api/user/reset-password",
     *     tags={"User-Authorization"},
     *     summary="Gửi link quên password",
     *     description="",
     *     operationId="updatePetWithForm",
     *     @OA\RequestBody(
     *         required=false,
     *         @OA\MediaType(
     *             mediaType="application/x-www-form-urlencoded",
     *             @OA\Schema(
     *                 type="object",
     *                 @OA\Property(
     *                     property="email",
     *                     description="Email của user",
     *                     type="string"
     *                 ),
     *             )
     *         )
     *     ),
     *     @OA\Response(response="200", description="An example endpoint")
     * )
     */
    /**
     * @OA\Post(
     *     path="/api/partner/reset-password",
     *     tags={"Partner-Authorization"},
     *     summary="Gửi link quên password",
     *     description="",
     *     operationId="updatePetWithForm",
     *     @OA\RequestBody(
     *         required=false,
     *         @OA\MediaType(
     *             mediaType="application/x-www-form-urlencoded",
     *             @OA\Schema(
     *                 type="object",
     *                 @OA\Property(
     *                     property="email",
     *                     description="Email của user",
     *                     type="string"
     *                 ),
     *             )
     *         )
     *     ),
     *     @OA\Response(response="200", description="An example endpoint")
     * )
     */
    public function resetPassword(Request $request) {
        try {
            $validator = Validator::make($request->all(), [
                'email' => 'required|email'
            ]);

            if($validator->fails()){
                return $this->sendError('Validation Error.', $validator->errors());       
            }

            $user = User::where('email', $request->email)->first();

            if (empty($user)) {
                return $this->sendResponse([], 'Chúng tôi đã gửi link quên mật khẩu đến mail của bạn. Vui lòng kiểm tra email. 123');
            }

            $user->token = Str::random(60);
            $user->token_expiration_date = Carbon::today()->addDays(7);
            $user->save();
            
            $mailData = [
                'user' => [
                    'email' => $user->email,
                    'name' => $user->first_name . ' ' . $user->last_name,
                    'url' => env('DOMAIN_FRONTEND') . 'partner-reset-password' . '?' . 'token=' . $user->token . '&email=' . $user->email
                ]
            ];

            SendMailResetPassword::dispatch($mailData)->delay((now()->addMilliseconds(10)));
    
            return $this->sendResponse([], 'Chúng tôi đã gửi link quên mật khẩu đến mail của bạn. Vui lòng kiểm tra email.');
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->sendError($e->getMessage());
        }
    }

    /**
     * @OA\Post(
     *     path="/api/user/create-new-password",
     *     tags={"User-Authorization"},
     *     summary="Tạo password mới",
     *     description="",
     *     operationId="createNewPassword",
     *     @OA\RequestBody(
     *         required=false,
     *         @OA\MediaType(
     *             mediaType="application/x-www-form-urlencoded",
     *             @OA\Schema(
     *                 type="object",
     *                 required={"email", "password", "c_password", "token"},
     *                 @OA\Property(property="email", type="string", format="string"),
     *                 @OA\Property(property="password", type="string", format="string"),
     *                 @OA\Property(property="c_password", type="string", format="string"),
     *                 @OA\Property(property="token", type="string", format="string")
     *             )
     *         )
     *     ),
     * )
     */

     /**
     * @OA\Post(
     *     path="/api/partner/create-new-password",
     *     tags={"Partner-Authorization"},
     *     summary="Tạo password mới",
     *     description="",
     *     operationId="createNewPassword",
     *     @OA\RequestBody(
     *         required=false,
     *         @OA\MediaType(
     *             mediaType="application/x-www-form-urlencoded",
     *             @OA\Schema(
     *                 type="object",
     *                 required={"email", "password", "c_password", "token"},
     *                 @OA\Property(property="email", type="string", format="string"),
     *                 @OA\Property(property="password", type="string", format="string"),
     *                 @OA\Property(property="c_password", type="string", format="string"),
     *                 @OA\Property(property="token", type="string", format="string")
     *             )
     *         )
     *     ),
     *     @OA\Response(response="200", description="An example endpoint")
     * )
     */
    public function createNewPassword(Request $request) {
        try {
            $validator = Validator::make($request->all(), [
                'email' => 'required|email',
                'password' => 'required',
                'c_password' => 'required|same:password',
                'token' => 'required'
            ]);
            
            if($validator->fails()){
                return $this->sendError('Validation Error.', $validator->errors());
            }

            $requestData = $request->only([
                'email',
                'password',
                'c_password',
                'token',
            ]);
    
            $requestData['password'] = bcrypt($requestData['password']);

            $user = User::where('token', $requestData['token'])->where('token_expiration_date', '>=', Carbon::today())->first();
            if (empty($user)) {
                return $this->sendResponse([], 'Không hợp lệ vui lòng thử lại');
            }

            $user->password = $requestData['password'];
            $user->token = null;
            $user->token_expiration_date = null;
            $user->save();

            $success['token'] =  $user->createToken('MyApp-Partner')->accessToken;
    
            return $this->sendResponse($success, 'Partner register successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->sendError($e->getMessage());
        }
    }

    /**
     * @OA\Post(
     *     path="/api/user/change-password",
     *     tags={"User-Authorization"},
     *     security={{"bearer":{}}},
     *     summary="Thay đổi password",
     *     description="",
     *     operationId="changePassword",
     *     @OA\RequestBody(
     *         required=false,
     *         @OA\MediaType(
     *             mediaType="application/x-www-form-urlencoded",
     *             @OA\Schema(
     *                 type="object",
     *                 required={"old_password", "password", "c_password"},
     *                 @OA\Property(property="old_password", type="string", format="string"),
     *                 @OA\Property(property="password", type="string", format="string"),
     *                 @OA\Property(property="c_password", type="string", format="string")
     *             )
     *         )
     *     ),
     *     @OA\Response(response="200", description="An example endpoint")
     * )
     */

     /**
     * @OA\Post(
     *     path="/api/partner/change-password",
     *     tags={"Partner-Authorization"},
     *     security={{"bearer":{}}},
     *     summary="Thay đổi password",
     *     description="",
     *     operationId="changePassword",
     *     @OA\RequestBody(
     *         required=false,
     *         @OA\MediaType(
     *             mediaType="application/x-www-form-urlencoded",
     *             @OA\Schema(
     *                 type="object",
     *                 required={"old_password", "password", "c_password"},
     *                 @OA\Property(property="old_password", type="string", format="string"),
     *                 @OA\Property(property="password", type="string", format="string"),
     *                 @OA\Property(property="c_password", type="string", format="string")
     *             )
     *         )
     *     ),
     *     @OA\Response(response="200", description="An example endpoint")
     * )
     */
    public function changePassword(Request $request) {
        try {
            $validator = Validator::make($request->all(), [
                'old_password' => 'required',
                'password' => 'required',
                'c_password' => 'required|same:password',
            ]);
            
            if($validator->fails()){
                return $this->sendError('Validation Error.', $validator->errors());
            }

            $requestData = $request->only([
                'password',
                'old_password',
            ]);
            
            $user = Auth::user();

            if (!Hash::check($requestData['old_password'], $user->password)) {
                return $this->sendError('Mật khẩu cũ không đúng. Vui lòng kiểm tra lại');
            }
    
            $requestData['password'] = bcrypt($requestData['password']);

            $user = User::find($user->id);
            
            $user->password = $requestData['password'];
            $user->save();

            $success['token'] =  $user->createToken('MyApp-Partner')->accessToken;
    
            return $this->sendResponse($success, 'Thay đổi mật khẩu thành công.');
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->sendError($e->getMessage());
        }
    }
}
