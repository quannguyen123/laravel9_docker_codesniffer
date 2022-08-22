<?php

namespace App\Http\Controllers\Admins;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\LoginRequest;
use App\Http\Requests\Admin\RegisterRequest;
use App\Models\Admin;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function showLoginForm() {
        return view('admins.auth.login');
    }
    public function login(LoginRequest $request) {
        $credentials = $request->only('email', 'password');

        if (Auth::guard('admin')->attempt($credentials)) {
            return 'thanh cong';
            return redirect()->route('top')->with('success', __('top.alert.success'));
        } else {
            return redirect()->back()->with('error', __('login-frontend.messages.error'));
        }
    }

    public function register() { 
        return view('admins.auth.register');
    }
    public function postRegister(RegisterRequest $request) {
        $data['name'] = $request->name;
        $data['email'] = $request->email;
        $data['password'] = Hash::make($request->password);

        Admin::create($data);
        return redirect()->route('admin-login');
    }

    public function logout() {
        Auth::guard('admin')->logout();
		return view('auth.login');
    }

    public function resetPassword() {
        
    }
    public function sendMailResetPassword() {
        
    }
    
    public function formResetPassword() {
        
    }
    public function reset() {
        
    }
}
