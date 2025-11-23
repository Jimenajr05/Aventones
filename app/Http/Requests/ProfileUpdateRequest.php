<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProfileUpdateRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */

    // Reglas de validación para actualizar el perfil
    public function rules(): array
    {
        return [
            'nombre' => ['required', 'string', 'max:255'],
            'apellido' => ['required', 'string', 'max:255'],
            'telefono' => ['required', 'string', 'max:20'],

            // email validado y único excepto el del usuario actual
            'email' => ['required', 'email', 'max:255', Rule::unique('users')->ignore($this->user()->id)],

            // foto opcional
            'foto' => ['nullable', 'image', 'max:2048'],
        ];
    }
}
