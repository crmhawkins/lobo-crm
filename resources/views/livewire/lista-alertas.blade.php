<div style="max-height: 350px; overflow-y: auto;">
    @if($alertas->isEmpty())
    <p>No tienes alertas</p>
    @else
    @foreach($alertas as $alerta)
        <div style="border: 1px solid #ccc; margin-bottom: 10px; padding: 10px 10px 5px 10px; display: flex; justify-content: space-between; align-items: center;">
            <div>
                <p>{{ $alerta->titulo }}</p>
                <p style=" margin-bottom:0;">{{ $alerta->descripcion }}</p>
            </div>
            <button type="button" class="btn btn-primary" wire:click="accion({{ $alerta->stage }}, {{ $alerta->id }}, {{ $alerta->referencia_id }})">Acción</button>
        </div>
    @endforeach
    @endif
</div>
