<?php

namespace App\Http\Controllers\Admins;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admins\LoginRequest;
use App\Http\Requests\Admins\RegisterRequest;
use App\Models\Admin;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function showLoginForm()
    {
        if (Auth::guard('admin')->check()) {
            return redirect()->route('admin-dashboard');
        }

        return view('admins.auth.login');
    }
    public function login(LoginRequest $request)
    {
        $credentials = $request->only('email', 'password');

        if (Auth::guard('admin')->attempt($credentials)) {
            return redirect()->route('admin-dashboard')->with('success', __('top.alert.success'));
        } else {
            return redirect()->back()->with('error', __('login-frontend.messages.error'));
        }
    }

    public function register()
    { 
        return view('admins.auth.register');
    }
    public function postRegister(RegisterRequest $request)
    {
        $data['name'] = $request->name;
        $data['email'] = $request->email;
        $data['password'] = Hash::make($request->password);

        Admin::create($data);
        return redirect()->route('admin-login')->withSuccess('Successfully');
    }

    public function logout()
    {
        Auth::guard('admin')->logout();
        return redirect()->route('admin-get-login');
    }

    public function resetPassword()
    {
        
    }
    public function sendMailResetPassword()
    {
        
    }
    
    public function formResetPassword()
    {
        
    }
    public function reset()
    {
        
    }
}
