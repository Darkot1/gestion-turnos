<?php

namespace App\Http\Controllers;

use App\Models\Module;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ModuleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $modules = Module::all();

        if (request()->wantsJson()) {
            return response()->json([
                'modules' => $modules
            ]);
        }

        return Inertia::render('Modules/Index', [
            'modules' => $modules
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'number' => 'required|string|unique:modules,number',
            'status' => 'sometimes|in:active,inactive,busy',
        ]);

       
        $validated['status'] = $validated['status'] ?? 'active';

        $module = Module::create($validated);

        if ($request->wantsJson()) {
            return response()->json([
                'message' => 'Módulo creado correctamente',
                'module' => $module
            ]);
        }

        return back()->with('message', 'Módulo creado correctamente');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Module $module)
    {
        $validated = $request->validate([
            'number' => 'required|string|unique:modules,number,' . $module->id,
            'status' => 'required|in:active,inactive,busy',
        ]);

        $module->update($validated);

        if ($request->wantsJson()) {
            return response()->json([
                'message' => 'Módulo actualizado correctamente',
                'module' => $module
            ]);
        }

        return back()->with('message', 'Módulo actualizado correctamente');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
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
}
