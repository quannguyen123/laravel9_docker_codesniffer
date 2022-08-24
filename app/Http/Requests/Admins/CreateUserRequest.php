<?php

namespace App\Http\Requests\Admins;

use Illuminate\Foundation\Http\FormRequest;

class CreateUserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'name' => 'required|max:50',
            'email'  => 'required|max:50|email|unique:users',
            'password'  => 'required|max:20|confirmed|min:8',
            'password_confirmation'  => 'required',
        ];
    }

    /**
     * Get message validate
     *
     * @return array<string, mixed>
     */
    public function messages()
    {
        return [
            'name.required' => __('user.msg.name_required'),
            'name.max' => __('user.msg.name_max'),
            'email.required'  => __('user.msg.email_required'),
            'email.email'  => __('user.msg.email_email'),
            'email.unique'  => __('user.msg.email_unique'),
            'email.max'  => __('user.msg.email_max'),
            'password.required'  => __('user.msg.password_required'),
            'password.max'  => __('user.msg.password_max'),
            'password.confirmed'  => __('user.msg.password_confirmed'),
            'password.min'  => __('user.msg.password_min'),
        ];
    }
}
