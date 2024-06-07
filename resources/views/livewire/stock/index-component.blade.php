
@php
$EsAdmin = Auth::user()->isAdmin();
$canEdit = $EsAdmin; //|| $estado == 1;
@endphp
<div class="container-fluid">
    <style>
        @media(max-width: 768px) {
            
            .botones {
                margin: 10px;
                width: 100%;
                display: block;
            }
        }
    </style>
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
                        <button type="button" onclick="iniciarEscaneo('editar')" class="btn btn-lg btn-primary w-100 mt-2">MODIFICAR STOCK</button>
                        <button type="button" wire:click.prevent="alertaGuardar" class="btn btn-lg btn-primary w-100 mt-2">GENERAR CÓDIGOS QR</button>
                        {{-- <button type="button" wire:click.prevent="imprimirEntrante" class="btn btn-lg btn-secondary w-100 mt-2">DESCARGAR HISTORIAL ENTRANTE</button> --}}
                        {{-- <button type="button" wire:click.prevent="imprimirSaliente" class="btn btn-lg btn-secondary w-100 mt-2">DESCARGAR HISTORIAL SALIENTE</button> --}}
                        <a href="{{ route('stock.historial') }}" class="btn btn-lg btn-secondary w-100 mt-2">VER HISTORIAL</a>

                    </div>
                    @if (auth()->user()->role == 1 || auth()->user()->role == 7)
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
                        <div x-data="" x-init="
                        $('#select2-producto').select2();
                        $('#select2-producto').on('change', function(e) {
                            var data = $('#select2-producto').select2('val');
                            @this.set('producto_seleccionado', data);
                            @this.emit('setLotes');
                        });
                        
                        // Establecer el valor seleccionado de Select2 para que coincida con Livewire al iniciar
                        $nextTick(() => {
                            $('#select2-producto').val(@this.producto_seleccionado).trigger('change');
                        });
                        ">
                        <label for="fechaVencimiento">Producto</label>
                        <select class="form-control" name="producto" id="select2-producto">
                            <option value="0">Mostrar todo</option>
                            @foreach ($productos as $presup)
                            <option value="{{ $presup->id }}">{{ $presup->nombre }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                
                <div class="col-md-12 mt-4" x-data="{}" x-init="$nextTick(() => {
                    $('#tabla-stock').DataTable({
                        responsive: true,
                        fixedHeader: {
                            header: true,
                            footer: true,
                        },
                        searching: false,
                        paging: true,
                        info: false,
                        dom: 'Bfrtip', // Este elemento define dónde se colocan los botones
                        buttons: [
                                            {
                                                extend: 'excelHtml5',
                                                text: 'Exportar a Excel',
                                                titleAttr: 'Excel',
                                                className: 'btn-secondary px-3 py-1 mb-2'
                                            },
                                            {
                                                extend: 'pdfHtml5',
                                                text: 'Exportar a PDF',
                                                titleAttr: 'PDF',
                                                className: 'btn-secondary px-3 py-1 mb-2'
                                            }
                                            ]
                                        });
                                    })"
                                    wire:key='{{ rand() }}'>
                                    <table id="tabla-stock"  class="table table-striped table-bordered dt-responsive nowrap"  wire:key='{{ rand() }}'>
                                        <thead>
                                            <tr>
                                                <th>Nº Interno</th>
                                                <th>N.º Lote</th>
                                                <th>Almacen</th>
                                                <th>Producto</th>
                                                <th>Fecha de entrada</th>
                                                <th>Cantidad (en Botellas)</th>
                                                <th>Cantidad (en Cajas)</th>
                                                <th>Accion</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($producto_lotes as $loteIndex => $lote)
                                            <tr>
                                                <th>{{ $lote['lote_id'] }}</th>
                                                <th>{{ $lote['orden_numero'] }}</th>
                                                <th>{{ $this->almacen($lote) }}</th>
                                                <th>{{ $this->getProducto($lote['producto_id']) }}</th>
                                                <td>{{ $this->formatFecha($lote['stock_id']) }}</td>

                                                    <td>{{ $lote['cantidad'] }}</td>
                                                    
                                                    <td>{{ floor($lote['cantidad']/ $this->getUnidadeCaja($lote['producto_id']) )}}</td>

                                                    <td class="botonesStock">
                                                    @if(Auth::user()->role != 2)
                                                        @if($this->qrAsignado($lote))
                                                            <button class="btn btn-primary botones" onclick="generarQRIndividual({{$lote}})"> QR</button>
                                                        @else
                                                            <button class="btn btn-primary botones" onclick="iniciarEscaneo('asignar',{{$lote}})">Asignar Qr</button>
                                                        @endif
                                                       
                                                                <a class="btn btn-primary botones" href="/admin/stock-traspaso/{{$lote['id']}}"> Traspaso de lote</a>
                                                    @endif      
                                                            @if($EsAdmin)
                                                                <a class="btn btn-primary botones" href="/admin/stock-edit/{{$lote['id']}}"> Editar lote</a>
                                                            @else
                                                                <a class="btn btn-warning botones" href="/admin/stock-edit/{{$lote['id']}}">Editar</a>
                                                            @endif
                                                    @if(Auth::user()->role != 2)
                                                        @if($this->qrAsignado($lote))
                                                            
                                                                <button class="btn btn-danger botones" onclick="borrar({{$lote}})">Eliminar QR</button>
                                                            
                                                        @endif
                                                    @endif
                                                    </td>
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
    <script>
       window.addEventListener('downloadPdfBase64', event => {
        const pdfBase64 = event.detail.pdfBase64;
        const nombreArchivo = event.detail.nombreArchivo; // Usa el nombre de archivo enviado desde el servidor
        const link = document.createElement('a');
        link.href = `data:application/pdf;base64,${pdfBase64}`;
        link.download = nombreArchivo; // Utiliza el nombre de archivo dinámico
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
        });
        </script>
        <script>
            function generarQRIndividual(id) {
                // Suponiendo que tu descarga se realiza aquí
                window.livewire.emit('generarQRIndividual', id);
                setTimeout(() => {
                    location.reload()
                }, 2000);
            }
        </script>
        <script>
            function borrar(id) {
                // Suponiendo que tu descarga se realiza aquí
                window.livewire.emit('borrar', id);
                // setTimeout(() => {
                //     location.reload()
                // }, 2000);
            }
        </script>
    <script src="https://cdn.jsdelivr.net/npm/jsqr"></script>
    <script>

        let video = document.createElement("video");
        let canvasElement = document.getElementById("qr-canvas");
        let botoncerrar = document.getElementById("btnCerrarEscaneo");
        let canvas = canvasElement.getContext("2d", { willReadFrequently: true });
        let scanning = false;
        let currentAction = '';
        let lotestock = '';
        function iniciarEscaneo(action, lote = null) {
            currentAction = action; // Guardamos la acción actual
            if (lote !== null) {
                lotestock = lote; // Guarda la ID del lote si se proporcionó
                }
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
                    botoncerrar.style.display = "none";
                }
            }
            if (scanning) {
                requestAnimationFrame(tick);
            }
        }

        function handleQRCodeAction(data) {
            // Aquí manejas las diferentes acciones basadas en el código QR y la acción actual
            if (currentAction === 'añadir') {
                Livewire.emit('anadir', data);
            } else if (currentAction === 'asignar') {
                // window.location.href = '/admin/stock-edit/' + data;
                Livewire.emit('asignarQr', { qrData: data, lote: lotestock });
            }else if (currentAction === 'editar') {
                Livewire.emit('editar', data);
            }
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

<link href="https://cdn.datatables.net/v/bs4/jszip-3.10.1/dt-2.0.3/b-3.0.1/b-colvis-3.0.1/b-html5-3.0.1/r-3.0.1/datatables.min.css" rel="stylesheet">

<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>
<script src="https://cdn.datatables.net/v/bs4/jszip-3.10.1/dt-2.0.3/b-3.0.1/b-colvis-3.0.1/b-html5-3.0.1/r-3.0.1/datatables.min.js"></script>



@endsection
