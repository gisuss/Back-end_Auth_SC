<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class EditUserRequests extends FormRequest
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
            'email.email' => 'El valor del campo email no es válido.',
            'identification.required' => 'El campo de cédula es requerido.',
            'email.unique' => 'El valor del campo email ya está en uso.',
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
            'identification' => 'required',
            'email' => 'nullable|email|string|unique:users',
            'role' => 'nullable',
            'active' => 'nullable'
        ];
    }
}
