<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UserFormRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $this->user,
            'document' => 'required|string|max:255|unique:users,document,' . $this->user,
            'password' => 'nullable|string|min:6',
            'role' => 'required|in:admin,employee,client',
        ];
    }

    /**
     * @return array
     */
    public function messages()
    {
        return [
            'name.required' => 'El nombre es obligatorio.',
            'email.required' => 'El correo electrónico es obligatorio.',
            'email.unique' => 'El correo electrónico ya está registrado.',
            'document.required' => 'El documento es obligatorio.',
            'document.unique' => 'El documento ya está registrado.',
            'password.min' => 'La contraseña debe tener al menos 6 caracteres.',
            'role.required' => 'El rol es obligatorio.',
        ];
    }
}
