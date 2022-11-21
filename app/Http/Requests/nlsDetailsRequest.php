<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class nlsDetailsRequest extends FormRequest
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
            'url'   => 'url',
            'file'  => 'mimes:zip,rar,gzip,text/html,html|max:200000', //required_without_all:url|
            'date'  => 'required|date_format:Y-m-d',
            'name'  => 'required|min:3|max:20'
        ];
    }

    /**
     * Custom message for validation
     *
     * @return array
     */
    /*public function messages()
    {
        return [
            'email.required' => 'Email is required!',
            'name.required' => 'Name is required!',
            'password.required' => 'Password is required!'
        ];
    }*/
}
