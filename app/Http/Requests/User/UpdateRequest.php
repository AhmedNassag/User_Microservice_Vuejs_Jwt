<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;

class UpdateRequest extends FormRequest
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
        return [
            'name'     => 'required',
            'email'    => 'required|email|unique:users,email,'.$this->user.',_id',
            'password' => 'nullable|min:8',
            'roles'    => 'required'
        ];
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
            'password.nullable' => trans('validation.nullable'),
            'password.min'      => trans('validation.min'),
            'roles.required'    => trans('validation.required'),
        ];
    }
}
