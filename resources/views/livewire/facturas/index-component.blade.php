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
                <div class="card-body">
                    <h4 class="mt-0 header-title">Listado de todas las facturas</h4>
                    <p class="sub-title../plugins">Listado completo de todas nuestros facturas, para editar o ver la
                        informacion completa pulse el boton de Editar en la columna acciones.
                    </p>
                    <div class="col-12 mb-5">
                        <a href="facturas-create" class="btn btn-lg w-100 btn-primary">Crear factura</a>
                    </div>
                    @if (count($facturas) > 0)

                        <!-- Aquí comienza el botón desplegable para filtrar por columna -->
                    <div id="Botonesfiltros">
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
                                @foreach ($facturas as $fact)
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
                                        <td>{{ $this->getCliente($fact->cliente_id)}}</td>
                                        <td>{{ $fact->fecha_emision }}</td>
                                        <td>@if((new DateTime($fact->fecha_vencimiento)) <= (new DateTime()))
                                            <span class="badge badge-danger">{{ $fact->fecha_vencimiento }}</span>
                                            @else
                                            <span class="badge badge-success">{{ $fact->fecha_vencimiento }}</span>
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
                                        <td>{{ $fact->metodo_pago }}</td>
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
                                            <span class="badge badge-infos">{{ $fact->estado }}</span>
                                        @endswitch</td>
                                        <td> <a href="facturas-edit/{{ $fact->id }}" class="btn btn-primary">Ver/Editar</a>
                                            <button  onclick="descargarFactura({{ $fact->id }}, true)" class="btn btn-primary" style="color: white;">Factura Con IVA</button>
                                            <button  onclick="descargarFactura({{ $fact->id }}, false)" class="btn btn-primary" style="color: white;">Factura Sin IVA</button>
                                            <button  onclick="mostrarAlbaran({{ $fact->id }}, true)" class="btn btn-primary" style="color: white;">Albarán</button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
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
