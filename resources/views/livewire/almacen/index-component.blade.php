<div class="container-fluid mx-auto">
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

                        <table id="datatable-buttons" class="table table-striped table-bordered dt-responsive nowrap"
                            style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                            <thead>
                                <tr>
                                    <th scope="col">Pedido</th>
                                    <th scope="col">Cliente</th>
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
                                        <td>{{ $this->getNombreCliente($pedido->cliente_id) }}</td>
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
                                            <a wire:click="prepararPedido({{ $pedido->id }})"  class="btn btn-primary"  style="color: white;">Preparar pedido</a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
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

                        <table id="datatable-buttons_preparacion" class="table table-striped table-bordered dt-responsive nowrap"
                            style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                            <thead>
                                <tr>
                                    <th scope="col">Pedido</th>
                                    <th scope="col">Cliente</th>
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
                                        <td>{{ $this->getNombreCliente($pedido->cliente_id) }}</td>
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
                                        <td> <a href="#" wire:click.prevent="comprobarStockPedido({{ $pedido->id }})" class="btn btn-primary">Comprobar pedido</a>
                                            <a href="almacen-create/{{ $pedido->id }}" class="btn btn-primary">Generar albarán</a>

                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
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

                        <table id="datatable-buttons_enviados" class="table table-striped table-bordered dt-responsive nowrap"
                            style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                            <thead>
                                <tr>
                                    <th scope="col">Pedido</th>
                                    <th scope="col">Cliente</th>
                                    <th scope="col">Fecha</th>
                                    <th scope="col">Precio</th>
                                    <th scope="col">Tipo de pedido</th>
                                    <th scope="col">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($pedidos_enviados as $pedido)
                                    <tr>
                                        <td>{{ $pedido->id }}</td>
                                        <td>{{ $this->getNombreCliente($pedido->cliente_id) }}</td>
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
                                            <a wire:click.prevent="mostrarAlbaran({{ $pedido->id }},true)" class="btn btn-primary"  style="color: white;">Descargar albarán Con IVA</a>
                                            <a wire:click.prevent="mostrarAlbaran({{ $pedido->id }},false)" class="btn btn-primary"  style="color: white;">Descargar albarán Sin IVA</a>
                                                @if ($pedido->estado ==8)
                                                <a href="facturas-create/{{ $pedido->id }}" class="btn btn-primary">Crear Factura</a>
                                                @else
                                                <a  wire:click.prevent="enRuta({{ $pedido->id }})" class="btn btn-primary" style="color: white;">Pedido En Ruta</a>
                                                @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @else
                        <h2 class="text-center" style="color: #35a8e0 !important">No hay pedidos en proceso por enviar
                        </h2>
                    @endif

                </div>
            </div>
        </div>
    </div>
   @section('scripts')
        {{--<script src="https://cdn.datatables.net/responsive/2.4.0/js/dataTables.responsive.min.js"></script>
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
                $('#tableFacturas').DataTable({
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
        </script>--}}
document.addEventListener('DOMContentLoaded', function() {
<script src="../assets/js/jquery.slimscroll.js"></script>
<script src="../plugins/datatables/jquery.dataTables.min.js"></script>
<script src="../plugins/datatables/dataTables.bootstrap4.min.js"></script>
<!-- Buttons examples -->
<script src="../plugins/datatables/dataTables.buttons.min.js"></script>
<script src="../plugins/datatables/buttons.bootstrap4.min.js"></script>
<script src="../plugins/datatables/jszip.min.js"></script>
<script src="../plugins/datatables/pdfmake.min.js"></script>
<script src="../plugins/datatables/vfs_fonts.js"></script>
<script src="../plugins/datatables/buttons.html5.min.js"></script>
<script src="../plugins/datatables/buttons.print.min.js"></script>
<script src="../plugins/datatables/buttons.colVis.min.js"></script>
<!-- Responsive examples -->
<script src="../plugins/datatables/dataTables.responsive.min.js"></script>
<script src="../plugins/datatables/responsive.bootstrap4.min.js"></script>
<script src="../assets/pages/datatables.init.js"></script>
});
    @endsection

</div>
