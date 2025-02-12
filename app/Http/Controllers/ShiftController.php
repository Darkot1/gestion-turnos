<?php

namespace App\Http\Controllers;

use App\Models\shifts;
use Illuminate\Http\Request;
use Pusher\Pusher;
use Mike42\Escpos\Printer;
use Mike42\Escpos\PrintConnectors\WindowsPrintConnector;
use Mike42\Escpos\PrintConnectors\FilePrintConnector;

class ShiftController extends Controller
{
    //Validar el tipo de turno
    public function generate(Request $request)
    {
        $request->validate([
            'type'=> 'required|in:R,T'
        ]);
        $type = $request->input('type');

        // obtener el turno
        $lastTurn = shifts::where('codigo', 'like', "$type%")->latest()->first();
        $lastNumber = $lastTurn ? ((int)substr($lastTurn->codigo, 1)) : 0;

        //resetear los turno si se llega al limite
        $number = ($lastNumber >= 99) ? 1 : $lastNumber + 1;

        // Generr nuevo turno
        $turn = shifts::create([
            'codigo' => $type . str_pad($number, 3, '0', STR_PAD_LEFT),
            'module_id' => 1
        ]);

        //Emitir el evento con WebSocket
        $this->emitTurn($turn);

        return response()->json($turn);
    }

    // Imprimir el turno
    public function print($id)
    {
        $turn = shifts::findOrFail($id);

        try {
            $connector = strtoupper(substr(PHP_OS, 0, 3)) === 'WIN'
                ? new WindowsPrintConnector("THERMAL_PRINTER")
                : new FilePrintConnector("/dev/usb/lp0");

            $printer = new Printer($connector);
            $printer->setJustification(Printer::JUSTIFY_CENTER);
            $printer->text("=== TICKET ===\n");
            $printer->text("Code: " . $turn->codigo . "\n");
            $printer->text("--------------------\n");
            $printer->feed(3);
            $printer->cut();
            $printer->close();

            return response()->json(["message" => "Ticket printed successfully"]);
        } catch (\Exception $e) {
            return response()->json(["error" => $e->getMessage()], 500);
        }
    }


    private function emitTurn($turn)
    {
        $pusher = new Pusher(env('PUSHER_APP_KEY'), env('PUSHER_APP_SECRET'), env('PUSHER_APP_ID'), [
            'cluster' => env('PUSHER_APP_CLUSTER'),
            'useTLS' => true
        ]);

        $pusher->trigger('turns', 'new-turn', ['turn' => $turn]);
    }
}

