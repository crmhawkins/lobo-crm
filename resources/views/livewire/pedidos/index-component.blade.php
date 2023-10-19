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
                    <table id="datatable-buttons" class="table table-striped table-bordered dt-responsive nowrap" style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                        <thead>
                            <tr>
                                <th scope="col">Número</th>
                                <th scope="col">Fecha emisión</th>
                                <th scope="col">Cliente</th>
                                <th scope="col">Precio</th>
                                <th scope="col">Estado</th>
                                <th scope="col">Acciones</th>
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
                                <td>@if($this->getEstadoNombre($presup->estado) == "Aceptado")
                                    <span class="badge badge-primary">Aceptado</span>
                                    @elseif($this->getEstadoNombre($presup->estado) == "Pendiente de revisión")
                                    <span class="badge badge-warning">Pendiente</span>
                                    @elseif($this->getEstadoNombre($presup->estado) == "Completado")
                                    <span class="badge badge-info">Completado</span>
                                    @elseif($this->getEstadoNombre($presup->estado) == "En almacén (Pendiente)")
                                    <span class="badge badge-warning">En almacén (Pendiente)</span>
                                    @elseif($this->getEstadoNombre($presup->estado) == "En almacén (En preparación)")
                                    <span class="badge badge-primary">En almacén (En preparación)</span>
                                    @elseif($this->getEstadoNombre($presup->estado) == "Enviado")
                                    <span class="badge badge-info">Enviado</span>
                                    @elseif($this->getEstadoNombre($presup->estado) == "Completado")
                                    <span class="badge badge-info">Enviado</span>
                                    @elseif($this->getEstadoNombre($presup->estado) == "Cancelado")
                                    <span class="badge badge-danger">Cancelado</span>
                                    @elseif($this->getEstadoNombre($presup->estado) == "Facturado")
                                    <span class="badge badge-success">Facturado</span>
                                    @elseif($this->getEstadoNombre($presup->estado) == "Pagado")
                                    <span class="badge badge-success">Pagado</span>
                                    @endif
                                </td>
                                <td> <a href="pedidos-edit/{{ $presup->id }}" class="btn btn-primary">Ver/Editar</a> </td>
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
