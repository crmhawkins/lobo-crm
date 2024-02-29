<div class="container-fluid">
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

                        <div style="display: flex; justify-content: center;">
                            <canvas id="qr-canvas" style="width: 50%; display: none;"></canvas>
                        </div>
                        <button id="btnCerrarEscaneo" onclick="cerrarEscaneo()" class="btn btn-lg btn-danger w-100 mt-2" style="display: none;">CERRAR ESCÁNER</button>
                        <button type="button" onclick="iniciarEscaneo('añadir')" class="btn btn-lg btn-primary w-100 mt-2">AÑADIR STOCK</button>
                        <button type="button" wire:click.prevent="alertaGuardar" class="btn btn-lg btn-primary w-100 mt-2">GENERAR CÓDIGOS QR</button>
                    </div>
                    @if (auth()->user()->role == 1)
                    <div class="row justify-content-center">
                        <div class="form-group col-md-12 mt-1">
                            <label for="fechaVencimiento">Almacén</label>
                                <select name="almacen" id="select2-almacen" wire:model="almacen_id"
                                    wire:change='setLotes' style="width: 100% !important">
                                    <option value="{{ null }}">-- Selecciona un almacén --
                                    </option>
                                    @foreach ($almacenes as $presup)
                                        <option value="{{ $presup->id }}">{{ $presup->almacen }}
                                        </option>
                                    @endforeach
                                </select>
                        </div>
                    </div>
                    @endif
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
                                })"
                                    wire:key='{{ rand() }}'>
                                    <table id="tabla-stock"
                                        class="table table-striped table-bordered dt-responsive nowrap"
                                        wire:key='{{ rand() }}'>
                                        <thead>
                                            <tr>
                                                <th>Lote/Pallet</th>
                                                <th>Nº Orden</th>
                                                <th>Fecha de entrada</th>
                                                <th>Cantidad (en cajas)</th>
                                                {{--<th>Accion</th>--}}
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($producto_lotes as $loteIndex => $lote)
                                                <tr>
                                                    <th>{{ $lote['lote_id'] }}</th>
                                                    <th>{{ $lote['orden_numero'] }}</th>
                                                    <td>{{ $this->formatFecha($lote['stock_id']) }}</td>
                                                    <td>{{ $lote['cantidad'] }}</td>
                                                    {{--<td>
                                                        <button class="btn btn-primary" wire:click.prevent="generarPDFConQRCode({{ $lote->id }})">Generar QR</button>
                                                    </td>--}}
                                                </tr>
                                            @endforeach
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
    <script src="https://cdn.jsdelivr.net/npm/jsqr"></script>
    <script>

        let video = document.createElement("video");
        let canvasElement = document.getElementById("qr-canvas");
        let botoncerrar = document.getElementById("btnCerrarEscaneo");
        let canvas = canvasElement.getContext("2d", { willReadFrequently: true });
        let scanning = false;
        let currentAction = '';

        function iniciarEscaneo(action) {
            currentAction = action; // Guardamos la acción actual
            navigator.mediaDevices.getUserMedia({ video: { facingMode: "environment" } }).then(function(stream) {
                scanning = true;
                video.srcObject = stream;
                video.setAttribute("playsinline", true); // necesario para iOS Safari
                video.play();
                requestAnimationFrame(tick);
            });
            botoncerrar.style.display = "block";
            canvasElement.style.display = "block";
        }

        function tick() {
            if (video.readyState === video.HAVE_ENOUGH_DATA && scanning) {
                canvasElement.height = video.videoHeight;
                canvasElement.width = video.videoWidth;
                canvas.drawImage(video, 0, 0, canvasElement.width, canvasElement.height);
                var imageData = canvas.getImageData(0, 0, canvasElement.width, canvasElement.height);
                var code = jsQR(imageData.data, imageData.width, imageData.height, {
                    inversionAttempts: "dontInvert",
                });

                if (code) {
                    console.log("Código QR encontrado", code.data);
                    handleQRCodeAction(code.data);
                    scanning = false;
                    video.srcObject.getTracks().forEach(track => track.stop());
                    canvasElement.style.display = "none";
                }
            }
            if (scanning) {
                requestAnimationFrame(tick);
            }
        }

        function handleQRCodeAction(data) {
            // Aquí manejas las diferentes acciones basadas en el código QR y la acción actual
            if (currentAction === 'añadir') {
                window.location.href = '/admin/stock-create/' + data;
            } else if (currentAction === 'salida') {
                window.location.href = '/admin/stock-edit/' + data;
            }
            // Agrega más condiciones según sea necesario
        }

        function cerrarEscaneo() {
            scanning = false;
            if (video.srcObject) {
                video.srcObject.getTracks().forEach(track => track.stop());
            }
            canvasElement.style.display = "none";
            botoncerrar.style.display = "none";
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
