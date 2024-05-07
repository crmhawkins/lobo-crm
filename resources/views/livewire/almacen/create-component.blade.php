<div class="container-fluid">
    <script src="//unpkg.com/alpinejs" defer></script>
    <div class="page-title-box">
        <div class="row align-items-center">
            <div class="col-sm-6">
                <h4 class="page-title">PREPARAR PEDIDO PARA ENVÍO</span></h4>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-right">
                    <li class="breadcrumb-item"><a href="javascript:void(0);">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="javascript:void(0);">Albaranes</a></li>
                    <li class="breadcrumb-item active">Preparar pedido para envío</li>
                </ol>
            </div>
        </div> <!-- end row -->
    </div>
    
    <!-- end page-title -->
    <div class="row" style="align-items: start !important">
        <div class="col-md-9">
            <div class="card m-b-30">
                <div class="card-body">
                    <form wire:submit.prevent="submit">
                        <input type="hidden" name="csrf-token" value="{{ csrf_token() }}">

                        <div class="form-group row justify-content-center">
                            <div class="col-md-12">
                                <h5 class="ms-3"
                                    style="border-bottom: 1px gray solid !important; padding-bottom: 10px !important;">
                                    Datos
                                    de albarán</h5>
                            </div>
                            <div class="col-md-4">
                                <label for="num_albaran" class="col-sm-12 col-form-label">Número de albarán</label>
                                <div class="col-sm-12">
                                    <input type="text" wire:model="num_albaran" class="form-control"
                                        name="num_albaran" id="num_albaran" readonly>
                                    @error('num_albaran')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <label for="pedido_id" class="col-sm-12 col-form-label">Nº del pedido</label>
                                <input type="text" wire:model="pedido_id" class="form-control" disabled>
                            </div>
                            <div class="col-md-4">
                                <label for="fecha" class="col-sm-12 col-form-label">Fecha</label>
                                <div class="col-sm-12">
                                    <input type="date" wire:model="fecha" class="form-control"
                                        placeholder="15/02/2023" readonly>
                                    @error('fecha')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        <div class="form-group row justify-content-center mt-5">
                            <div class="col-md-12">
                                <h5 class="ms-3"
                                    style="border-bottom: 1px gray solid !important; padding-bottom: 10px !important;">
                                    Datos
                                    de cliente</h5>
                            </div>
                            <div class="col-md-4">
                                <label class="col-sm-12 col-form-label">Nombre</label>
                                <div class="col-sm-12">
                                    <input type="text" class="form-control" value="{{ $cliente->nombre }}" disabled>
                                    @error('num_albaran')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <label class="col-sm-12 col-form-label">DNI/CIF</label>
                                <input type="text" class="form-control" value="{{ $cliente->dni_cif }}" disabled>
                            </div>
                            <div class="col-md-4">
                                <label for="estado" class="col-sm-12 col-form-label">Dirección</label>
                                <div class="col-sm-12">
                                    <input type="text" class="form-control" value="{{ $cliente->direccion }}"
                                        disabled>
                                    @error('estado')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <label for="estado" class="col-sm-12 col-form-label">Localidad</label>
                                <div class="col-sm-12">
                                    <input type="text" class="form-control" value="{{ $cliente->localidad }}"
                                        disabled>
                                    @error('estado')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <label for="estado" class="col-sm-12 col-form-label">Provincia</label>
                                <div class="col-sm-12">
                                    <input type="text" class="form-control" value="{{ $cliente->provincia }}"
                                        disabled>
                                    @error('estado')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <label for="estado" class="col-sm-12 col-form-label">Codigo Postal</label>
                                <div class="col-sm-12">
                                    <input type="text" class="form-control" value="{{ $cliente->cod_postal }}"
                                        disabled>
                                    @error('estado')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label for="estado" class="col-sm-12 col-form-label">Teléfono</label>
                                <div class="col-sm-12">
                                    <input type="text" class="form-control" value="{{ $cliente->telefono }}"
                                        disabled>
                                    @error('estado')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label for="estado" class="col-sm-12 col-form-label">Correo</label>
                                <div class="col-sm-12">
                                    <input type="text" class="form-control" value="{{ $cliente->email }}"
                                        disabled>
                                    @error('estado')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        <div class="form-group row justify-content-center">
                            <div class="col-md-12">
                                <h5 class="ms-3"
                                    style="border-bottom: 1px gray solid !important; padding-bottom: 10px !important;">
                                    Datos
                                    de envío</h5>
                            </div>
                            <div class="form-group col-md-5">
                                <label for="fecha">Dirección</label>
                                <input type="text" value="{{ $pedido->direccion_entrega }}" class="form-control" disabled>
                            </div>
                            <div class="form-group col-md-1">
                                &nbsp;
                            </div>
                            <div class="form-group col-md-5">
                                <label for="fecha">Localidad</label>
                                <input type="text" value="{{ $pedido->localidad_entrega }}" class="form-control" disabled>
                            </div>
                        </div>
                        <div class="form-row justify-content-center">
                            <div class="form-group col-md-5">
                                <label for="fecha">Provincia</label>
                                <input type="text" value="{{ $pedido->provincia_entrega }}" class="form-control" disabled>
                            </div>
                            <div class="form-group col-md-1">
                                &nbsp;
                            </div>
                            <div class="form-group col-md-5">
                                <label for="fecha">Código postal</label>
                                <input type="text" value="{{ $pedido->cod_postal_entrega }}"
                                    class="form-control" disabled>
                            </div>
                        </div>
                        <div class="form-row justify-content-center">
                            <div class="form-group col-md-5">
                                <label for="observaciones" >Observaciones</label>
                                <textarea wire:model="observaciones" class="form-control" name="observaciones" id="observaciones"></textarea>
                            </div>
                            <div class="form-group col-md-1">
                                &nbsp;
                            </div>
                            <div class="form-group col-md-5">
                                <label for="observaciones" >Observaciones Descarga</label>
                                <textarea wire:model="observacionesDescarga" class="form-control" name="observacionesDescarga" id="observacionesDescarga" disabled></textarea>
                            </div>
                        </div>
                        
                </div>
                <div class="form-row justify-content-center">
                    <div class="form-group col-md-12">
                        <h5 class="ms-3"
                            style="border-bottom: 1px gray solid !important;padding-bottom: 10px !important;display: flex !important;flex-direction: row;justify-content: space-between;">
                            Lista de productos</h5>
                        <div class="form-group col-md-12">
                            @if (count($productos_pedido) > 0)
                            <div id="qr-scanner-container" style="display: none;">
                                <div style="display: flex; justify-content: center;">
                                    <canvas id="qr-canvas" style="width: 50%;"></canvas>
                                </div>
                                <button onclick="cerrarEscaneo()" type="button" class="btn btn-lg btn-danger w-100 mt-2">Cerrar Escáner</button>
                            </div>
                                <table class="table ms-3 table-striped table-bordered dt-responsive nowrap">
                                    <thead>
                                        <tr>
                                            <th>Producto</th>
                                            <th>Lote</th>
                                            <th>Peso Total</th>
                                            <th>Cantidad</th>
                                            <th>Precio unidad</th>
                                            <th>Precio total</th>

                                        </tr>
                                    </thead>
                                    <tbody>

                                        @foreach ($productos_pedido as $productoIndex => $producto)
                                            <tr>

                                                <td>{{ $this->getNombreTabla($producto['producto_pedido_id']) }}
                                                   
                                                </td>
                                                @if (is_null($producto['lote_id']))
                                                <td>
                                                <button type="button" onclick="iniciarEscaneo({{ $productoIndex }})" class="btn btn-lg btn-primary">Escanera lote</button>
                                                </td>
                                                @else
                                                <td>{{ $producto['lote_id']}}
                                                @endif

                                                <td>{{$this->getPesoTotal($producto['producto_pedido_id'],$productoIndex)}} KG </td>

                                                <td>{{ $this->getUnidadesTabla($productoIndex)}}</td>

                                                <td>{{ $producto['precio_ud']}} €</td>
                                                <td>{{ $producto['precio_total']}} €</td>

                                                </td>
                                            </tr>
                                        @endforeach
                                        @if ($descuento)
                                        <tr>
                                            <th colspan="1">Descuento aplicado</th>
                                            <th colspan="4">Importe</th>
                                            <th>{{ $pedido->precio }} €</td>
                                        </tr>
                                        @else
                                        <tr>
                                            <th colspan="5">Importe</th>
                                            <th>{{ $pedido->precio }} €</td>
                                        </tr>
                                        @endif
                                    </tbody>
                                </table>
                            @endif
                        </div>
                    </div>
                    </form>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card m-b-30">
                <div class="card-body">
                    <h5>Acciones</h5>
                    <div class="row">
                        <div class="col-12">
                            <button class="w-100 btn btn-info mb-2" wire:click="GenerarAlbaran(true)">Generar Albarán de Envio</button>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>

