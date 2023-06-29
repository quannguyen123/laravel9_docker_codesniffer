<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\BaseController;
use App\Http\Requests\Api\Admin\RegisterAdminRequest;
use App\Models\Admin;
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
                'name' => 'required',
                'email' => 'required|email|unique:admins',
                'password' => 'required',
                'c_password' => 'required|same:password|min:8',
            ]);
            
            if ($validator->fails()) {
                return $this->sendError('Validation Error.', $validator->errors());
            }
            
            $input = $request->all();
            $input['password'] = bcrypt($input['password']);
            
            DB::beginTransaction();
            $admin = Admin::create($input);
            $admin->assignRole('admin');
            DB::commit();

            $success['token'] =  $admin->createToken('MyApp-Admin')->accessToken;
            $success['name'] =  $admin->name;
   
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
            if((Auth::guard('admin')->attempt(['email' => $request->email, 'password' => $request->password]))){
                $adminId = Auth::guard('admin')->user()->id;
                $admin = Admin::find($adminId);
                $success['token'] = $admin->createToken('MyApp-Admin')->accessToken;
                $success['name'] = $admin->name;
                
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
            $success = [];
   
            return $this->sendResponse($success, 'Admin logout success');
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage());
        }
    }
}
