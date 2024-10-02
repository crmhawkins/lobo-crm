<div class="container-fluid">


     <!-- Pestañas de navegación -->
     <ul class="nav nav-tabs" id="myTab" role="tablist" >
        <li class="nav-item" role="presentation">
            <button class="nav-link @if($activeTab === 'normales') active @endif" id="normales-tab" data-toggle="tab" wire:click="setActiveTab('normales')" type="button" role="tab" aria-controls="normales" aria-selected="{{ $activeTab === 'normales' ? 'true' : 'false' }}">Incidencias Normales</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link @if($activeTab === 'pedidos') active @endif" id="pedidos-tab" data-toggle="tab" wire:click="setActiveTab('pedidos')" type="button" role="tab" aria-controls="pedidos" aria-selected="{{ $activeTab === 'pedidos' ? 'true' : 'false' }}">Incidencias de Pedidos</button>
        </li>
    </ul>

    <div class="tab-content" id="myTabContent">
        <div class="tab-pane fade @if($activeTab === 'normales') show active @endif" id="normales" role="tabpanel" aria-labelledby="normales-tab">

            <!-- Sección de Incidencias Normales -->
            <h2>Incidencias Normales</h2>
            <button class="btn btn-primary mb-3" data-toggle="modal" data-target="#createIncidenciaModal">
                Crear Incidencia
            </button>

            <!-- Modal para crear una nueva incidencia normal -->
            <div wire:ignore.self class="modal fade" id="createIncidenciaModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLabel">Crear Nueva Incidencia</h5>
                            <button type="button" class="btn-close" data-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <form wire:submit.prevent="createIncidencia">
                                <div class="mb-3">
                                    <select name="" id="" class="form-select" wire:model="user_id">
                                        <option value="">Selecciona un responsable</option>
                                        @foreach($users as $user)
                                            <option value="{{ $user->id }}">{{ $user->name }} {{$user->surname}}</option>
                                        @endforeach
                                    </select>
                                </div>
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
            <!-- Modal para editar una incidencia normal -->
            <div wire:ignore.self class="modal fade" id="editIncidenciaModal" tabindex="-1" aria-labelledby="editIncidenciaModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="editIncidenciaModalLabel">Editar Incidencia</h5>
                            <button type="button" class="btn-close" data-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <form wire:submit.prevent="">
                                <div class="mb-3">
                                    <select name="" id="" class="form-select" wire:model="user_id">
                                        <option value="">Selecciona un responsable</option>
                                        @foreach($users as $user)
                                            <option value="{{ $user->id }}">{{ $user->name }} {{$user->surname}}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label for="observaciones" class="form-label">Observaciones</label>
                                    <textarea class="form-control" id="observaciones" wire:model="observaciones" required></textarea>
                                </div>
                                <div class="mb-3">
                                    <label for="estado" class="form-label">Estado</label>
                                    <select class="form-control" id="estado" wire:model="estado" required>
                                        <option value="recibida">Recibida</option>
                                        <option value="tramite">En Trámite</option>
                                        <option value="solucionada">Solucionada</option>
                                        <option value="rechazada">Rechazada</option>
                                    </select>
                                </div>
                                <button type="submit" class="btn btn-success" data-dismiss="modal" wire:click="updateIncidencia">Guardar Cambios</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            @if(count($incidencias) == 0)
                <div class="alert alert-warning" role="alert">
                    No hay incidencias registradas.
                </div>
            @else

                <!-- Listado de Incidencias Normales por Estado -->
                <div class="row">
                    <!-- Recibida -->
                    <div class="col-md-3">
                        <h3>Recibida</h3>
                        <div id="recibida" class="incidencias-list" wire:sortable-group.item-group="incidencias">
                            @foreach($incidencias->where('estado', 'recibida')->sortByDesc('created_at') as $incidencia)
                                @include('livewire.incidencias.incidencia-card', ['incidencia' => $incidencia])
                            @endforeach
                        </div>
                    </div>

                    <!-- En Trámite -->
                    <div class="col-md-3">
                        <h3>En Trámite</h3>
                        <div id="tramite" class="incidencias-list" wire:sortable-group.item-group="incidencias">
                            @foreach($incidencias->where('estado', 'tramite')->sortByDesc('created_at') as $incidencia)
                                @include('livewire.incidencias.incidencia-card', ['incidencia' => $incidencia])
                            @endforeach
                        </div>
                    </div>

                    <!-- Solucionada -->
                    <div class="col-md-3">
                        <h3>Solucionada</h3>
                        <div id="solucionada" class="incidencias-list" wire:sortable-group.item-group="incidencias">
                            @foreach($incidencias->where('estado', 'solucionada')->sortByDesc('created_at') as $incidencia)
                                @include('livewire.incidencias.incidencia-card', ['incidencia' => $incidencia])
                            @endforeach
                        </div>
                    </div>

                    <!-- Rechazada -->
                    <div class="col-md-3">
                        <h3>Rechazada</h3>
                        <div id="rechazada" class="incidencias-list" wire:sortable-group.item-group="incidencias">
                            @foreach($incidencias->where('estado', 'rechazada')->sortByDesc('created_at') as $incidencia)
                                @include('livewire.incidencias.incidencia-card', ['incidencia' => $incidencia])
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif
        </div>
        <div class="tab-pane fade @if($activeTab === 'pedidos') show active @endif" id="pedidos" role="tabpanel" aria-labelledby="pedidos-tab">

            <!-- Sección de Incidencias de Pedidos -->
            <h2>Incidencias de Pedidos</h2>
            <button class="btn btn-primary mb-3" data-toggle="modal" data-target="#createIncidencia2Modal">
                Crear Incidencia
            </button>
            <!-- Modal para crear una nueva incidencia de pedido -->
            <div wire:ignore.self class="modal fade" id="createIncidencia2Modal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLabel">Crear Nueva Incidencia Pedido</h5>
                            <button type="button" class="btn-close" data-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <form wire:submit.prevent="createIncidenciaPedido">
                                <div class="mb-3">
                                    <select name="" id="" class="form-select" wire:model="user_id">
                                        <option value="">Selecciona un responsable</option>
                                        @foreach($users as $user)
                                            <option value="{{ $user->id }}">{{ $user->name }} {{$user->surname}}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <select name="" id="" class="form-select" wire:model="pedido_id">
                                        <option value="">Selecciona un pedido</option>
                                        @foreach($pedidos as $pedido)
                                            <option value="{{ $pedido->id }}">{{ $pedido->id }}</option>
                                        @endforeach
                                    </select>
                                </div>
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
            <!-- Modal para editar una incidencia de pedido -->
