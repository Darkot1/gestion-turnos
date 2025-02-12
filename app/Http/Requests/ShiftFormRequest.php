<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ShiftFormRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'type' => 'required|in:muestras,resultados',
            'module_id' => 'required|exists:modules,id',  // Verifica que el module_id sea válido
            'codigo' => 'required|string|max:255',
            'status' => 'required|in:espera,atendido,en proceso,cancelado',
        ];
    }
}
