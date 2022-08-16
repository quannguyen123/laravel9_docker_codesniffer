<?php

namespace App\Http\Controllers\Admins;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function showLoginForm() {
        return view('admins.auth.login');
    }
    public function login() {
        $login = [
            'email' => $request->email,
            'password' => $request->password
        ];

        if (Auth::guard()->attempt($login)) {
            return redirect()->route('top')->with('success', __('top.alert.success'));
        } else {
            return redirect()->back()->with('error', __('login-frontend.messages.error'));
        }
    }

    public function register() {
        return view('auth.registers.show');
    }
    public function postRegister() {
        $data['name'] = $request->name;
        $data['email'] = $request->email;
        $data['password'] = bcrypt($request->password);
        $data['role_id'] = 1;

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