<div wire:ignore.self class="modal fade" id="editPedidoIncidenciaModal" tabindex="-1" aria-labelledby="editPedidoIncidenciaModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editPedidoIncidenciaModalLabel">Editar Incidencia de Pedido</h5>
                <button type="button" class="btn-close" data-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form wire:submit.prevent="">
                    <div class="mb-3">
                        <select name="" id="" class="form-select" wire:model="user_id">
                            <option value="">Selecciona un responsable</option>
                            @foreach($users as $user)
                                <option value="{{ $user->id }}">{{ $user->name }} {{$user->surname}}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <select name="" id="" class="form-select" wire:model="pedido_id">
                            <option value="">Selecciona un pedido</option>
                            @foreach($pedidos as $pedido)
                                <option value="{{ $pedido->id }}">{{ $pedido->id }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="observaciones" class="form-label">Observaciones</label>
                        <textarea class="form-control" id="observaciones" wire:model="observaciones" required></textarea>
                    </div>
                    
                    <div class="mb-3">
                        <label for="estado" class="form-label">Estado</label>
                        <select class="form-control" id="estado" wire:model="estado" required>
                            <option value="recibida">Recibida</option>
                            <option value="tramite">En Trámite</option>
                            <option value="solucionada">Solucionada</option>
                            <option value="rechazada">Rechazada</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-success" data-dismiss="modal" wire:click="updatePedidoIncidencia">Guardar Cambios</button>
                </form>
            </div>
        </div>
    </div>
</div>


            @if(count($pedidosIncidencias) == 0)
                <div class="alert alert-warning" role="alert">
                    No hay incidencias de pedidos registradas.
                </div>
            @else
                <!-- Listado de Incidencias de Pedidos por Estado -->
                <div class="row">
                    <!-- Recibida -->
                    <div class="col-md-3">
                        <h3>Recibida</h3>
                        <div id="recibida" class="pedidos-incidencias-list" wire:sortable-group.item-group="pedidosIncidencias">
                            @foreach($pedidosIncidencias->where('estado', 'recibida')->sortByDesc('created_at') as $pedidoIncidencia)
                                @include('livewire.incidencias.pedido-incidencia-card', ['incidencia' => $pedidoIncidencia])
                            @endforeach
                        </div>
                    </div>

                    <!-- En Trámite -->
                    <div class="col-md-3">
                        <h3>En Trámite</h3>
                        <div id="tramite" class="pedidos-incidencias-list" wire:sortable-group.item-group="pedidosIncidencias">
                            @foreach($pedidosIncidencias->where('estado', 'tramite')->sortByDesc('created_at') as $pedidoIncidencia)
                                @include('livewire.incidencias.pedido-incidencia-card', ['incidencia' => $pedidoIncidencia])
                            @endforeach
                        </div>
                    </div>

                    <!-- Solucionada -->
                    <div class="col-md-3">
                        <h3>Solucionada</h3>
                        <div id="solucionada" class="pedidos-incidencias-list" wire:sortable-group.item-group="pedidosIncidencias">
                            @foreach($pedidosIncidencias->where('estado', 'solucionada')->sortByDesc('created_at') as $pedidoIncidencia)
                                @include('livewire.incidencias.pedido-incidencia-card', ['incidencia' => $pedidoIncidencia])
                            @endforeach
                        </div>
                    </div>

                    <!-- Rechazada -->
                    <div class="col-md-3">
                        <h3>Rechazada</h3>
                        <div id="rechazada" class="pedidos-incidencias-list" wire:sortable-group.item-group="pedidosIncidencias">
                            @foreach($pedidosIncidencias->where('estado', 'rechazada')->sortByDesc('created_at') as $pedidoIncidencia)
                                @include('livewire.incidencias.pedido-incidencia-card', ['incidencia' => $pedidoIncidencia])
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
    <!-- Modal para editar la nota -->
<div wire:ignore.self class="modal fade" id="editNotapedidoModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Editar Nota del Responsable</h5>
                <button type="button" class="btn-close" data-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form wire:submit.prevent="">
                    <div class="mb-3">
                        <label for="notas" class="form-label">Nota</label>
                        <textarea class="form-control" id="notas" wire:model="notas" required></textarea>
                    </div>
                    <button type="submit" class="btn btn-success" data-dismiss="modal" wire:click="updateNotas({{ $editingIncidenciaId }}, 'pedido')">Guardar Nota</button>
                </form>
            </div>
        </div>
    </div>
</div>
<div wire:ignore.self class="modal fade" id="editNotaModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Editar Nota del Responsable</h5>
                <button type="button" class="btn-close" data-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form wire:submit.prevent="">
                    <div class="mb-3">
                        <label for="notas" class="form-label">Nota</label>
                        <textarea class="form-control" id="notas" wire:model="notas" required></textarea>
                    </div>
                    <button type="submit" class="btn btn-success" data-dismiss="modal" wire:click="updateNotas()">Guardar Nota</button>
                </form>
            </div>
        </div>
    </div>
</div>
    <!-- Estilos -->
    <style>
       .incidencias-list, .pedidos-incidencias-list {
    border: 2px dashed #007bff;
    padding: 15px;
    margin-bottom: 30px;
    border-radius: 10px;
    background-color: #f0f4ff;
    flex-grow: 1;
    transition: background-color 0.3s ease;
    min-height: 250px;
}

.row {
    display: flex;
    justify-content: space-between;
}

.col-md-3 {
    flex: 1;
    padding: 15px;
}

        .incidencias-list.drag-active , .pedidos-incidencias-list.drag-active {
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

        .card-actions {
            display: flex;
            gap: 10px;
        }

        .btn-warning:hover {
            background-color: #e0a800;
            color: white;
        }

        .btn-danger:hover {
            background-color: #c82333;
            color: white;
        }
    
        .observaciones-container {
         position: relative;
         max-height: 400px;
         overflow-y: auto; /* Habilitar el scroll vertical */
         padding-right: 10px; /* Espacio para el scrollbar */
         transition: max-height 0.4s ease-in-out;
         background-color: #f9f9f9;
         border: 1px solid #ddd;
         padding: 10px;
         border-radius: 5px;
         margin: 20px 0px;
         resize: vertical; /* Permitir que el usuario cambie el tamaño verticalmente */
     }
     
     .observaciones-container.expanded {
         max-height: 500px; /* Altura expandida cuando se muestra todo */
     }
     
     .observaciones-container p {
         margin: 0;
         margin-bottom: 20px;
         margin-top: 20px;
     }
     
     .scrollbar-custom {
         scrollbar-width: thin;
         scrollbar-color: #888 #f9f9f9;
     }
     
     .observaciones-container::-webkit-scrollbar {
         width: 8px;
     }
     
     .observaciones-container::-webkit-scrollbar-thumb {
         background-color: #888; 
         border-radius: 10px;
     }
     
     .observaciones-container::-webkit-scrollbar-thumb:hover {
         background-color: #555;
     }
     
     
     </style>
</div>

@section('scripts')
<script>
    function equalizeHeights() {
        const columns = document.querySelectorAll('.incidencias-list, .pedidos-incidencias-list');
        let maxHeight = 0;

        // Reset heights
        columns.forEach(column => {
            column.style.height = 'auto';
        });

        // Find the maximum height
        columns.forEach(column => {
            const height = column.offsetHeight;
            if (height > maxHeight) {
                maxHeight = height;
            }
        });

        // Set all columns to the maximum height
        columns.forEach(column => {
            column.style.height = maxHeight + 'px';
        });
    }

    document.addEventListener('DOMContentLoaded', function () {
        // Equalize heights on page load
        equalizeHeights();

        // Equalize heights after Livewire updates
        Livewire.hook('message.processed', () => {
            equalizeHeights();
        });

        // Equalize heights when incidencias are dragged
        document.querySelectorAll('.incidencias-list, .pedidos-incidencias-list').forEach(container => {
            container.addEventListener('sortupdate', function() {
                equalizeHeights();
            });
        });
    });
</script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        var triggerTabList = [].slice.call(document.querySelectorAll('#myTab button'));

        // Restaurar la pestaña activa desde localStorage si existe
        const savedTab = localStorage.getItem('activeTab');
        if (savedTab) {
            const tabTrigger = new bootstrap.Tab(document.getElementById(savedTab));
            tabTrigger.show();
        }

        // Configurar el comportamiento de las pestañas
        triggerTabList.forEach(function (triggerEl) {
            triggerEl.addEventListener('click', function (event) {
                event.preventDefault();
                const tabId = event.target.id;
                localStorage.setItem('activeTab', tabId); // Guardar la pestaña activa en localStorage
                const tabTrigger = new bootstrap.Tab(triggerEl);
                tabTrigger.show();
            });
        });

        // Escuchar eventos de Livewire para restaurar la pestaña activa después de una actualización
        Livewire.hook('message.processed', () => {
            const savedTab = localStorage.getItem('activeTab');
            if (savedTab) {
                const tabTrigger = new bootstrap.Tab(document.getElementById(savedTab));
                tabTrigger.show();
            }
        });
    });
