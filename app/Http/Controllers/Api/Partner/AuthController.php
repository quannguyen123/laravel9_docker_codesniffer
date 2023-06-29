<?php

namespace App\Http\Controllers\Api\Partner;

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

            DB::beginTransaction();
            $user = User::create($input);
            $user->assignRole('partner');
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
            if((Auth::attempt(['email' => $request->email, 'password' => $request->password]))){
                $userId = Auth::user()->id;
                $user = User::find($userId);
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
