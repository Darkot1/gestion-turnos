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
        $modules = $this->getActiveAvailableModules();

        return request()->wantsJson()
            ? response()->json(['modules' => $modules])
            : Inertia::render('Modules/Index', ['modules' => $modules]);
    }

    private function getActiveAvailableModules()
    {
        return Module::where('status', 'active')
            ->whereDoesntHave('shifts', function ($query) {
                $query->where('status', 'en proceso');
            })
            ->get();
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
            'number' => 'required|string|unique:modules,number',
            'status' => 'required|in:active,inactive,busy',
        ]);

        $module = Module::create($validated);

        return response()->json([
            'message' => 'Módulo agregado correctamente',
            'module' => $module
        ], 201);
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
    public function update(Request $request, Module $module)
    {
        $validated = $request->validate([
            'status' => 'required|in:active,inactive,busy',
        ]);

        $module->update($validated);

        return response()->json([
            'message' => 'Estado actualizado correctamente',
            'module' => $module
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    /**
     * Get all modules.
     */
}
