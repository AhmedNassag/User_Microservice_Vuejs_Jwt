<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;

class StoreRequest extends FormRequest
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
     * @return array
     */
    public function rules()
    {
        $roles = [
            'name'     => 'required',
            'email'    => 'required|email|unique:users,email',
            'password' => 'required|min:8',
            'roles'    => 'required'
        ];

        return $roles;
    }


    /**
     * Get the validation messages that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function messages()
    {
        return [
            'name.required'     => trans('validation.required'),
            'email.required'    => trans('validation.required'),
            'email.email'       => trans('validation.email'),
            'email.unique'      => trans('validation.unique'),
            'password.required' => trans('validation.required'),
            'password.min'      => trans('validation.min'),
            'roles.required'    => trans('validation.required'),
        ];
    }
}
