<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Illuminate\Support\Facades\Log;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {

        return Inertia::render('RegisterUser');
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
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'document' => 'required|string|max:20',
            ]);


            $user = User::where('document', $validated['document'])->first();

            if (!$user) {
                $user = User::create([
                    'name' => $validated['name'],
                    'document' => $validated['document'],
                    'password' => bcrypt($validated['document']),
                    'email' => $validated['document'] . '@example.com', // Campo requerido
                ]);
            }

            Auth::login($user);

            return response()->json([
                'message' => 'Usuario registrado con éxito',
                'redirect' => route('shifts.index'),
                'user' => $user
            ], 201);

        } catch (\Exception $e) {
            Log::error('Error en registro:', ['error' => $e->getMessage()]);
            return response()->json([
                'message' => 'Error al registrar usuario: ' . $e->getMessage()
            ], 500);
        }
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
