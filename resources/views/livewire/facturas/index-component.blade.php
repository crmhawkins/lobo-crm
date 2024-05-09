<div class="container-fluid mx-auto">
    <div class="page-title-box">
        <div class="row align-items-center">
            <div class="col-sm-6">
                <h4 class="page-title">TODAS LAS FACTURAS</span></h4>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-right">
                    <li class="breadcrumb-item"><a href="javascript:void(0);">Dashboard</a></li>
                    {{-- <li class="breadcrumb-item"><a href="javascript:void(0);">Contratos</a></li> --}}
                    <li class="breadcrumb-item active">Facturas</li>
                </ol>
            </div>
        </div> <!-- end row -->
    </div>
    <div class="row">
        <div class="col-12">
            <div class="card m-b-30">
                <div class="card-body" id="table-container">
                    <h4 class="mt-0 header-title">Listado de todas las facturas</h4>
                    <p class="sub-title../plugins">Listado completo de todas nuestros facturas, para editar o ver la
                        informacion completa pulse el boton de Editar en la columna acciones.
                    </p>
                    @if(Auth::user()->role != 3)
                        <div class="col-12 mb-5">
                            <a href="facturas-create" class="btn btn-lg w-100 btn-primary">Crear factura</a>
                        </div>
                    @endif
                    <div class="d-flex gap-1 justify-content-end align-items-end flex-column" >
                        
                        <div class="d-flex gap-2 justify-content-end" >
                            <div class="filtro d-flex flex-column">
                                <label class=""  id="comerciales"  >
                                    Filtrar por Comerciales
                                    </label>
                                <select class="text-white bg-secondary rounded p-1" id="comercialesSelect" wire:change="onChangeFiltrado(1)" wire:model="comercialSeleccionadoId">
                                    <option value="-1">Todos</option>
                                    @foreach ( $comerciales as $comercial )
                                        <option value='{{ $comercial->id }}'>{{ $comercial->name }}</option>
                                    @endforeach
                                    <!-- Agrega más ítems según las columnas de tu tabla -->
                                </select>                            
                            </div>
                            <div class="filtro d-flex flex-column" >
                                <label class=""  id="delegaciones"  >
                                Filtrar por Delegaciones
                                </label>
                                <select class="text-white bg-secondary rounded p-1" id="delegacionesSelect" wire:change="onChangeFiltrado(2)" wire:model="delegacionSeleccionadaCOD">
                                    <option value='-1' >Todas</option>
                                    @foreach ( $delegaciones as $delegacion )
                                        <option value='{{  $delegacion->COD }}'>{{ $delegacion->nombre }}</option>
                                    @endforeach
                                    <!-- Agrega más ítems según las columnas de tu tabla -->
                                    </select>                            
                            </div>
                            <div class="filtro d-flex flex-column" >
                                <label class=""  id="clientes"  >
                                Filtrar por Clientes
                                </label>
                                <select class="text-white bg-secondary rounded p-1" id="clientesSelect"  wire:change="onChangeFiltrado(3)" wire:model="clienteSeleccionadoId">
                                    <option value='-1' >Todos</option>
                                    @foreach ( $clientes as $cliente )
                                        <option value='{{$cliente->id }}' >{{ $cliente->nombre }}</option>
                                    @endforeach
                                    <!-- Agrega más ítems según las columnas de tu tabla -->
                                    </select>                            
                            </div>
                            
                        </div>
                        @if(count($arrFiltrado) > 0)
                            <p>Filtrando por: @if(isset($arrFiltrado[1])) Comerciales @endif  @if(isset($arrFiltrado[2])) Delegaciones @endif  @if(isset($arrFiltrado[3])) Cliente @endif</p>
                        @endif
                        <button class="btn btn-primary" id="clear"  @if(count($arrFiltrado) == 0) style="display:none" @endif>Eliminar Filtros</button>

                    </div>
                    @if (isset($facturas) && count($facturas) > 0)

                            <!-- Aquí comienza el botón desplegable para filtrar por columna -->
                        <div id="Botonesfiltros" class="d-flex gap-2">
                            <div class="dropdown ">
                                <button class="btn btn-secondary dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                Filtrar por Columna
                                </button>
                                <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                    <a class="dropdown-item" href="#" data-column="0">Número</a>
                                    <a class="dropdown-item" href="#" data-column="1">P.asociado</a>
                                    <a class="dropdown-item" href="#" data-column="2">Cliente</a>
                                    <a class="dropdown-item" href="#" data-column="3">Total</a>
                                    <a class="dropdown-item" href="#" data-column="4">M.pago</a>
                                    <!-- Agrega más ítems según las columnas de tu tabla -->
                                </div>
                                <!-- Aquí termina el botón desplegable -->
                                <button class="btn btn-primary ml-2" id="clear-filter">Eliminar Filtro</button>
                            </div>
                            
                        </div>
                        
                        <table id="datatable-buttons" class="table table-striped table-bordered dt-responsive nowrap" style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                            <thead>
                                    <tr>
                                        <th scope="col">Número</th>
                                        <th scope="col">P.asociado</th>
                                        <th scope="col">Comercial</th>
                                        <th scope="col">Delegacion</th>
                                        <th scope="col">Cliente</th>
                                        <th scope="col">F.emisión</th>
                                        <th scope="col">F.vencimiento</th>
                                        <th scope="col">Importe</th>
                                        <th scope="col">IVA</th>
                                        <th scope="col">Total(Con IVA)</th>
                                        <th scope="col">M.pago</th>
                                        <th scope="col">Estado</th>
                                        <th scope="col">Acciones</th>
                                    </tr>
                            </thead>
                                <tbody>
                                    
                                    @foreach ($facturas as $key=>$fact)
                                        
                                        <tr>
                                            
                                            <td>{{ $fact->numero_factura }}</td>
                                            @if ($fact->pedido_id == 0 || $pedidos->where('id', $fact->pedido_id) == null)
                                                <td>Sin pedido</td>
                                            @else
                                                <td><a href="{{ route('pedidos.edit', ['id' => $fact->pedido_id]) }}"
                                                        class="btn btn-primary" target="_blank"> &nbsp;Pedido
                                                        {{ $fact->pedido_id }}</a></td>
                                            @endif
                                        
                                            <td>{{ $this->getComercial($fact->cliente_id)}}</td>
                                            <td>{{ $this->getDelegacion($fact->cliente_id)}}</td>

                                            <td>{{ $this->getCliente($fact->cliente_id)->nombre}}</td>

                                            <td>{{ $fact->fecha_emision }}</td>
                                            <td>@if((new DateTime($fact->fecha_vencimiento)) <= (new DateTime()) && $fact->estado != 'Pagado')
                                                    <span class="badge badge-danger">{{ $fact->fecha_vencimiento }}</span>
                                                @elseif($fact->estado == 'Pagado')
                                                    <span class="badge badge-success">{{ $fact->fecha_vencimiento }}</span>
                                                @else
                                                    <span class="badge badge-info">{{ $fact->fecha_vencimiento }}</span>
                                                @endif
                                            </td>
                                            @if(isset($fact->descuento))
                                            <td>{{number_format( $fact->precio * (1 + (-($fact->descuento) /100)),2) }}€
                                            </td>
                                            <td>{{number_format(($fact->precio*(1 + (-($fact->descuento) /100))) * 0.21, 2)}}€
                                            </td>
                                            <td>{{number_format(($fact->precio*(1 + (-($fact->descuento) /100))) * 1.21, 2)}}€
                                            </td>
                                            @else
                                            <td>{{$fact->precio }}€
                                            </td>
                                            <td>{{number_format($fact->precio *  0.21, 2)}}€
                                            </td>
                                            <td>{{number_format($fact->precio * 1.21, 2)}}€
                                            </td>
                                            @endif
                                            <td >
                                                @switch($fact->metodo_pago)
                                                    @case("giro_bancario")
                                                        Giro Bancario
                                                        @break
                                                    @case("confirming")
                                                        Confirming
                                                        @break
                                                    @case("transferencia")
                                                        Transferencia
                                                        @break
                                                    @case("pagare")
                                                        Pagaré
                                                        @break
                                                    @default
                                                    {{ $fact->metodo_pago }}
                                                @endswitch
                                                
                                            </td>
                                            <td>@switch($fact->estado)
                                                @case('Pendiente')
                                                <span class="badge badge-warning">{{ $fact->estado }}</span>
                                                    @break
                                                @case('Pagado')
                                                <span class="badge badge-success">{{ $fact->estado }}</span>
                                                    @break
                                                @case('Cancelado')
                                                <span class="badge badge-danger">{{ $fact->estado }}</span>
                                                    @break
                                                @default
                                                <span class="badge badge-info">{{ $fact->estado }}</span>
                                            @endswitch</td>
                                            <td> 
                                                <a href="facturas-edit/{{ $fact->id }}" class="btn btn-primary">
                                                    @if(Auth::user()->role == 3)
                                                        Ver
                                                    @else
                                                        Ver/Editar
                                                    @endif
                                                </a>
                                                <button  onclick="descargarFactura({{ $fact->id }}, true)" class="btn btn-primary" style="color: white;">Factura Con IVA</button>
                                                <button  onclick="descargarFactura({{ $fact->id }}, false)" class="btn btn-primary" style="color: white;">Factura Sin IVA</button>
                                                <button  onclick="mostrarAlbaran({{ $fact->id }}, true)" class="btn btn-primary" style="color: white;">Albarán</button>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td colspan="7"></td>
                                        <td><strong>Total Importe</strong></td>
                                        <td><strong>Total Iva</strong></td>
                                        <td><strong>Total Con Iva</strong></td>
                                        <!-- Ajusta el colspan según el número de columnas en tu tabla -->
                                        <td colspan="2"></td>
                                    </tr>
                                    <tr>
                                        <td colspan="7"></td>
                                        <td><strong>{{ $totalImportes }}€</strong></td>
                                        <td><strong>{{ $totalIva }}€</strong></td>
                                        <td><strong>{{ $totalesConIva }}€</strong></td>
                                        <!-- Ajusta el colspan según el número de columnas en tu tabla -->
                                        <td colspan="2"></td>
                                    </tr>
                                </tfoot>
                            </table>
                    @else
                        <h6 class="text-center">No tenemos ninguna factura</h6>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@section('scripts')
    <script>

        //ready
        $(document).ready(function() {
            
            $('#clientesSelect').on('change', function() {
                $('#datatable-buttons').DataTable().destroy();
            });

            $('#comercialesSelect').on('change', function() {
                $('#datatable-buttons').DataTable().destroy();
            });

            $('#delegacionesSelect').on('change', function() {
                $('#datatable-buttons').DataTable().destroy();
            });

            $('#clear').on('click', function() {
                console.log('clear')
                $('#datatable-buttons').DataTable().destroy();
                window.livewire.emit('limpiarFiltros');
            });

        });

     livewire.on('actualizarTablaAntes', ()=>{
        
        $('#datatable-buttons').DataTable().destroy();
        console.log('destruido')

     })

     livewire.on('actualizarTablaDespues', () =>{
        
            $('#datatable-buttons').DataTable();
            console.log('creado')
        
        
        //$('#datatable-buttons').DataTable().destroy();
       
     })
    
  
    function descargarFactura(id, conIva) {
        // Suponiendo que tu descarga se realiza aquí
        window.livewire.emit('pdf', id, conIva);
        setTimeout(() => {
            location.reload()
        }, 5000);
    }
    function mostrarAlbaran(id, conIva) {
        // Suponiendo que tu descarga se realiza aquí
        window.livewire.emit('albaran', id, conIva);
        setTimeout(() => {
            location.reload()
        }, 5000);
    }
    </script>
<script src="../assets/js/jquery.slimscroll.js"></script>
<link href="https://cdn.datatables.net/v/bs5/jq-3.7.0/jszip-3.10.1/dt-2.0.3/b-3.0.1/b-colvis-3.0.1/b-html5-3.0.1/b-print-3.0.1/r-3.0.1/datatables.min.css" rel="stylesheet">
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>
<script src="https://cdn.datatables.net/v/bs5/jq-3.7.0/jszip-3.10.1/dt-2.0.3/b-3.0.1/b-colvis-3.0.1/b-html5-3.0.1/b-print-3.0.1/r-3.0.1/datatables.min.js"></script>
<!-- Responsive examples -->
<script src="../assets/pages/datatables.init.js"></script>

@endsection
