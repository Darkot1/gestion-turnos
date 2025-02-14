<?php

namespace App\Services;

use App\Models\Shifts;
use Illuminate\Support\Facades\Http;

class TicketPrinterService
{
    private $printerUrl;

    public function __construct()
    {
        $this->printerUrl = env('PRINT_URL');
    }

    public function printShiftTicket(Shifts $shift)
    {
        try {
            $response = Http::post($this->printerUrl, [
                'company' => 'CLINICA ESD ',
                'turno' => strtoupper($shift->number),
                'message' => $shift->user->name. " \n Por favor dirigirse a la \n proxima sala de espera"
            ]);

            if (!$response->successful()) {
                throw new \Exception('Error en la respuesta del servidor de impresion: ' . $response->body());
            }

            return response()->json(["message" => "Turno impreso correctamente"]);
        } catch (\Exception $e) {
            throw new \Exception('Error al imprimir: ' . $e->getMessage());
        }
    }
}
