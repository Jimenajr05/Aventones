<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Reserva;
use App\Mail\RecordatorioReservasPendientes;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;
use Illuminate\Support\Collection; 

class NotificarReservasPendientes extends Command
{
    protected $signature = 'reservas:notificar {minutos=10}';
    protected $description = 'Notifica a los choferes sobre reservas pendientes con más de X minutos sin responder';

    public function handle()
    {
        $minutos = $this->argument('minutos');
        $this->info("Buscando reservas pendientes con más de {$minutos} minutos...");

        $reservasPendientes = Reserva::where('estado', 1)
            ->where('created_at', '<=', now()->subMinutes($minutos))
            ->with(['ride.vehiculo.chofer'])
            ->get();

        if ($reservasPendientes->isEmpty()) {
            $this->info("No hay reservas pendientes para notificar.");
            return 2;
        }
        
        // 1. Agrupar las reservas por el correo del chofer
        /** @var Collection<string, Collection<Reserva>> $reservasAgrupadas */
        $reservasAgrupadas = collect();
        
        foreach ($reservasPendientes as $reserva) {
            $chofer = $reserva->ride->vehiculo->chofer;
            
            if (!$chofer || !$chofer->email) {
                $this->warn("Reserva ID={$reserva->id} no tiene chofer o email asignado. Saltando...");
                continue;
            }
            
            $email = $chofer->email;
            
            // Si la llave del email no existe, la inicializamos
            if (!$reservasAgrupadas->has($email)) {
                $reservasAgrupadas->put($email, collect());
            }
            
            // Añadimos la reserva a la colección de ese chofer
            $reservasAgrupadas->get($email)->push($reserva);
        }

        $this->info("Se encontraron " . $reservasAgrupadas->count() . " choferes con reservas pendientes.");

        // 2. ENVIAR UN SOLO CORREO POR CADA CHOFER (pasando la colección)
        foreach ($reservasAgrupadas as $email => $reservasColeccion) {
            
            // Usamos la colección de reservas para enviar el Mailable
            Mail::to($email)->send(new RecordatorioReservasPendientes($reservasColeccion));

            $count = $reservasColeccion->count();
            $this->info("Correo enviado a {$email} con {$count} reservas.");
        }


        return Command::SUCCESS;
    }
}