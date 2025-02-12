<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\shifts;

class ShiftsComponent extends Component
{
    public $turns;

    protected $listeners = ['turnReceived' => 'updateTurns'];

    public function mount()
    {
        $this->turns = shifts::orderBy('created_at', 'desc')->get();
    }

    public function updateTurns()
    {
        $this->turns = shifts::orderBy('created_at', 'desc')->get();
    }

    public function render()
    {
        return view('livewire.shifts');
    }
}
