<div class="container-fluid">
    <style>
        @media(max-width: 1042px) {
            #filtrosSelect{
                display: flex;
                flex-wrap: wrap;
                gap: 14px !important;
            }
        }
        
    </style>
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
                        <button class="btn btn-primary" wire:click="limpiarFiltros()"  @if( $clienteSeleccionadoId == -1 && $fecha_min == null && $fecha_max == null) style="display:none" @endif>Eliminar Filtros</button>

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
                                }
                            });
                        })"
                        wire:key='{{ rand() }}'>
                            <table id="datatable-buttons" class="table table-striped table-bordered dt-responsive nowrap" style="border-collapse: collapse; border-spacing: 0; width: 100%;" wire:key='{{ rand() }}'>
                                <thead>
                                    <tr>
                                        <th scope="col">Nº</th>
                                        <th scope="col">Nº ped. Cliente</th>
                                        <th scope="col">Fecha</th>
                                        <th scope="col">Cliente: @mobile &nbsp; @endmobile</th>
                                        <th scope="col">Precio: @mobile &nbsp; @endmobile</th>
                                        <th scope="col">Total: @mobile &nbsp; @endmobile</th>
                                        <th scope="col">Acciones: @mobile &nbsp; @endmobile</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    {{-- Recorre los pedidos --}}
                                    @foreach ($pedidos as $presup)
                                        <tr>
                                            <td>{{ $presup->id }}</td>
                                            <td>{{ $presup->npedido_cliente }}</td>
                                            

                                            <td>{{ $presup->fecha }}</td>
                                            <td><a target="blank_" href="{{ route('comercial.editcliente', ['id' => $presup->cliente_id]) }}" class="btn btn-primary btn-sm fw-bold"> {{ $this->getClienteNombre($presup->cliente_id) }}</a></td>
                                            <td>{{ $presup->precio }} €</td>
                                            <td>{{ $presup->total }} €</td>
                                            
                                            <td>
                                                <a href="/admin/comercial/edit-pedido/{{ $presup->id }}" class="btn btn-primary">Ver/Editar</a>
                                            </td>
                                            
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

    <script src="../assets/js/jquery.slimscroll.js"></script>
<link href="https://cdn.datatables.net/v/bs5/jq-3.7.0/jszip-3.10.1/dt-2.0.3/b-3.0.1/b-colvis-3.0.1/b-html5-3.0.1/b-print-3.0.1/r-3.0.1/datatables.min.css" rel="stylesheet">
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>
<script src="https://cdn.datatables.net/v/bs5/jq-3.7.0/jszip-3.10.1/dt-2.0.3/b-3.0.1/b-colvis-3.0.1/b-html5-3.0.1/b-print-3.0.1/r-3.0.1/datatables.min.js"></script>


@endsection
