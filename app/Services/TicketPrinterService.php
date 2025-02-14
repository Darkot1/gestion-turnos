<?php

namespace App\Services;

use App\Models\Shifts;
use Mike42\Escpos\Printer;
use Mike42\Escpos\PrintConnectors\WindowsPrintConnector;

class TicketPrinterService
{
    private $printerName;

    public function __construct()
    {
        $this->printerName = env('THERMAL_PRINTER_NAME', 'Xprinter5890k');
    }

    public function printShiftTicket(Shifts $shift)
    {
        try {
            $connector = new WindowsPrintConnector($this->printerName);
            $printer = new Printer($connector);

            // Configurar el estilo del ticket
            $printer->setJustification(Printer::JUSTIFY_CENTER);

            // Logo o encabezado
            $printer->selectPrintMode(Printer::MODE_DOUBLE_WIDTH);
            $printer->text("CLÍNICA ESD\n");
            $printer->selectPrintMode();

            // Fecha y hora
            $printer->text("Fecha: " . now()->format('d/m/Y H:i') . "\n");
            $printer->text("------------------------------\n");

            // Número de turno
            $printer->selectPrintMode(Printer::MODE_DOUBLE_WIDTH | Printer::MODE_EMPHASIZED);
            $printer->text("TURNO\n");
            $printer->text(strtoupper($shift->number) . "\n");
            $printer->selectPrintMode();
            $printer->text("------------------------------\n");

            // Información del turno
            $printer->text("Tipo: " . ucfirst($shift->type) . "\n");
            $printer->text("Estado: Espera\n");
            $printer->text("------------------------------\n");

            // Mensaje final
            $printer->text("Por favor espere su turno\n");
            $printer->text("Gracias por su paciencia\n");

            // Finalizar impresión
            $printer->feed(3);
            $printer->cut();
            $printer->close();

            return true;
        } catch (\Exception $e) {
            throw new \Exception('Error al imprimir: ' . $e->getMessage());
        }
    }
}
