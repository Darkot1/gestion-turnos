<?php

namespace App\Http\Controllers;

use App\Http\Requests\ModuleFormRequest;
use App\Models\modules;

class ModuleController extends Controller
{
    // Método para crear una nueva caja
    public function store(ModuleFormRequest $request)
    {
        // La validación ya ha sido realizada por el Form Request
        $module = modules::create($request->validated());

        return response()->json([
            'message' => 'Caja creada exitosamente',
            'module' => $module,
        ], 201);
    }

    public function destroy($id)
    {
        $module = modules::find($id);

        if (!$module) {
            return response()->json(['message' => 'Caja no encontrada'], 404);
        }

        $module->delete();

        return response()->json([
            'message' => 'Caja eliminada exitosamente',
        ], 200);
    }
}
