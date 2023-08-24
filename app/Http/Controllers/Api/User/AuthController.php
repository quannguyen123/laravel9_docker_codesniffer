<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\BaseController;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class AuthController extends BaseController
{
    /**
     * @OA\Post(
     *     path="/api/user/register",
     *     summary="Đăng ký user",
     *     tags={"User-Authorization"},
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
                'email' => 'required|email',
                'password' => 'required',
                'c_password' => 'required|same:password',
            ]);
    
            if($validator->fails()){
                return $this->sendError('Validation Error.', $validator->errors());       
            }
    
            $userData['first_name'] = $request['first_name'];
            $userData['last_name'] = $request['last_name'];
            $userData['email'] = $request['email'];
            $userData['password'] = bcrypt($request['password']);
            $userData['type'] = config('custom.user-type.type-user');
            $userData['status'] = config('custom.status.active');

            DB::beginTransaction();
            $user = User::create($userData);
            $user->assignRole('user');
            DB::commit();

            $success['token'] =  $user->createToken('MyApp-User')->accessToken;
            $success['name'] =  $user->first_name . ' ' . $user->last_name;
    
            return $this->sendResponse($success, 'User register successfully.');
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
     *     path="/api/user/login",
     *     summary="User đăng nhập",
     *     tags={"User-Authorization"},
     *     description="User login",
     *      @OA\RequestBody(
     *          @OA\JsonContent(),
     *          @OA\MediaType(
     *              mediaType="multipart/form-data",
     *              @OA\Schema(
     *                  type="object",
     *                  required={"email", "password"},
     *                  @OA\Property(property="email", type="string", format="string", example="quanquanuser@gmail.com"),
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
            if((Auth::attempt(['email' => $request->email, 'password' => $request->password, 'type' => config('custom.user-type.type-user')]))){
                $userId = Auth::user()->id;
                $user = User::find($userId);
                if ($user->status == config('custom.status.lock')) {
                    Auth::user()->tokens->each(function($token, $key) {
                        $token->delete();
                    });

                    return $this->sendResponse([], 'Tài khoản đã bị khóa. Vui lòng liên hệ admin để được hỗ trợ');
                }

                $success['token'] = $user->createToken('MyApp-User')->accessToken;
                
                return $this->sendResponse($success, 'User login successfully.');
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
            return $this->sendResponse($success, 'User logout success');
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage());
        }
    }
}
