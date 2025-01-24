<div class="container-fluid mx-auto">
    <style>
        @media (max-width: 760px){
            .botones{
                width: 100%;
                margin: 10px;
                display: block;
            }
        }
        </style>
                                        

    <div class="page-title-box">
        <div class="row align-items-center">
            <div class="col-sm-6">
                <h4 class="page-title">ALMACÉN</span></h4>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-right">
                    <li class="breadcrumb-item"><a href="javascript:void(0);">Dashboard</a></li>
                    {{-- <li class="breadcrumb-item"><a href="javascript:void(0);">Contratos</a></li> --}}
                    <li class="breadcrumb-item active">Almacén</li>
                </ol>
            </div>
        </div> <!-- end row -->
    </div>
    <div class="row">
        <div class="col-12">
            <div class="card m-b-30">
                <div class="card-body">
                    <h4 class="mt-0 header-title">Pedidos pendientes</h4>
                    <p class="sub-title../plugins">Pedidos en cola para su comprobación, preparación y envío.
                    </p>
                    

                    @if (count($pedidos_pendientes) > 0)
                    <!-- Aquí comienza el botón desplegable para filtrar por columna -->
                    <div id="Botonesfiltros">
                        <div class="dropdown ">
                            <button class="btn btn-secondary dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                             Filtrar por Columna
                            </button>
                            <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                <a class="dropdown-item" href="#" data-column="0">Nº</a>
                                <a class="dropdown-item" href="#" data-column="1">Cliente</a>
                                <a class="dropdown-item" href="#" data-column="2">Fecha</a>
                                <a class="dropdown-item" href="#" data-column="3">Precio</a>
                                <a class="dropdown-item" href="#" data-column="4">Tipo de pedido</a>
                                <!-- Agrega más ítems según las columnas de tu tabla -->
                            </div>
                            <!-- Aquí termina el botón desplegable -->
                            <button class="btn btn-primary ml-2" id="clear-filter">Eliminar Filtro</button>
                        </div>
                    </div>
                    @php
                    $mostrarElemento = Auth::user()->isdirectorcomercial();
                    @endphp
                        <div class="col-md-12 mt-4" x-data="{}" x-init="$nextTick(() => {
                            $('#datatable-buttons_pendientes').DataTable({
                                responsive: true,
                                layout: {
                                    topStart: 'buttons'
                                },
                                lengthChange: false,
                                pageLength: 30,
                                buttons: ['copy', 'excel', 'pdf', 'colvis'],
                                stateSave: true, // Habilita el guardado del estado
                                language: {
                                    'lengthMenu': 'Mostrar _MENU_ registros por página',
                                    'zeroRecords': 'No se encontraron registros',
                                    'info': 'Mostrando página _PAGE_ de _PAGES_',
                                    'infoEmpty': 'No hay registros disponibles',
                                    'infoFiltered': '(filtrado de _MAX_ total registros)',
                                    'search': 'Buscar:',
                                }
                            });
                        })"
                        wire:key='{{ rand() }}'>
                            <table id="datatable-buttons_pendientes" class="table table-striped table-bordered dt-responsive nowrap"
                                style="border-collapse: collapse; border-spacing: 0; width: 100%;" wire:key='{{ rand() }}'>
                                <thead>
                                    <tr>
                                        <th scope="col">ID</th>
                                        <th scope="col">Pedido Nº</th>
                                        <th scope="col">Nº Ped. Cliente</th>
                                        <th scope="col">Cliente</th>
                                        <th scope="col">Almacen</th>
                                        <th scope="col">Fecha</th>
                                        <th scope="col">Precio</th>
                                        <th scope="col">Tipo de pedido</th>
                                        <th scope="col">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($pedidos_pendientes as $pedido)
                                        
                                        <tr>
                                            <td>{{ $pedido->id }}</td>
                                            @if($pedido->departamento_id)
                                                <td>M{{ $pedido->numero ? $pedido->numero : $pedido->id }}</td>
                                            @else
                                                <td>G{{ $pedido->numero ? $pedido->numero : $pedido->id }}</td>
                                            @endif
                                            <td>{{ $pedido->npedido_cliente }}</td>

                                            <td>{{ $this->getNombreCliente($pedido->cliente_id) }}</td>
                                            <td>{{ $this->getAlmacen($pedido->almacen_id) }}</td>

                                            <td>{{ $pedido->fecha }}</td>
                                            <td>{{ $pedido->precio }}€</td>
                                            <td>
                                                @switch($pedido->tipo_pedido_id)
                                                @case(0)
                                                    Albarán y factura
                                                    @break
                                                @case(1)
                                                    Albarán sin factura
                                                    @break
                                                @default
                                                    Tipo de pedido no reconocido
                                            @endswitch
                                            </td>
                                            <td>
                                                <a onclick="prepararPedido({{ $pedido->id }})" class="btn btn-primary"  style="color: white;">Preparar pedido</a>
                                                <a href="pedidos-edit/{{ $pedido->id }}" class="btn btn-warning">Ver/Editar</a>
                                            </td>
                                        </tr>

                                    @endforeach
                                </tbody>
                            </table>
                    </div>
                    @else
                        <h2 class="text-center" style="color: #35a8e0 !important">No hay pedidos para preparar</h2>
                    @endif
                </div>
            </div>
            <div class="card m-b-30">
                <div class="card-body">
                    <h4 class="mt-0 header-title">Pedidos en preparación</h4>
                    
                    @if (count($pedidos_preparacion) > 0)

                    <div id="Botonesfiltros-preparacion">
                        <div class="dropdown ">
                            <button class="btn btn-secondary dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                             Filtrar por Columna
                            </button>
                            <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                <a class="dropdown-item" href="#" data-column="0">Nº</a>
                                <a class="dropdown-item" href="#" data-column="1">Cliente</a>
                                <a class="dropdown-item" href="#" data-column="2">Fecha</a>
                                <a class="dropdown-item" href="#" data-column="3">Precio</a>
                                <a class="dropdown-item" href="#" data-column="4">Tipo de pedido</a>
                                <!-- Agrega más ítems según las columnas de tu tabla -->
                            </div>
                            <!-- Aquí termina el botón desplegable -->
                            <button class="btn btn-primary ml-2" id="clear-filter-preparacion">Eliminar Filtro</button>
                        </div>
                    </div>
                    <div class="col-md-12 mt-4" x-data="{}" x-init="$nextTick(() => {
                        $('#datatable-buttons_preparacion').DataTable({
                            responsive: true,
                            layout: {
                                topStart: 'buttons'
                            },
                            lengthChange: false,
                            pageLength: 30,
                            buttons: ['copy', 'excel', 'pdf', 'colvis'],
                             stateSave: true, // Habilita el guardado del estado
                            language: {
                                'lengthMenu': 'Mostrar _MENU_ registros por página',
                                'zeroRecords': 'No se encontraron registros',
                                'info': 'Mostrando página _PAGE_ de _PAGES_',
                                'infoEmpty': 'No hay registros disponibles',
                                'infoFiltered': '(filtrado de _MAX_ total registros)',
                                'search': 'Buscar:',
                            }
                        });
                    })"
                    wire:key='{{ rand() }}'>
                        <table id="datatable-buttons_preparacion" class="table table-striped table-bordered dt-responsive nowrap"
                            style="border-collapse: collapse; border-spacing: 0; width: 100%;" wire:key='{{ rand() }}'>
                            <thead>
                                <tr>
                                    <th scope="col">ID</th>
                                    <th scope="col">Pedido Nº</th>
                                    <th scope="col">Nº Ped. Cliente</th>
                                    <th scope="col">Cliente</th>
                                    <th scope="col">Almacen</th>
                                    <th scope="col">Fecha</th>
                                    <th scope="col">Precio</th>
                                    <th scope="col">Tipo de pedido</th>
                                    <th scope="col">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($pedidos_preparacion as $pedido)
                                    <tr>
                                        <td>{{ $pedido->id }}</td>
                                        @if($pedido->departamento_id)
                                            <td>M{{ $pedido->numero ? $pedido->numero : $pedido->id }}</td>
                                        @else
                                            <td>G{{ $pedido->numero ? $pedido->numero : $pedido->id }}</td>
                                        @endif
                                        <td>{{ $pedido->npedido_cliente }}</td>
                                        <td>{{ $this->getNombreCliente($pedido->cliente_id) }}</td>
                                        <td>{{ $this->getAlmacen($pedido->almacen_id) }}</td>
                                        <td>{{ $pedido->fecha }}</td>
                                        <td>{{ $pedido->precio }}€</td>
                                        <td>
                                            @switch($pedido->tipo_pedido_id)
                                            @case(0)
                                                Albarán y factura
                                                @break
                                            @case(1)
                                                Albarán sin factura
                                                @break
                                            @default
                                                Tipo de pedido no reconocido
                                        @endswitch
                                        </td>
                                        <td> 

                                            <a onclick="comprobarStockPedido({{ $pedido->id }})" class="btn btn-primary botones" style="color: white;">Comprobar pedido</a>
                                            @if(!$this->pedidoHasAlbaran($pedido->id))
                                                <a href="almacen-create/{{ $pedido->id }}" class="btn btn-secondary botones">Generar albarán</a>
                                            @else
                                                <a onclick="mostrarAlbaran({{ $pedido->id }}, false)" class="btn btn-dark botones"  style="color: white;">Descargar albarán</a>
                                                <button onclick="pasarEnviado('{{ $pedido->id }}')" class="btn btn-secondary botones" style="color: white;">Pasar a enviado</button>
                                            @endif
                                            <a href="pedidos-edit/{{ $pedido->id }}" class="btn btn-warning botones">Ver/Editar</a>
                                            <button onclick="volverPendientes({{ $pedido->id }})" class="btn btn-danger botones" style="color: white;">Volver a pendientes</button>

                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @else
                        <h2 class="text-center" style="color: #35a8e0 !important">No hay pedidos para preparar</h2>
                    @endif

                </div>
            </div>
            <div class="card m-b-30">
                <div class="card-body">
                    <h4 class="mt-0 header-title">Pedidos Enviados</h4>

                    @if (count($pedidos_enviados) > 0)

                    <div id="Botonesfiltros-enviados">
                        <div class="dropdown ">
                            <button class="btn btn-secondary dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                             Filtrar por Columna
                            </button>
                            <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                <a class="dropdown-item" href="#" data-column="0">Nº</a>
                                <a class="dropdown-item" href="#" data-column="1">Cliente</a>
                                <a class="dropdown-item" href="#" data-column="2">Fecha</a>
                                <a class="dropdown-item" href="#" data-column="3">Precio</a>
                                <a class="dropdown-item" href="#" data-column="4">Tipo de pedido</a>
                                <!-- Agrega más ítems según las columnas de tu tabla -->
                            </div>
                            <!-- Aquí termina el botón desplegable -->
                            <button class="btn btn-primary ml-2" id="clear-filter-enviados">Eliminar Filtro</button>
                            
                        </div>
                    </div>
                    <div wire:ignore.self class="modal fade" id="enRutaModal" tabindex="-1" role="dialog">
                        <div class="modal-dialog"
                            style="min-width: 25vw !important; align-self: center !important; margin-top: 0 !important;">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title">Añadir Datos de Ruta</h5>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <div class="modal-body">
                                    
                                    <div class="form-group row">
                                        <label for="fecha_salida" class="col-sm-4 col-form-label">Fecha de salida</label>
                                        <div class="col-sm-8">
                                            <input type="date" class="form-control" id="fecha_salida" wire:model="fecha_salida">
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label for="empresa_transporte" class="col-sm-4 col-form-label">Empresa de transporte</label>
                                        <div class="col-sm-8">
                                            <input type="text" class="form-control" id="empresa_transporte" wire:model="empresa_transporte">
                                        </div>
                                    </div>

                                    @if(isset($pedidoEnRutaId))
                                    
                                        <button onclick="enRuta({{ $pedidoEnRutaId }})" class="btn btn-success mt-2">Pedido {{$pedidoEnRutaId}} en Ruta</button>
                                    @endif

                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div wire:ignore.self class="modal fade" id="enFechaEntrega" tabindex="-1" role="dialog">
                        <div class="modal-dialog"
                            style="min-width: 25vw !important; align-self: center !important; margin-top: 0 !important;">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title">Añadir Fecha entrega</h5>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <div class="modal-body">
                                    
                                    <div class="form-group row">
                                        <label for="fecha_salida" class="col-sm-4 col-form-label">Fecha de entrega</label>
                                        <div class="col-sm-8">
                                            <input type="date" class="form-control" id="fecha_entrega" wire:model="fecha_entrega">
                                        </div>
                                    </div>
                                    

                                    @if(isset($pedidoEnRutaId))
                                    
                                        <button onclick="fechaEntrega({{ $pedidoEnRutaId }})" class="btn btn-success mt-2">Añadir entrega a Pedido {{$pedidoEnRutaId}}</button>
                                    @endif

                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div wire:ignore.self class="modal fade" id="enviarEmailModal" tabindex="-1" role="dialog">
                        <div class="modal-dialog"
                            style="min-width: 25vw !important; align-self: center !important; margin-top: 0 !important;">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title">Enviar Email al Transportista</h5>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <div class="modal-body">
                                    
                                    <div class="form-group row">
                                        <label for="email_transporte" class="col-sm-4 col-form-label">Email</label>
                                        <div class="col-sm-8">
                                            <input type="text" class="form-control" id="email_transporte" wire:model="email_transporte">
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label for="observaciones_transporte" class="col-sm-4 col-form-label">Observaciones</label>
                                        <div class="col-sm-8">
                                            <input type="text" class="form-control" id="observaciones_transporte" wire:model="observaciones_transporte">
                                        </div>
                                    </div>

                                    @if(isset($pedidoEnRutaId))
                                        <button onclick="enviarEmailTransporte({{ $pedidoEnRutaId }})" class="btn btn-success mt-2">Enviar email pedido {{ $pedidoEnRutaId }}</button>
                                    @endif

                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-12 mt-4" x-data="{}" x-init="$nextTick(() => {
                        $('#datatable-buttons_enviados').DataTable({
                            responsive: true,
                            layout: {
                                topStart: 'buttons'
                            },
                            lengthChange: false,
                            pageLength: 30,
                            buttons: ['copy', 'excel', 'pdf', 'colvis'],
                             stateSave: true, // Habilita el guardado del estado
                            language: {
                                'lengthMenu': 'Mostrar _MENU_ registros por página',
                                'zeroRecords': 'No se encontraron registros',
                                'info': 'Mostrando página _PAGE_ de _PAGES_',
                                'infoEmpty': 'No hay registros disponibles',
                                'infoFiltered': '(filtrado de _MAX_ total registros)',
                                'search': 'Buscar:',
                            }
                        });
                    })"
                    wire:key='{{ rand() }}'>
                        <table id="datatable-buttons_enviados" class="table table-striped table-bordered dt-responsive nowrap"
                            style="border-collapse: collapse; border-spacing: 0; width: 100%;" wire:key='{{ rand() }}'>
                            <thead>
                                <tr>
                                    <th scope="col">ID</th>
                                    <th scope="col">Pedido Nº</th>
                                    <th scope="col">Nº Ped. Cliente</th>
                                    <th scope="col">Cliente</th>
                                    <th scope="col">Almacen</th>
                                    <th scope="col">Fecha</th>
                                    <th scope="col">Precio</th>
                                    <th scope="col">Fecha de salida</th>
                                    <th scope="col">Tipo de pedido</th>
                                    <th scope="col">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($pedidos_enviados as $pedido)
                                    <tr>
                                        <td>{{ $pedido->id }}</td>
                                        @if($pedido->departamento_id)
                                            <td>M{{ $pedido->numero ? $pedido->numero : $pedido->id }}</td>
                                        @else
                                            <td>G{{ $pedido->numero ? $pedido->numero : $pedido->id }}</td>
                                        @endif
                                        <td>{{ $pedido->npedido_cliente }}</td>
                                        <td>{{ $this->getNombreCliente($pedido->cliente_id) }}</td>
                                        <td>{{ $this->getAlmacen($pedido->almacen_id) }}</td>
                                        <td>{{ $pedido->fecha }}</td>
                                        <td>{{ $pedido->precio }}€</td>
                                        <td>{{$pedido->fecha_salida}}</td>

                                        <td>
                                            @switch($pedido->tipo_pedido_id)
                                            @case(0)
                                                Albarán y factura
                                                @break
                                            @case(1)
                                                Albarán sin factura
                                                @break
                                            @default
                                                Tipo de pedido no reconocido
                                        @endswitch
                                        </td>

                                        <td>
                                                <a onclick="mostrarAlbaran({{ $pedido->id }}, true)" class="btn btn-primary botones"  style="color: white;">Descargar albarán</a>
                                            @if ($pedido->estado ==8 )
                                                @if($pedido->tipo_pedido_id == 0)
                                                    @if($pedido->fecha_entrega == null)
                                                        <button  onclick="asignarPedidoEnRutaId('{{ $pedido->id }}')" data-toggle="modal" data-target="#enFechaEntrega" class="btn btn-success botones" style="color: white;">Fecha entrega</button>
                                                    @endif
    
                                                    @if($this->hasFactura($pedido->id))
                                                        <a href="facturas-edit/{{ $this->hasFactura($pedido->id) }}" class="btn btn-success botones">Ver Factura</a>
                                                    @else
                                                        <a href="facturas-create/{{ $pedido->id }}" class="btn btn-danger botones">Crear Factura</a>
                                                    @endif
                                                    @if($this->hasFactura($pedido->id) && $pedido->fecha_entrega != null)
                                                        <button  onclick="completarPedido({{ $pedido->id }})" class="btn btn-dark botones" style="color: white;">Completar Pedido</button>
                                                    @endif

                                                @elseif($pedido->tipo_pedido_id != 0)
                                                    <button  onclick="asignarPedidoEnRutaId('{{ $pedido->id }}')" data-toggle="modal" data-target="#enFechaEntrega" class="btn btn-success botones" style="color: white;">Fecha entrega</button>
                                                @endif
                                            @else
                                                
                                                <button  onclick="asignarPedidoEnRutaId('{{ $pedido->id }}')" data-toggle="modal" data-target="#enRutaModal" class="btn btn-secondary botones" style="color: white;">Pedido En Ruta</button>
                                            @endif
                                            <button  onclick="asignarPedidoEnRutaId('{{ $pedido->id }}')" data-toggle="modal" data-target="#enviarEmailModal" class="btn btn-secondary botones" style="color: white;">Enviar Email Transporte</button>

                                            <a href="pedidos-edit/{{ $pedido->id }}" class="btn btn-warning botones">Ver/Editar</a>
                                            <button  onclick="alertaVolverPreparacion('{{ $pedido->id }}')" data-toggle="modal" data-target="#volverPreparacionModal" class="btn btn-danger botones" style="color: white;">Volver a Preparación</button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>

                    </div>
                    @else
                        <h2 class="text-center" style="color: #35a8e0 !important">No hay pedidos en proceso por enviar
                        </h2>

                    @endif

                </div>
            </div>
        </div>
    </div>
