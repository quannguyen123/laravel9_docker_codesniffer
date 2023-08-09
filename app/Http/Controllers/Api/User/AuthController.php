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
                $success['name'] = $user->name;
                
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
