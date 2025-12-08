<?php

namespace App\Http\Controllers\Admin; 

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

// Controlador para manejar tareas administrativas relacionadas con reservas
class AdminTaskController extends Controller
{
    // Método para ejecutar el comando de recordatorio de reservas
    public function executeReservationReminder()
    {
        // Determinación de la ruta de redirección basada en el rol del usuario
        $user = Auth::user();
        $redirectRoute = ($user->role_id == 1) ? 'superadmin.dashboard' : 'admin.dashboard';

        // Definición del argumento para el comando Artisan
        $minutos = 1; 

        try {
            // Ejecución del comando Artisan con el argumento especificado
            $exitCode = Artisan::call('reservas:notificar', ['minutos' => $minutos]);

            // Manejo de la respuesta basada en el código de salida del comando
            if ($exitCode === 0) {
                return Redirect::route($redirectRoute)
                    ->with('success', "Se notificaron reservas con más de {$minutos} minuto(s).");
            }

            // Código de salida 2 indica que no se encontraron reservas pendientes
            if ($exitCode === 2) {
                return Redirect::route($redirectRoute)
                    ->with('success', "No se encontraron reservas pendientes con más de {$minutos} minuto(s).");
            }

            // Manejo de otros códigos de error
            return Redirect::route($redirectRoute)
            ->with('error', '❌ Ocurrió un error al ejecutar el comando. Revisa los logs.');

        } catch (\Exception $e) {
            Log::error('Excepción al ejecutar comando Artisan: ' . $e->getMessage());
            return Redirect::route($redirectRoute)
                ->with('error', '❌ Error fatal al ejecutar el comando. Mensaje: ' . $e->getMessage());
        }
    }
}
