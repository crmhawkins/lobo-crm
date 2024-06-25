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
                <h4 class="page-title">Ver Emails</h4>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-right">
                    <li class="breadcrumb-item"><a href="javascript:void(0);">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="javascript:void(0);">Ver emails</a></li>
                    <li class="breadcrumb-item active">Todos los Emails</li>
                </ol>
            </div>
        </div> <!-- end row -->
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card m-b-30">
                <div class="card-body">
                    <h4 class="mt-0 header-title">Listado de todos los Emails</h4>
                    <p class="sub-title../plugins">Listado completo de todos nuestros Emails
                
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
                                <label class=""  id="tipos"  >
                                Tipos
                                </label>
                                <select class="text-white bg-secondary rounded p-1" id="tipoEmailSeleccionadoId"   wire:model="tipoEmailSeleccionadoId">
                                    <option value='-1' >Todos</option>
                                    @foreach ( $tipoEmails as $tipo )
                                        <option value='{{$tipo->id }}' >{{ $tipo->nombre }}</option>
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
                        
                        <button class="btn btn-primary" wire:click="limpiarFiltros()"  @if($clienteSeleccionadoId == -1 && $fecha_min == null && $fecha_max == null) 
                        style="display:none"
                         @endif>Eliminar Filtros</button>

                    </div>
                    @if (count($registroEmails) > 0)

                    <!-- Aquí comienza el botón desplegable para filtrar por columna -->
                    
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
                    @if(count($registroEmails) > 0)
                        <div class="card m-b-30">
                            <div class="card-body">
                                <h5>Correos electrónicos enviados</h5>
                                <div class="row">
                                    <div class="col-12">
                                        <table id="datatable-buttons" class="table ms-3 table-striped table-bordered dt-responsive nowrap" wire:key='{{ rand() }}'>
                                            <thead>
                                                <tr>
                                                    <th>Correo</th>
                                                    <th>Cliente</th>
                                                    <th>Usuario</th>
                                                    <th>Fecha</th>
                                                    <th>Pedido o Factura </th>
                                                    <th>Tipo</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($registroEmails as $index => $email)
                                                    <tr>
                                                        <td>{{ $email->email }}</td>
                                                        <td>{{ $this->getCliente( $email->cliente_id) }}</td>
                                                        <td>{{ $this->getUser($email->user_id) }}</td>
                                                        <td>{{ $email->updated_at }}</td>
                                                        @if($email->pedido_id != null)
                                                            <td><a href="{{ route('pedidos.edit', $email->pedido_id) }}" target="blank_" class="btn btn-primary">Pedido nº {{$this->getPedido($email->pedido_id)}}</a></td>
                                                        @elseif($email->factura_id != null)
                                                            <td><a href="{{ route('facturas.edit', $email->factura_id) }}" target="blank_" class="btn btn-primary">Factura {{$this->getFactura($email->factura_id)}}</a></td>
                                                        @else
                                                            <td>-</td>
                                                        @endif
                                                        <td>{{ $this->getTipo($email->tipo_id) }}</td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
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