</script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Sortable/1.14.0/Sortable.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Incidencias Normales
            const containersIncidencias = document.querySelectorAll('.incidencias-list');
            containersIncidencias.forEach(container => {
                new Sortable(container, {
                    group: 'incidencias',
                    animation: 150,
                    onEnd: function (evt) {
                        console.log(evt)
                        const itemId = evt.item.getAttribute('wire:sortable-group.item');
                        const newEstado = evt.to.id; // ID del nuevo estado
                        Livewire.emit('updateIncidenciaState', itemId, newEstado);
                    }
                });
            });

            // Incidencias de Pedidos
            const containersPedidos = document.querySelectorAll('.pedidos-incidencias-list');
            containersPedidos.forEach(container => {
                new Sortable(container, {
                    group: 'pedidosIncidencias',
                    animation: 150,
                    onEnd: function (evt) {
                        console.log(evt);
                        const itemId = evt.item.getAttribute('wire:sortable-group.item');
                        const newEstado = evt.to.id; // ID del nuevo estado
                        Livewire.emit('updatePedidoIncidenciaState', itemId, newEstado);
                    }
                });
            });
        });
    </script>
    <script>
        function pedidotoggleObservaciones(incidenciaId, text) {
            const p = document.getElementById('pedido-observaciones-' + incidenciaId);
            const button = document.getElementById('pedido-toggle-button-' + incidenciaId);
            const shortText = text.substring(0, 100) + '...';
    
            if (p.classList.contains('expanded')) {
                p.innerText = shortText;
                button.innerText = 'Ver más';
            } else {
                p.innerText = text;
                button.innerText = 'Ver menos';
            }
            
            p.classList.toggle('expanded');
        }
    </script>
    <script>
        function toggleObservaciones(incidenciaId, text) {
            const p = document.getElementById('observaciones-' + incidenciaId);
            const button = document.getElementById('toggle-button-' + incidenciaId);
            const shortText = text.substring(0, 100) + '...';
    
            if (p.classList.contains('expanded')) {
                p.innerText = shortText;
                button.innerText = 'Ver más';
            } else {
                p.innerText = text;
                button.innerText = 'Ver menos';
            }
            
            p.classList.toggle('expanded');
        }
    </script>
@endsection
