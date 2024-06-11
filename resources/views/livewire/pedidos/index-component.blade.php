<div class="container-fluid">
    <div class="page-title-box">
        <div class="row align-items-center">
            <div class="col-sm-6">
                <h4 class="page-title">PEDIDOS</h4>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-right">
                    <li class="breadcrumb-item"><a href="javascript:void(0);">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="javascript:void(0);">Pedidos</a></li>
                    <li class="breadcrumb-item active">Todos los pedidos</li>
                </ol>
            </div>
        </div> <!-- end row -->
    </div>
    <!-- end page-title -->
    <div class="row">
        <div class="col-12">
            <div class="card m-b-30">
                <div class="card-body">
                    <h4 class="mt-0 header-title">Listado de todos los pedidos</h4>
                    <p class="sub-title../plugins">Listado completo de todos nuestros pedidos, para editar o ver la informacion completa pulse el boton de Editar en la columna acciones.
                    </p>
                    <div class="d-flex gap-1 justify-content-end align-items-end flex-column flex-wrap" >
                        
                        <div class="d-flex gap-2 justify-content-end" id="filtrosSelect" >
                            <div class="filtro d-flex flex-column">
                                <label class=""  id="comerciales"  >
                                    Comerciales
                                    </label>
                                <select class="text-white bg-secondary rounded p-1" id="comercialesSelect"  wire:model="comercialSeleccionadoId">
                                    <option value="-1">Todos</option>
                                    @foreach ( $comerciales as $comercial )
                                        <option value='{{ $comercial->id }}'>{{ $comercial->name }}</option>
                                    @endforeach
                                    <!-- Agrega más ítems según las columnas de tu tabla -->
                                </select>                            
                            </div>
                            <div class="filtro d-flex flex-column" >
                                <label class=""  id="delegaciones"  >
                                Delegaciones
                                </label>
                                <select class="text-white bg-secondary rounded p-1" id="delegacionesSelect"  wire:model="delegacionSeleccionadaCOD">
                                    <option value='-1' >Todas</option>
                                    @foreach ( $delegaciones as $delegacion )
                                        <option value='{{  $delegacion->COD }}'>{{ $delegacion->nombre }}</option>
                                    @endforeach
                                    <!-- Agrega más ítems según las columnas de tu tabla -->
                                    </select>                            
                            </div>
                            <div class="filtro d-flex flex-column" >
                                <label class=""  id="clientes"  >
                                Clientes
                                </label>
                                <select class="text-white bg-secondary rounded p-1" id="clientesSelect"   wire:model="clienteSeleccionadoId">
                                    <option value='-1' >Todos</option>
                                    @foreach ( $clientes as $cliente )
                                        <option value='{{$cliente->id }}' >{{ $cliente->nombre }}</option>
                                    @endforeach
                                    <!-- Agrega más ítems según las columnas de tu tabla -->
                                    </select>                            
                            </div>
                            <div class="filtro d-flex flex-column" >
                                <label class=""  id="estado"  >
                                Estado
                                </label>
                                <select class="text-white bg-secondary rounded p-1" id="clientesSelect"   wire:model="estadoSeleccionado" >
                                    <option value='-1' >Todos</option>
                                    <option value='Entregado' >Entregado</option>
                                    <option value='Facturado' >Facturado</option>
                                    <option value='Preparación' >Preparación</option>
                                    <option value='Recibido' >Recibido</option>
                                    <option value='Aceptado en Almacén' >Almacén</option>
                                    <option value='Albarán' >Albarán</option>
                                    <option value='Rechazado' >Rechazado</option>
                                    <option value='En Ruta' >En Ruta</option>
                                    <!-- Agrega más ítems según las columnas de tu tabla -->
                                </select>                            
                            </div>
                            <div class="filtro d-flex flex-column" >
                                <label class=""  id="estado"  >
                                Fecha min
                                </label>
                                <input type="date" class="text-white bg-secondary rounded p-1" id="fecha_min"   wire:model="fecha_min" >
                               
                                        <!-- Agrega más ítems según las columnas de tu tabla -->
                            </div>
                            <div class="filtro d-flex flex-column" >
                                <label class=""  id="estado"  >
                                    Fecha max
                                </label>
                                <input type="date" class="text-white bg-secondary rounded p-1" id="fecha_max"   wire:model="fecha_max" >
                            </div>        
                            
                        </div>
                        @if(count($arrFiltrado) > 0)
                            <p>Filtrando por: @if(isset($arrFiltrado[1])) Comerciales @endif  @if(isset($arrFiltrado[2])) Delegaciones @endif  @if(isset($arrFiltrado[3])) Cliente @endif @if(isset($arrFiltrado[4])) Estado @endif</p>
                        @endif
                        <button class="btn btn-primary" wire:click="limpiarFiltros()"  @if($comercialSeleccionadoId == -1 && $delegacionSeleccionadaCOD == -1 && $clienteSeleccionadoId == -1 && $estadoSeleccionado == -1 && $fecha_min == null && $fecha_max == null) 
                        style="display:none"
                         @endif>Eliminar Filtros</button>

                    </div>
                    @if (count($pedidos) > 0)

                    <!-- Aquí comienza el botón desplegable para filtrar por columna -->
                    <div id="Botonesfiltros">
                        <div class="dropdown ">
                            <button class="btn btn-secondary dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                             Filtrar por Columna
                            </button>
                            <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                <a class="dropdown-item" href="#" data-column="0">Nº</a>
                                <a class="dropdown-item" href="#" data-column="1">Fecha</a>
                                <a class="dropdown-item" href="#" data-column="2">Cliente</a>
                                <a class="dropdown-item" href="#" data-column="3">Precio</a>
                                <a class="dropdown-item" href="#" data-column="4">Estado</a>
                                
                                <!-- Agrega más ítems según las columnas de tu tabla -->
                            </div>
                            <!-- Aquí termina el botón desplegable -->
                            <button class="btn btn-primary ml-2" id="clear-filter">Eliminar Filtro</button>
                        </div>
                    </div>
                    <div class="col-md-12 mt-4" x-data="{}" x-init="$nextTick(() => {
                        $('#datatable-buttons').DataTable({
                            responsive: true,
                            layout: {
                                topStart: 'buttons'
                            },
                            lengthChange: false,
                            pageLength: 30,
                            buttons: ['copy', 'excel', 'pdf', 'colvis'],
                            language: {
                                'lengthMenu': 'Mostrar _MENU_ registros por página',
                                'zeroRecords': 'No se encontraron registros',
                                'info': 'Mostrando página _PAGE_ de _PAGES_',
                                'infoEmpty': 'No hay registros disponibles',
                                'infoFiltered': '(filtrado de _MAX_ total registros)',
                                'search': 'Buscar:',
                            },
                    
                                            });
                                        })"
                                        wire:key='{{ rand() }}'>
                    <table id="datatable-buttons" class="table table-striped table-bordered dt-responsive nowrap" style="border-collapse: collapse; border-spacing: 0; width: 100%;" wire:key='{{ rand() }}'>
                        <thead>
                            <tr>
                                <th scope="col">Nº</th>
                                <th scope="col">Nº ped. Cliente</th>
                                <th scope="col">Delegación</th>
                                <th scope="col">Comercial</th>
                                <th scope="col">Fecha</th>
                                <th scope="col">Cliente: @mobile &nbsp; @endmobile</th>
                                <th scope="col">Precio: @mobile &nbsp; @endmobile</th>
                                <th scope="col">Estado: @mobile &nbsp; @endmobile</th>
                                <th scope="col">Acciones: @mobile &nbsp; @endmobile</th>
                            </tr>
                        </thead>
                        <tbody>
                            {{-- Recorre los pedidos --}}
                            @foreach ($pedidos as $presup)
                            <tr>
                                @if($presup->departamento_id == config('app.departamentos_pedidos')['Marketing']['id'])
                                    <td>{{ config('app.departamentos_pedidos')['Marketing']['pedido'] }}{{ $presup->id }}</td>
                                @else
                                    <td>{{ config('app.departamentos_pedidos')['General']['pedido'] }}{{ $presup->id }}</td>
                                @endif
                                <td>{{ $presup->npedido_cliente }}</td>
                                

                                <td>{{ $this->getDelegacion($presup->cliente_id) }}</td>
                                <td>{{ $this->getComercial($presup->cliente_id) }}</td>
                                <td>{{ $presup->fecha }}</td>
                                <td>{{ $this->getClienteNombre($presup->cliente_id) }}</td>
                                <td>{{ $presup->precio }} €</td>
                                <td>
                                    @if($this->getEstadoNombre($presup->estado) == "Recibido")
                                        <span class="badge badge-warning">Recibido</span>
                                    @elseif($this->getEstadoNombre($presup->estado) == "Aceptado en Almacén")
                                        <span class="badge badge-primary">Aceptado en Almacén</span>
                                    @elseif($this->getEstadoNombre($presup->estado) == "Preparación")
                                        <span class="badge badge-info">Preparación</span>
                                    @elseif($this->getEstadoNombre($presup->estado) == "Albarán")
                                        <span class="badge badge-secondary">Albarán</span>
                                    @elseif($this->getEstadoNombre($presup->estado) == "Entregado")
                                        <span class="badge badge-secondary">Entregado</span>
                                    @elseif($this->getEstadoNombre($presup->estado) == "Facturado")
                                        <span class="badge badge-success">Facturado</span>
                                    @elseif($this->getEstadoNombre($presup->estado) == "Rechazado")
                                        <span class="badge badge-danger">Rechazado</span>
                                    @elseif($this->getEstadoNombre($presup->estado) == "En Ruta")
                                        <span class="badge badge-secondary">En Ruta</span>
                                    @endif
                                </td>
                                @if(Auth::user()->role != 3 && Auth::user()->role != 2)
                                <td>
                                    <a href="pedidos-edit/{{ $presup->id }}" class="btn btn-primary">Ver/Editar</a>
                                    @if($this->albaranExiste($presup->id))
                                        <button class="btn btn-secondary" wire:click="albaran({{ $presup->id }})">Descargar albaran </button>
                                    @endif
                                </td>
                                @else
                                <td>
                                    <a href="pedidos-edit/{{ $presup->id }}" class="btn btn-primary">Ver</a>
                                    @if($this->albaranExiste($presup->id))
                                        <button class="btn btn-secondary" wire:click="albaran({{ $presup->id }})">Descargar albaran </button>
                                    @endif
                                </td>
                                @endif
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                    @else
                    <h6 class="text-center">No se encuentran pedidos disponibles</h6>
                    @endif
                </div>
            </div>
        </div>
    </div>




