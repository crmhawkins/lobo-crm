<div class="container my-5">
    <button class="btn btn-primary mb-3" data-toggle="modal" data-target="#createIncidenciaModal">
        Crear Incidencia
    </button>

    <!-- Modal -->
    <div wire:ignore.self class="modal fade" id="createIncidenciaModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Crear Nueva Incidencia</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form wire:submit.prevent="createIncidencia">
                        <div class="mb-3">
                            <label for="observaciones" class="form-label">Observaciones</label>
                            <textarea class="form-control" id="observaciones" wire:model="observaciones" required></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="estado" class="form-label">Estado</label>
                            <input type="text" class="form-control" id="estado" wire:model="estado" disabled>
                        </div>
                        <button type="submit" class="btn btn-success">Crear</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <!-- Recibida -->
        <div class="col-md-3">
            <h3>Recibida</h3>
            <div id="recibida" class="incidencias-list" wire:sortable-group.item-group="estado">
                @foreach($incidencias->where('estado', 'recibida')->sortByDesc('created_at') as $incidencia)
                <div class="card mb-3" wire:sortable-group.item="{{ $incidencia->id }}">
                    <div class="card-body">
                        @if ($editingIncidenciaId === $incidencia->id)
                            <!-- Formulario de edición -->
                            <form wire:submit.prevent="updateIncidencia">
                                <div class="mb-3">
                                    <label for="observaciones" class="form-label">Observaciones</label>
                                    <textarea class="form-control" wire:model="observaciones"></textarea>
                                </div>
                                <div class="mb-3">
                                    <label for="estado" class="form-label">Estado</label>
                                    <select class="form-control" wire:model="estado">
                                        <option value="recibida">Recibida</option>
                                        <option value="tramite">En Trámite</option>
                                        <option value="solucionada">Solucionada</option>
                                        <option value="rechazada">Rechazada</option>
                                    </select>
                                </div>
                                <button type="submit" class="btn btn-success">Actualizar</button>
                                <button type="button" class="btn btn-secondary" wire:click="cancelEdit">Cancelar</button>
                            </form>
                        @else
                            <!-- Visualización normal de la incidencia -->
                            <div>
                                <h5 class="card-title">Incidencia #{{ $incidencia->id }}</h5>
                                <p class="card-text">{{ $incidencia->observaciones }}</p>
                                <p class="card-text">
                                    <small class="text-muted">Creado el {{ $incidencia->created_at->format('d-m-Y H:i') }}</small>
                                </p>
                            </div>
                            <div class="card-actions">
                                <button class="btn btn-warning" wire:click="editIncidencia({{ $incidencia->id }})">Editar</button>
                                <button class="btn btn-danger" wire:click="deleteIncidencia({{ $incidencia->id }})">Borrar</button>
                            </div>
                        @endif
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        <!-- Tramite -->
        <div class="col-md-3">
            <h3>En Trámite</h3>
            <div id="tramite" class="incidencias-list" wire:sortable-group.item-group="estado">
                @foreach($incidencias->where('estado', 'tramite')->sortByDesc('created_at') as $incidencia)
                <div class="card mb-3" wire:sortable-group.item="{{ $incidencia->id }}">
                    <div class="card-body">
                        <div>
                            <h5 class="card-title">Incidencia #{{ $incidencia->id }}</h5>
                            <p class="card-text">{{ $incidencia->observaciones }}</p>
                            <p class="card-text">
                                <small class="text-muted">Creado el {{ $incidencia->created_at->format('d-m-Y H:i') }}</small>
                            </p>
                        </div>
                        <div class="card-actions">
                            <button class="btn btn-warning" wire:click="editIncidencia({{ $incidencia->id }})">Editar</button>
                            <button class="btn btn-danger" wire:click="deleteIncidencia({{ $incidencia->id }})">Borrar</button>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        <!-- Solucionada -->
        <div class="col-md-3">
            <h3>Solucionada</h3>
            <div id="solucionada" class="incidencias-list" wire:sortable-group.item-group="estado">
                @foreach($incidencias->where('estado', 'solucionada')->sortByDesc('created_at') as $incidencia)
                <div class="card mb-3" wire:sortable-group.item="{{ $incidencia->id }}">
                    <div class="card-body">
                        <div>
                            <h5 class="card-title">Incidencia #{{ $incidencia->id }}</h5>
                            <p class="card-text">{{ $incidencia->observaciones }}</p>
                            <p class="card-text">
                                <small class="text-muted">Creado el {{ $incidencia->created_at->format('d-m-Y H:i') }}</small>
                            </p>
                        </div>
                        <div class="card-actions">
                            <button class="btn btn-warning" wire:click="editIncidencia({{ $incidencia->id }})">Editar</button>
                            <button class="btn btn-danger" wire:click="deleteIncidencia({{ $incidencia->id }})">Borrar</button>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        <!-- Rechazada -->
        <div class="col-md-3">
            <h3>Rechazada</h3>
            <div id="rechazada" class="incidencias-list" wire:sortable-group.item-group="estado">
                @foreach($incidencias->where('estado', 'rechazada')->sortByDesc('created_at') as $incidencia)
                <div class="card mb-3" wire:sortable-group.item="{{ $incidencia->id }}">
                    <div class="card-body">
                        <div>
                            <h5 class="card-title">Incidencia #{{ $incidencia->id }}</h5>
                            <p class="card-text">{{ $incidencia->observaciones }}</p>
                            <p class="card-text">
                                <small class="text-muted">Creado el {{ $incidencia->created_at->format('d-m-Y H:i') }}</small>
                            </p>
                        </div>
                        <div class="card-actions">
                            <button class="btn btn-warning" wire:click="editIncidencia({{ $incidencia->id }})">Editar</button>
                            <button class="btn btn-danger" wire:click="deleteIncidencia({{ $incidencia->id }})">Borrar</button>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
    <style>
    .incidencias-list {
        border: 2px dashed #007bff;
        min-height: 250px;
        padding: 15px;
        margin-bottom: 30px;
        border-radius: 10px;
        background-color: #f0f4ff;
        transition: background-color 0.3s ease;
    }

    .incidencias-list.drag-active {
        border-color: #28a745;
        background-color: #e8f5e9;
    }

    .card {
        border: 2px solid #007bff;
        border-radius: 10px;
        margin-bottom: 20px;
        background-color: #ffffff;
        padding: 15px;
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }

    .card:hover {
        transform: scale(1.02);
        box-shadow: 0 4px 12px rgba(0, 123, 255, 0.2);
    }

    .card-title {
        font-size: 1.2rem;
        font-weight: bold;
        color: #007bff;
    }

    .card-text {
        font-size: 1rem;
        color: #343a40;
    }

    .card-text.observaciones {
        font-size: 1.2rem;
        color: #495057;
        font-weight: 500;
    }

    .card-body {
        display: block;
    }

    .card-footer {
        display: flex;
        justify-content: space-between;
        margin-top: 10px;
    }

    .card-footer small {
        font-size: 0.85rem;
        color: #6c757d;
    }

    .card-actions {
        display: flex;
        gap: 10px;
    }

    .card-actions .btn {
        font-size: 0.9rem;
        padding: 0.4rem 0.8rem;
        border-radius: 5px;
        transition: background-color 0.3s ease;
    }

    .btn-warning:hover {
        background-color: #e0a800;
        color: white;
    }

    .btn-danger:hover {
        background-color: #c82333;
        color: white;
    }
</style>

</div>

@section('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/Sortable/1.14.0/Sortable.min.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const containers = document.querySelectorAll('.incidencias-list');
        containers.forEach(container => {
            new Sortable(container, {
                group: 'shared',
                animation: 150,
                onEnd: function (evt) {
                    const itemId = evt.item.getAttribute('wire:sortable-group.item');
                    const newEstado = evt.to.id; // ID del nuevo estado
                    Livewire.emit('updateIncidenciaState', itemId, newEstado);
                }
            });
        });
    });
</script>

@endsection