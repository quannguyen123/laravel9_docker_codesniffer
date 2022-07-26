<?php

namespace App\Http\Controllers\Users;

use App\Http\Controllers\Controller;
use App\Http\Requests\Users\LoginRequest;
use App\Http\Requests\Users\RegisterRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function showLoginForm()
    {
        if (Auth::guard()->check()) {
            return redirect()->route('home');
        }

        return view('users.auth.login');
    }

    public function login(LoginRequest $request)
    {
        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials)) {
            return redirect()->route('home')->with('success', __('top.alert.success'));
        } else {
            return redirect()->back()->with('error', __('login-frontend.messages.error'));
        }
        return 'user login';
    }

    public function register()
    {
        return view('users.auth.register');
    }

    public function postRegister(RegisterRequest $request)
    {
        $data['name'] = $request->name;
        $data['email'] = $request->email;
        $data['password'] = Hash::make($request->password);

        User::create($data);
        return redirect()->route('user-get-login');
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

    public function logout() {
        Auth::logout();
        return redirect()->route('user-get-login');
    }
}