@section('scripts')
    <script>
        $("#alertaGuardar").on("click", () => {
            Swal.fire({
                title: 'Selecciona esta opción en caso de enviar el pedido a preparación.',
                icon: 'info',
                showConfirmButton: true,
                showCancelButton: true
            }).then((result) => {
                if (result.isConfirmed) {
                    window.livewire.emit('submit');
                }
            });
        });
        $("#alertaGuardar").on("click", () => {
            Swal.fire({
                title: 'Selecciona esta opción en caso de enviar el pedido a preparación.',
                icon: 'info',
                showConfirmButton: true,
                showCancelButton: true
            }).then((result) => {
                if (result.isConfirmed) {
                    window.livewire.emit('devolverPedido');
                }
            });
        });
        $.datepicker.regional['es'] = {
            closeText: 'Cerrar',
            prevText: '< Ant',
            nextText: 'Sig >',
            currentText: 'Hoy',
            monthNames: ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre',
                'Octubre', 'Noviembre', 'Diciembre'
            ],
            monthNamesShort: ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'],
            dayNames: ['Domingo', 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado'],
            dayNamesShort: ['Dom', 'Lun', 'Mar', 'Mié', 'Juv', 'Vie', 'Sáb'],
            dayNamesMin: ['Do', 'Lu', 'Ma', 'Mi', 'Ju', 'Vi', 'Sá'],
            weekHeader: 'Sm',
            dateFormat: 'dd/mm/yy',
            firstDay: 1,
            isRTL: false,
            showMonthAfterYear: false,
            yearSuffix: ''
        };
        $.datepicker.setDefaults($.datepicker.regional['es']);
        document.addEventListener('livewire:load', function() {


        })

        $(document).ready(function() {
            $('.js-example-responsive').select2({
                placeholder: "-- Seleccione un pedido --",
                width: 'resolve'
            }).on('change', function() {

            });


            console.log('select2')
            $("#datepicker").datepicker();

            $("#datepicker").on('change', function(e) {
                @this.set('fecha_emision', $('#datepicker').val());
            });
            $("#datepicker2").datepicker();

            $("#datepicker2").on('change', function(e) {
                @this.set('fecha_vencimiento', $('#datepicker2').val());
            });


        });
    </script>
    {{-- SCRIPT PARA SELECT 2 CON LIVEWIRE --}}
    <script>
        window.initSelect2 = () => {
            jQuery("#id_pedido").select2({
                minimumResultsForSearch: 2,
                allowClear: false
            });
        }

        initSelect2();
        window.livewire.on('select2', () => {
            initSelect2();
        });
    </script>
    <script src="https://cdn.jsdelivr.net/npm/jsqr"></script>
    <script>
        let video = document.createElement("video");
        let canvasElement = document.getElementById("qr-canvas");
        let canvas = canvasElement.getContext("2d", { willReadFrequently: true });
        let scanning = false;
        let selectedRow = null; // Variable para almacenar la fila seleccionada

        function iniciarEscaneo(rowIndex) {
            selectedRow = rowIndex; // Guarda el índice de la fila seleccionada
            navigator.mediaDevices.getUserMedia({ video: { facingMode: "environment" } }).then(function(stream) {
                scanning = true;
                video.srcObject = stream;
                video.play();
                requestAnimationFrame(tick);
                document.getElementById('qr-scanner-container').style.display = 'block';
            });
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
                    scanning = false;
                    video.srcObject.getTracks().forEach(track => track.stop());
                    document.getElementById('qr-scanner-container').style.display = 'none';
                    console.log("QRcódigo encontrado para la fila " + selectedRow + ": " + code.data);
                    procesarCodigoQR(code.data, selectedRow);
                    }
            }
            if (scanning) {requestAnimationFrame(tick);}
        }

        function cerrarEscaneo() {
            scanning = false;
            if (video.srcObject) {
                video.srcObject.getTracks().forEach(track => track.stop());
            }
            document.getElementById('qr-scanner-container').style.display = 'none';
        }

        function procesarCodigoQR(data, rowIndex) {
            // Aquí puedes agregar la lógica para manejar el código QR escaneado
            console.log("Manejar QR: " + data + " para la fila: " + rowIndex);
            // Por ejemplo, emitir un evento Livewire para actualizar los datos del backend
            window.livewire.emit('qrScanned', data, rowIndex);
        }
    </script>
@endsection
