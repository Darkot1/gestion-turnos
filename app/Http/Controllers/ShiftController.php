<?php

namespace App\Http\Controllers;

use App\Models\Shifts;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
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
}
