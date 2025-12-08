<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Carbon\Carbon;

// Clase para manejar la solicitud de actualización de perfil
class ProfileUpdateRequest extends FormRequest
{

    // Reglas de validación para actualizar el perfil
    public function rules(): array
    {
        return [
            'nombre' => ['required', 'string', 'max:255'],
            'apellido' => ['required', 'string', 'max:255'],
            'telefono' => ['required','string','max:20', Rule::unique('users', 'telefono')->ignore($this->user()->id)],

            // Email validado y único excepto el del usuario actual
            'email' => ['required', 'email', 'max:255', Rule::unique('users')->ignore($this->user()->id)],

            // Validación de fecha de nacimiento según el rol del usuario
            'fecha_nacimiento' => [
                'required',
                'date',
                function ($attribute, $value, $fail) {
                    $edad = Carbon::parse($value)->age;
                    $role = $this->user()->role_id;

                    if ($role == 1 && $edad < 18) {
                        $fail('Como super administrador debe ser mayor de edad.');
                    }

                    if ($role == 2 && $edad < 18) {
                        $fail('Como administrador debe ser mayor de edad.');
                    }

                    if ($role == 4 && $edad < 13) {
                        $fail('Como pasajero debe tener al menos 13 años.');
                    }

                    if ($role == 3 && $edad < 18) {
                        $fail('Como chofer debe ser mayor de edad.');
                    }
                },
            ],

            // foto opcional
            'foto' => ['nullable', 'image', 'max:2048'],
        ];
    }
}
