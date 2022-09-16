<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RegisterMassiveRequests extends FormRequest
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
     * Get the error message for the defined validation rules.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'users.*.first_name.required' => 'el campo de nombre es requerido.',
            'users.*.last_name.required' => 'el campo de apellido es requerido.',
            'users.*.email.required' => 'el campo de email es requerido.',
            'users.*.identification.required' => 'el campo de cédula es requerido.',
            'users.*.email.email' => 'el campo de email no es válido.',
        ];
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'users' => 'present|array',
            'users.*.first_name' => 'required|string|min:4',
            'users.*.last_name' => 'required|string|min:4',
            'users.*.email' => 'required|string|email',
            'users.*.identification' => 'required|string|min:6',
            'users.*.role' => 'nullable'
        ];
    }
}
