<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Reserva;
use App\Mail\RecordatorioReservasPendientes;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;
use Illuminate\Support\Collection; 

// Comando Artisan que notifica a los choferes sobre reservas pendientes con más de X minutos sin responder.
// Uso: php artisan reservas:notificar 10
class NotificarReservasPendientes extends Command
{
    // Definición del comando artisan 
    protected $signature = 'reservas:notificar {minutos=10}';
    protected $description = 'Notifica a los choferes sobre reservas pendientes con más de X minutos sin responder';

    // Función para ejecutar el comando artisan 
    public function handle()
    {
        // Obtener el argumento de minutos
        $minutos = $this->argument('minutos');
        $this->info("Buscando reservas pendientes con más de {$minutos} minutos...");

        // Obtener reservas pendientes
        $reservasPendientes = Reserva::where('estado', 1)
            ->where('created_at', '<=', now()->subMinutes($minutos))
            ->with(['ride.vehiculo.chofer'])
            ->get();

        // Verificar si hay reservas pendientes
        if ($reservasPendientes->isEmpty()) {
            $this->info("No hay reservas pendientes para notificar.");
            return 2;
        }
        
        /** @var Collection<string, Collection<Reserva>> $reservasAgrupadas */
        $reservasAgrupadas = collect();
        
        // Agrupar reservas por email del chofer
        foreach ($reservasPendientes as $reserva) {
            $chofer = $reserva->ride->vehiculo->chofer;
            
            // Verificar si el chofer y su email existen
            if (!$chofer || !$chofer->email) {
                $this->warn("Reserva ID={$reserva->id} no tiene chofer o email asignado. Saltando...");
                continue;
            }
            
            $email = $chofer->email;
            
            // Inicializar colección si no existe
            if (!$reservasAgrupadas->has($email)) {
                $reservasAgrupadas->put($email, collect());
            }

            $reservasAgrupadas->get($email)->push($reserva);
        }

        $this->info("Se encontraron " . $reservasAgrupadas->count() . " choferes con reservas pendientes.");

        // Enviar correos electrónicos a cada chofer
        foreach ($reservasAgrupadas as $email => $reservasColeccion) {
            
            Mail::to($email)->send(new RecordatorioReservasPendientes($reservasColeccion));

            $count = $reservasColeccion->count();
            $this->info("Correo enviado a {$email} con {$count} reservas.");
        }
        return Command::SUCCESS;
    }
}
