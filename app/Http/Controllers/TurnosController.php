<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class TurnosController extends Controller
{
    public function standby(): Response
    {
        return Inertia::render('standby/WaitingTurn');
    }
}
