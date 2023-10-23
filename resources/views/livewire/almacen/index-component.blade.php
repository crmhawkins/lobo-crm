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
                                        <td>{{ $pedido->tipo_pedido_id }}</td>
                                        <td> <a href="albaranes-create/{{ $pedido->id }}"
                                                class="btn btn-primary">Preparar pedido</a>
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
                                @foreach ($pedidos_preparacion as $pedido)
                                    <tr>
                                        <td>{{ $pedido->id }}</td>
                                        <td>{{ $this->getNombreCliente($pedido->cliente_id) }}</td>
                                        <td>{{ $pedido->fecha }}</td>
                                        <td>{{ $pedido->precio }}€</td>
                                        <td>{{ $pedido->tipo_pedido_id }}</td>
                                        <td> <a href="albaranes-edit/{{ $pedido->id }}"
                                                class="btn btn-primary">Comprobar pedido</a>
                                            <a href="albaranes-pdf/{{ $pedido->id }}" class="btn btn-primary">Ver
                                                albarán</a>
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
                    <h4 class="mt-0 header-title">Pedidos enviados</h4>
                    @if (count($pedidos_enviados) > 0)
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
                                @foreach ($pedidos_enviados as $pedido)
                                    <tr>
                                        <td>{{ $pedido->id }}</td>
                                        <td>{{ $this->getNombreCliente($pedido->cliente_id) }}</td>
                                        <td>{{ $pedido->fecha }}</td>
                                        <td>{{ $pedido->precio }}€</td>
                                        <td>{{ $pedido->tipo_pedido_id }}</td>
                                        <td> <a href="albaranes-edit/{{ $pedido->id }}"
                                                class="btn btn-primary">Comprobar pedido</a>
                                            <a href="albaranes-pdf/{{ $pedido->id }}" class="btn btn-primary">Ver
                                                albarán</a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @else
                        <h2 class="text-center" style="color: #35a8e0 !important">No hay pedidos en proceso de envío
                        </h2>
                    @endif

                </div>
            </div>
        </div>
    </div>
    @section('scripts')
        <script src="https://cdn.datatables.net/responsive/2.4.0/js/dataTables.responsive.min.js"></script>
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
        </script>
    @endsection
</div>
