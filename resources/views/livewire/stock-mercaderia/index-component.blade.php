<div class="container-fluid">
    <div class="page-title-box">
        <div class="row align-items-center">
            <div class="col-sm-6">
                <h4 class="page-title">PRODUCTOS</h4>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-right">
                    <li class="breadcrumb-item"><a href="javascript:void(0);">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="javascript:void(0);">Materiales</a></li>
                    <li class="breadcrumb-item"><a href="javascript:void(0);">Stock de materiales</a></li>
                    <li class="breadcrumb-item active">Ver stock de materiales</li>
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
                        <h4 class="mt-0 header-title font-24">Listado de stockaje de materiales</h4>

                        <video id="preview" style="width: 100%; display: none;"></video>
                        <button id="btnCerrarEscaneo" onclick="cerrarEscaneo()" class="btn btn-lg btn-danger w-100" style="display: none;">CERRAR ESCÁNER</button>
                        <button type="button" onclick="iniciarEscaneo()" class="btn btn-lg btn-primary w-100 mt-2">AÑADIR STOCK</button>


                        <button type="button" wire:click.prevent="alertaGuardar"
                                        class="btn btn-lg btn-primary w-100 mt-2">GENERAR CÓDIGOS QR</button>
                    </div>
                    @if (count($mercaderia) > 0)
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
                        })"
                            wire:key='{{ rand() }}'>
                            <table id="tabla-stock" class="table table-striped table-bordered dt-responsive nowrap"
                                wire:key='{{ rand() }}'>
                                <thead>
                                    <tr>
                                        <th>Producto</th>
                                        <td>Cantidad</td>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($mercaderia as $mercaderiaIndex => $mercaderia)
                                        <tr>
                                            <th>{{ $mercaderia->nombre }}</th>
                                            <td>{{ $this->getCantidad($mercaderia->id) }}</td>
                                        </tr>
                                    @endforeach
                            </table>
                        </div>
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
    <script src="https://cdnjs.cloudflare.com/ajax/libs/instascan/1.0.0/instascan.min.js"></script>
    <script>

        let scanner = null;

        function iniciarEscaneo() {
            if (!scanner) {
                scanner = new Instascan.Scanner({ video: document.getElementById('preview') });
                scanner.addListener('scan', function (url) {
                    window.location.href = url; // Redirecciona a la URL del QR
                });

                Instascan.Camera.getCameras().then(function (cameras) {
                    if (cameras.length > 0) {
                        scanner.start(cameras[0]);
                    } else {
                        console.error('No cameras found.');
                        alert('No se encontraron cámaras.');
                    }
                }).catch(function (e) {
                    console.error(e);
                    alert('Error al acceder a la cámara: ' + e);
                });
            } else {
                scanner.start();
            }
            document.getElementById('preview').style.display = 'block'; // Mostrar el video
            document.getElementById('btnCerrarEscaneo').style.display = 'block'; // Mostrar el botón de cerrar
        }

        function cerrarEscaneo() {
            if (scanner) {
                scanner.stop();
            }
            document.getElementById('preview').style.display = 'none'; // Ocultar el video
            document.getElementById('btnCerrarEscaneo').style.display = 'none'; // Ocultar el botón de cerrar
        }
    </script>
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
