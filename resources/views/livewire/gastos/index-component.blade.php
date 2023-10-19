<div class="container-fluid mx-auto">
    <div class="page-title-box">
        <div class="row align-items-center">
            <div class="col-sm-6">
                <h4 class="page-title">TODAS LOS GASTOS</span></h4>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-right">
                    <li class="breadcrumb-item"><a href="javascript:void(0);">Dashboard</a></li>
                    {{-- <li class="breadcrumb-item"><a href="javascript:void(0);">Contratos</a></li> --}}
                    <li class="breadcrumb-item active">Gastos</li>
                </ol>
            </div>
        </div> <!-- end row -->
    </div>
    <div class="row">
        <div class="col-12">
            <div class="card m-b-30">
                <div class="card-body">
                    <h4 class="mt-0 header-title">Listado de todas las gastos</h4>
                    <p class="sub-title../plugins">Listado completo de todas nuestros gastos, para editar o ver la
                        informacion completa pulse el boton de Editar en la columna acciones.
                    </p>
                    @if (count($gastos) > 0)
                    <table id="datatable-buttons" class="table table-striped table-bordered dt-responsive nowrap" style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                        <thead>
                            <tr>
                                <th scope="col">Concepto</th>
                                <th scope="col">Tipo de gasto</th>
                                <th scope="col">Cuantía</th>
                                <th scope="col">Repetición</th>
                                <th scope="col">Fecha/Fecha inicial</th>
                                <th scope="col">Activo</th>
                                <th scope="col">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($gastos as $fact)
                            <tr>
                                <td>{{ $fact->nombre_gasto }}</td>
                                <td>{{ $fact->tipo_gasto }}</td>
                                <td>{{ $fact->cuantia }}</td>
                                @if($fact->tipo_gasto != "Variable")
                                <td>{{ $fact->repeticion }}</td>
                                @else
                                <td>n/a</td>
                                @endif
                                <td>{{ $fact->date }}</td>
                                @if($fact->tipo_gasto != "Variable")
                                @if($fact->activo == 1)
                                <td><span class="badge badge-success">Activo</span></td>
                                @else
                                <td><span class="badge badge-danger">Inactivo</span></td>
                                @endif
                                @else
                                <td><span class="badge badge-warning">Variable</span></td>
                                @endif
                                <td> <a href="gastos-edit/{{ $fact->id }}" class="btn btn-primary">Ver/Editar</a> </td>
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
                    $('#tableGastos').DataTable({
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