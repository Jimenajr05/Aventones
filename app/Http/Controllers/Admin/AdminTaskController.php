<?php

namespace App\Http\Controllers\Admin; 

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class AdminTaskController extends Controller
{
    /**
     * Ejecuta el comando Artisan para recordar reservas pendientes a choferes.
     */
    public function executeReservationReminder()
    {
        // 1. Determinar la ruta de redirección correcta (Super Admin vs. Admin)
        $user = Auth::user();
        $redirectRoute = ($user->role_id == 1) ? 'superadmin.dashboard' : 'admin.dashboard';

        // 2. 🎯 CORRECCIÓN FUNCIONAL: Definir el tiempo bajo (1 minuto).
        // Esto fuerza al comando a buscar casi todas las reservas pendientes.
        $minutos = 1; 

        try {
            // 3. Ejecutar el comando Artisan, pasando el argumento $minutos
            $exitCode = Artisan::call('reservas:notificar', ['minutos' => $minutos]);

            if ($exitCode === 0) {
                return Redirect::route($redirectRoute)
                    ->with('success', "Se notificaron reservas con más de {$minutos} minuto(s).");
            }

            if ($exitCode === 2) {
                return Redirect::route($redirectRoute)
                    ->with('success', "No se encontraron reservas pendientes con más de {$minutos} minuto(s).");
            }

            return Redirect::route($redirectRoute)
            ->with('error', '❌ Ocurrió un error al ejecutar el comando. Revisa los logs.');

            
        } catch (\Exception $e) {
            Log::error('Excepción al ejecutar comando Artisan: ' . $e->getMessage());
            return Redirect::route($redirectRoute)
                ->with('error', '❌ Error fatal al ejecutar el comando. Mensaje: ' . $e->getMessage());
        }
    }
}