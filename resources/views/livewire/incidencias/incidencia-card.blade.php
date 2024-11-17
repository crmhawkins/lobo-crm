<div class="card mb-3" wire:sortable-group.item="{{ $incidencia->id }}">
    <div class="text-dark fw-bold bg-warning px-3 py-2 rounded text-center">
        <span>Responsable: {{ $incidencia->user->name }} {{ $incidencia->user->surname }}</span>
    </div>

    <div class="card-body">
        <h5 class="card-title">Incidencia #{{ $incidencia->id }}</h5>

        <!-- Observaciones con funcionalidad de expandir/contraer -->
        <div class="observaciones-container">
            <p class="card-text observaciones" id="observaciones-{{ $incidencia->id }}">
                {{ Str::limit($incidencia->observaciones, 100) }} <!-- Muestra un límite de 100 caracteres -->
            </p>

            @if(strlen($incidencia->observaciones) > 100)
                <button class="btn btn-link p-0" onclick="toggleObservaciones({{ $incidencia->id }}, `{{ addslashes($incidencia->observaciones) }}`)" id="toggle-button-{{ $incidencia->id }}">
                    Ver más
                </button>
            @endif
        </div>
        <!-- Mostrar la nota si existe -->
        @if($incidencia->notas)
        <p><strong>Nota del Responsable:</strong> {{ $incidencia->notas }}</p>
        @endif

        <!-- Botón para añadir o editar la nota -->
        @if(Auth::user()->id === $incidencia->user_id)
        <button class="btn btn-primary" data-target= "#editNotaModal" wire:click="editNotas({{$incidencia->id}})" data-toggle="modal" >Editar Nota</button>
        @endif
        <p class="card-text">
            <small class="text-muted">Creado el {{ $incidencia->created_at->format('d-m-Y H:i') }}</small>
            <br>
            <small class="text-success">Actualizado el {{ $incidencia->updated_at->format('d-m-Y H:i') }}</small>
        </p>

        @if(Auth::user()->isAdmin())
        <button class="btn btn-warning" wire:click="editIncidencia({{ $incidencia->id }})" data-toggle="modal" data-target="#editIncidenciaModal">Editar</button>
        <button class="btn btn-info" wire:click="recordatorioIncidencia({{ $incidencia->id }}, 'normal')">Recordatorio</button>
        <button class="btn btn-danger mt-2"  wire:click="deleteIncidencia({{ $incidencia->id }})">Archivar</button>

        @endif
    </div>
</div>