</div>
@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/signature_pad@4.0.0/dist/signature_pad.umd.min.js"></script>

    <script>
    function enRuta(id) {
        // Suponiendo que tu descarga se realiza aquí

        window.livewire.emit('enRuta', id);
        //window.livewire.emit('enRuta', id);
        setTimeout(() => {
            location.reload()
        }, 1000);
    }

    function completarPedido(id) {
        // Suponiendo que tu descarga se realiza aquí

        window.livewire.emit('completarPedido', id);
        //window.livewire.emit('enRuta', id);
        setTimeout(() => {
            location.reload()
        }, 1000);
    }

    function alertaVolverPreparacion(id) {
        // Suponiendo que tu descarga se realiza aquí

        window.livewire.emit('alertaVolverPreparacion', id);
        //window.livewire.emit('enRuta', id);
        
    }

    function pasarEnviado(id) {
        // Suponiendo que tu descarga se realiza aquí

        window.livewire.emit('AlertapasarEnviado', id);
        //window.livewire.emit('enRuta', id);
       
    }

    function volverPendientes(id) {
        // Suponiendo que tu descarga se realiza aquí

        window.livewire.emit('AlertaVolverPendientes', id);
        //window.livewire.emit('enRuta', id);
        
    }

    function fechaEntrega(id) {
        // Suponiendo que tu descarga se realiza aquí

        window.livewire.emit('fechaEntrega', id);
        //window.livewire.emit('enRuta', id);
        setTimeout(() => {
            location.reload()
        }, 1000);
    }

    function asignarPedidoEnRutaId(id) {
        window.livewire.emit('asignarPedidoEnRutaId', id);
    }

    function enviarEmailTransporte(id) {
        // Suponiendo que tu descarga se realiza aquí

        window.livewire.emit('enviarEmailTransporte', id);
        //window.livewire.emit('enRuta', id);
        // setTimeout(() => {
        //     location.reload()
        // }, 1000);
    }

    </script>
    <script>
        function mostrarAlbaran(id, conIva) {
            // Suponiendo que tu descarga se realiza aquí
            window.livewire.emit('mostrarAlbaran', id, conIva);
            setTimeout(() => {
                location.reload()
            }, 5000);
        }
    </script>
    <script>
        function prepararPedido(id) {
            // Suponiendo que tu descarga se realiza aquí
            window.livewire.emit('prepararPedido', id);
            setTimeout(() => {
                location.reload()
            }, 1000);
        }
    </script>
    <script>
        function comprobarStockPedido(id) {
            // Suponiendo que tu descarga se realiza aquí
            window.livewire.emit('comprobarStockPedido', id);
        }

        
    </script>
    <script src="../assets/js/jquery.slimscroll.js"></script>
    <link href="https://cdn.datatables.net/v/bs5/jq-3.7.0/jszip-3.10.1/dt-2.0.3/b-3.0.1/b-colvis-3.0.1/b-html5-3.0.1/b-print-3.0.1/r-3.0.1/datatables.min.css" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>
    <script src="https://cdn.datatables.net/v/bs5/jq-3.7.0/jszip-3.10.1/dt-2.0.3/b-3.0.1/b-colvis-3.0.1/b-html5-3.0.1/b-print-3.0.1/r-3.0.1/datatables.min.js"></script>
    <!-- Responsive examples -->
    {{-- <script src="../assets/pages/datatables.init.js"></script> --}}
@endsection

