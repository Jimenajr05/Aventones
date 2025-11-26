<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Reserva;
use App\Mail\RecordatorioReservasPendientes;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;

class NotificarReservasPendientes extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'reservas:notificar {minutos=10}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Notifica a los choferes sobre reservas pendientes con más de X minutos sin responder';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $minutos = $this->argument('minutos');

        $this->info("Buscando reservas pendientes con más de {$minutos} minutos...");

        // ESTO ES LO CORRECTO: estado = 1
        $reservas = Reserva::where('estado', 1)
            ->where('created_at', '<=', now()->subMinutes($minutos))
            ->with(['ride.vehiculo.chofer'])
            ->get();

        if ($reservas->isEmpty()) {
            $this->info("No hay reservas pendientes para notificar.");
            return Command::SUCCESS;
        }

        foreach ($reservas as $reserva) {
            $chofer = $reserva->ride->vehiculo->chofer;

            if (!$chofer || !$chofer->email) {
                $this->warn("Reserva ID={$reserva->id} no tiene chofer asignado. Saltando...");
                continue;
            }

            Mail::to($chofer->email)->send(new RecordatorioReservasPendientes($reserva));


            $this->info("Correo enviado a {$chofer->email} por reserva #{$reserva->id}");
        }

        return Command::SUCCESS;
    }

}