</div>


@section('scripts')
{{-- <script src="https://cdn.datatables.net/responsive/2.4.0/js/dataTables.responsive.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.2/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.3.4/js/dataTables.buttons.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.3.4/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.3.4/js/buttons.print.min.js"></script>
    <script>
        $(document).ready(function() {
            console.log('entro');
            $('#tablePedidos').DataTable({
                responsive: true,
                dom: 'Bfrtip',
                buttons: [
                    'copy', 'csv', 'excel', 'pdf', 'print'
                ],
                buttons: [{
                    extend: 'collection',
                    text: 'Export',
                    buttons: [{
                            extend: 'pdf',
                            className: 'btn-export'
                        },
                        {
                            extend: 'excel',
                            className: 'btn-export'
                        }
                    ],
                    className: 'btn btn-info text-white'
                }],
                "language": {
                    "lengthMenu": "Mostrando _MENU_ registros por página",
                    "zeroRecords": "Nothing found - sorry",
                    "info": "Mostrando página _PAGE_ of _PAGES_",
                    "infoEmpty": "No hay registros disponibles",
                    "infoFiltered": "(filtrado de _MAX_ total registros)",
                    "search": "Buscar:",
                    "paginate": {
                        "first": "Primero",
                        "last": "Ultimo",
                        "next": "Siguiente",
                        "previous": "Anterior"
                    },
                    "zeroRecords": "No se encontraron registros coincidentes",
                }
            });

            addEventListener("resize", (event) => {
                location.reload();
            })
        });
    </script> --}}
    <script src="../assets/js/jquery.slimscroll.js"></script>
<link href="https://cdn.datatables.net/v/bs5/jq-3.7.0/jszip-3.10.1/dt-2.0.3/b-3.0.1/b-colvis-3.0.1/b-html5-3.0.1/b-print-3.0.1/r-3.0.1/datatables.min.css" rel="stylesheet">
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>
<script src="https://cdn.datatables.net/v/bs5/jq-3.7.0/jszip-3.10.1/dt-2.0.3/b-3.0.1/b-colvis-3.0.1/b-html5-3.0.1/b-print-3.0.1/r-3.0.1/datatables.min.js"></script>
<!-- Responsive examples -->
{{-- <script src="../assets/pages/datatables.init.js"></script> --}}

@endsection
