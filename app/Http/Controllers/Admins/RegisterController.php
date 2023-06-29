<?php

namespace App\Http\Controllers\Admins;

use App\Http\Controllers\BaseController;
use App\Models\Admin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class RegisterController extends BaseController
{
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|email',
            'password' => 'required',
            'c_password' => 'required|same:password',
        ]);
   
        if($validator->fails()){
            return $this->sendError('Validation Error.', $validator->errors());       
        }
   
        $input = $request->all();
        $input['password'] = bcrypt($input['password']);
        $admin = Admin::create($input);
        $success['token'] =  $admin->createToken('MyApp')->accessToken;
        $success['name'] =  $admin->name;
   
        return $this->sendResponse($success, 'User register successfully.');
    }
   
    /**
     * Login api
     *
     * @return \Illuminate\Http\Response
     */
    public function login(Request $request)
    {
        if((Auth::guard('admin')->attempt(['email' => $request->email, 'password' => $request->password]))){
            $adminId = Auth::guard('admin')->user()->id;
            $admin = Admin::find($adminId);
            $success['token'] = $admin->createToken('MyApp')->accessToken;
            $success['name'] = $admin->name;
            
            return $this->sendResponse($success, 'User login successfully.');
        } 
        else{ 
            return $this->sendError('Unauthorised.', ['error'=>'Unauthorised']);
        } 
    }
}
