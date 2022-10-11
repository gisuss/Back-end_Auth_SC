<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RegisterUserRequests extends FormRequest
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
            'first_name.required' => 'El campo de nombre es requerido.',
            'last_name.required' => 'El campo de apellido es requerido.',
            'email.required' => 'El campo de email es requerido.',
            'identification.required' => 'El campo de cédula es requerido.',
            'email.email' => 'El valor del campo de email no es válido.',
            'email.unique' => 'El valor del campo email ya está en uso.',
            'first_name.min' => 'El valor del campo nombre debe ser mayor a 4 caracteres.',
            'last_name.min' => 'El valor del campo apellido debe ser mayor a 4 caracteres.',
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
            'first_name' => 'required|string|min:4',
            'last_name' => 'required|string|min:4',
            'email' => 'required|string|email|unique:users',
            'identification' => 'required|string|min:6',
            'role' => 'required|string',
            'active' => 'nullable'
        ];
    }
}
