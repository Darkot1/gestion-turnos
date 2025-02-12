<div>
    <h1>Turnos</h1>
    <ul>
        @foreach ($turnos as $turno)
            <li>{{ $turno->codigo }}
                <button wire:click="$emit('imprimir', {{ $turno->id }})">🖨️ Imprimir</button>
            </li>
        @endforeach
    </ul>
</div>
