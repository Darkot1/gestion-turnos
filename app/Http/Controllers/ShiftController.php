<?php

namespace App\Http\Controllers;

use App\Models\Shifts;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;

class ShiftController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Inertia::render('ShiftsIndex');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'type' => 'required|in:muestras,resultados',
        ]);

        $today = now()->toDateString();

        $lastShift = shifts::where('type', $validated['type'])
            ->whereDate('date', $today)
            ->latest('number')
            ->first();

        $lastNumber = $lastShift ? (int) preg_replace('/\D/', '', $lastShift->number) : 0;

        $newNumber = $lastNumber + 1;

        // Formatear el número como M-001 o R-001
        $formattedNumber = ($validated['type'] == 'muestras' ? 'M-' : 'R-') . str_pad($newNumber, 3, '0', STR_PAD_LEFT);

        $shift = Shifts::create([
            'type' => $validated['type'],
            'module_id' => null,
            'user_id' => Auth::user()->id,
            'number' => $formattedNumber,
            'status' => 'espera',
            'date' => now()->toDateString(),
        ]);

        Auth::logout();

        return response()->json(['message' => "Turno generado: $formattedNumber",], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    public function showPendingShifts()
    {
        $shifts = $this->getPendingAndInProcessShifts();
        $firstShifts = $this->getFirstShiftsOfEachType($shifts);

        return Inertia::render('Shifts/PendingShifts', [
            'shifts' => $shifts,
            'firstShifts' => $firstShifts
        ]);
    }

    private function getPendingAndInProcessShifts()
    {
        return Shifts::whereIn('status', ['espera', 'en proceso'])
            ->with(['module', 'user'])
            ->orderBy('date')
            ->orderBy('created_at')
            ->orderBy('number')
            ->get();
    }

    private function getFirstShiftsOfEachType($shifts)
    {
        $firstShifts = $shifts->where('status', 'espera')
            ->groupBy('type')
            ->map(fn($group) => $group->first());

        return [
            'muestras' => $firstShifts->get('muestras'),
            'resultados' => $firstShifts->get('resultados')
        ];
    }

    public function showInProcessShifts()
    {
        $shifts = Shifts::where('status', 'en proceso')
            ->with(['module', 'user'])
            ->orderBy('date')
            ->orderBy('updated_at', 'desc')
            ->get();


        return Inertia::render('Shifts/InProcessShifts', [
            'shifts' => $shifts
        ]);
    }

    public function updateShiftStatus(Request $request, Shifts $shift)
    {
        $validated = $request->validate([
            'status' => 'required|in:en proceso,atendido,cancelado',
            'module_id' => 'required|exists:modules,id'
        ]);

        try {
            $shift->update($validated);
            $shift->load(['module', 'user']); 

            return response()->json([
                'message' => 'Estado actualizado correctamente',
                'shift' => $shift
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al actualizar el estado',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
