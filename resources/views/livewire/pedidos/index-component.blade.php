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

                    <table id="datatable-buttons" class="table table-striped table-bordered dt-responsive nowrap" style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                        <thead>
                            <tr>
                                <th scope="col">Nº</th>
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
                                <td>{{ $presup->id }}</td>
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
                                @if(Auth::user()->role != 3 )
                                <td><a href="pedidos-edit/{{ $presup->id }}" class="btn btn-primary">Ver/Editar</a></td>
                                @else
                                <td></td>
                                @endif
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
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
<script src="../assets/pages/datatables.init.js"></script>

@endsection
