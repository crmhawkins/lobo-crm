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
                                <a class="dropdown-item" href="#" data-column="1">Pedido asociado</a>
                                <a class="dropdown-item" href="#" data-column="2">Descripción</a>
                                <a class="dropdown-item" href="#" data-column="3">Total</a>
                                <a class="dropdown-item" href="#" data-column="4">Método de pago<</a>
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
                                    <th scope="col">Pedido asociado</th>
                                    <th scope="col">Cliente</th>
                                    <th scope="col">Fecha de emisión</th>
                                    <th scope="col">Fecha de vencimiento</th>
                                    <th scope="col">Importe</th>
                                    <th scope="col">IVA</th>
                                    <th scope="col">Total(Con IVA)</th>
                                    <th scope="col">Método de pago</th>
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
                                        <td>{{ $this->getCliente($fact->cliente_id)}}</td>
                                        <td>{{ $fact->fecha_emision }}</td>
                                        <td>@if((new DateTime($fact->fecha_vencimiento)) <= (new DateTime()))
                                            <span class="badge badge-danger">{{ $fact->fecha_vencimiento }}</span>
                                            @else
                                            <span class="badge badge-success">{{ $fact->fecha_vencimiento }}</span>
                                            @endif
                                        </td>
                                        <td>{{ $fact->precio }}€
                                        </td>
                                        <td>{{number_format($fact->precio * 0.21, 2)}}€
                                        </td>
                                        <td>{{number_format($fact->precio * 1.21, 2)}}€
                                        </td>
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
                                            <span class="badge badge-info">{{ $fact->estado }}</span>
                                        @endswitch</td>
                                        <td> <a href="facturas-edit/{{ $fact->id }}" class="btn btn-primary">Ver/Editar</a>
                                            <a wire:click="pdf({{ $fact->id }},true)"  class="btn btn-primary" style="color: white;">Factura Con IVA</a>
                                            <a wire:click="pdf({{ $fact->id }},false)"  class="btn btn-primary" style="color: white;">Factura Sin IVA</a>
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
@endsection
