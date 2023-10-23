<div class="container-fluid">
    <script src="//unpkg.com/alpinejs" defer></script>
    <div class="page-title-box">
        <div class="row align-items-center">
            <div class="col-sm-6">
                <h4 class="page-title">PRODUCTOS</h4>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-right">
                    <li class="breadcrumb-item"><a href="javascript:void(0);">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="javascript:void(0);">Productos</a></li>
                    <li class="breadcrumb-item active">Ver productos</li>
                </ol>
            </div>
        </div> <!-- end row -->
    </div>
    <!-- end page-title -->
    <div class="row">
        <div class="col-12">
            <div class="card m-b-30">
                <div class="card-body row">
                    <div class="col-12">
                        <h4 class="mt-0 header-title font-24">Listado de stockaje</h4>
                        <p class="sub-title../plugins mb-5">Listado completo del stockaje, para ver o añadir un lote,
                            seleccione un producto de la lista.
                        </p>
                    </div>
                    @if (count($productos) > 0)
                        <div class="row justify-content-center">
                            <div class="col-md-12" wire:ignore>
                                <div x-data="" x-init="$('#select2-producto').select2();
                                $('#select2-producto').on('change', function(e) {
                                    var data = $('#select2-producto').select2('val');
                                    @this.set('producto_seleccionado', data);
                                    @this.emit('setLotes');
                                });">
                                    <label for="fechaVencimiento">Producto</label>
                                    <select class="form-control" name="producto" id="select2-producto"
                                        value="{{ $producto_seleccionado }}">
                                        @foreach ($productos as $presup)
                                            <option value="{{ $presup->id }}">{{ $presup->nombre }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            @if ($producto_seleccionado != 0 && $producto_seleccionado != null)
                                <div class="col-md-12 mt-4" x-data="{}" x-init="$nextTick(() => {
                                    $('#tabla-stock').DataTable({
                                        responsive: true,
                                        fixedHeader: {
                                            header: true,
                                            footer: true,
                                        },
                                        searching: false,
                                        paging: false,
                                        info: false,
                                    });
                                })" wire:key='{{rand()}}'>
                                    <table id="tabla-stock"
                                        class="table table-striped table-bordered dt-responsive nowrap"
                                        wire:key='{{ rand() }}'>
                                        <thead>
                                            <tr>
                                                <th>Lote</th>
                                                <th>Cantidad inicial</th>
                                                <th>Cantidad actual</th>
                                                <th>Fecha de entrada</th>
                                                <th>Acciones:</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($producto_lotes as $loteIndex => $lote)
                                                <tr>
                                                    <th>{{ $lote['lote_id'] }}</th>
                                                    <td>{{ $lote['cantidad_inicial'] }}</td>
                                                    <td>{{ $lote['cantidad_actual'] }}</td>
                                                    <td>{{ $this->formatFecha($lote['fecha_entrada']) }}</td>
                                                    <td><a href="stock-edit/{{ $lote['id'] }}"
                                                            class="btn @mobile btn-md @elsemobile btn-lg w-100 @endmobile btn-primary">Ver/Editar</a></td>
                                                </tr>
                                            @endforeach
                                        </table>
                                        <table class="table table-striped table-bordered dt-responsive nowrap mt-3">
                                            <tr>
                                                <td colspan="5"><a href="stock-create/{{ $producto_seleccionado }}"
                                                        class="btn btn-lg btn-primary w-100">AÑADIR NUEVO LOTE</a>
                                                </td>
                                            </tr>
                                        </table>

                                </div>
                            @endif
                        @else
                            <h6 class="text-center">No se encuentran productos disponibles</h6>
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
            $('#tablePresupuestos').DataTable({
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
