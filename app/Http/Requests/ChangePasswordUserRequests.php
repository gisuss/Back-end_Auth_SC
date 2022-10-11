<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ChangePasswordUserRequests extends FormRequest
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
            'old_password.required' => 'El campo de contraseña antigua es requerido.',
            'password.required' => 'El campo de contraseña es requerido.',
            'confirm_password.required' => 'El campo de confirmación de contraseña es requerido.',
            'confirm_password.same' => 'El valor del campo confirmación de contraseña debe ser igual al valor del campo de contraseña.',
            'identification.required' => 'El campo de cédula es requerido.',
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
            'old_password' => 'required',
            'password' => 'required|min:6|max:16',
            'confirm_password' => 'required|same:password',
            'identification' => 'required'
        ];
    }
}
