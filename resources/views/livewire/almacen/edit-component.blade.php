<div class="container-fluid">
    <div class="page-title-box">
        <div class="row align-items-center">
            <div class="col-sm-6">
                <h4 class="page-title">EDITAR FACTURA {{ $identificador }}</span></h4>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-right">
                    <li class="breadcrumb-item"><a href="javascript:void(0);">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="javascript:void(0);">Facturas</a></li>
                    <li class="breadcrumb-item active">Editar Factura</li>
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
                                        name="num_albaran" id="num_albaran">
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
                                        placeholder="15/02/2023">
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
                                <input type="text" value="{{ $pedido->direccion_entrega }}" class="form-control"
                                    disabled>
                            </div>
                            <div class="form-group col-md-1">
                                &nbsp;
                            </div>
                            <div class="form-group col-md-5">
                                <label for="fecha">Localidad</label>
                                <input type="text" value="{{ $pedido->localidad_entrega }}" class="form-control"
                                    disabled>
                            </div>
                        </div>
                        <div class="form-row justify-content-center">
                            <div class="form-group col-md-5">
                                <label for="fecha">Provincia</label>
                                <input type="text" value="{{ $pedido->provincia_entrega }}" class="form-control"
                                    disabled>
                            </div>
                            <div class="form-group col-md-1">
                                &nbsp;
                            </div>
                            <div class="form-group col-md-5">
                                <label for="fecha">Código postal</label>
                                <input type="text" value="{{ $pedido->cod_postal_entrega }}" class="form-control"
                                    disabled>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <label for="observaciones" class="col-sm-12 col-form-label">Observaciones</label>
                            <div class="col-sm-12">
                                <textarea wire:model="observaciones" class="form-control" name="observaciones" id="observaciones"></textarea>
                                @error('descripcion')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
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
                                <table class="table ms-3 table-striped table-bordered dt-responsive nowrap">
                                    <thead>
                                        <tr>
                                            <th>Producto</th>
                                            <th>Lote</th>
                                            <th>Unidades</th>
                                        </tr>
                                    </thead>
                                    <tbody>

                                        @foreach ($productos_pedido as $producto)
                                            <tr>
                                                <td>{{ $this->getNombreTabla($producto['producto_lote_id']) }}
                                                </td>
                                                <td>{{ $this->getNombreLoteTabla($producto['producto_lote_id']) }}
                                                </td>
                                                @if (isset($producto['id']))
                                                    <td>{{ $producto['unidades'] + $producto['unidades_old'] }}
                                                    </td>
                                                @else
                                                    <td>{{ $producto['unidades'] }}</td>
                                                @endif
                                            </tr>
                                        @endforeach
                                        <tr>
                                            <th colspan="2">Importe</th>
                                            <th>{{ $pedido->precio }} €</td>
                                        </tr>
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
                            <button class="w-100 btn btn-info mb-2" id="alertaGuardar">Marcar pedido como enviado</button>
                            <button class="w-100 btn btn-info mb-2" id="alertaEliminar">Marcar pedido como completado</button>
                        </div>
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
                title: '¿Estás seguro?',
                icon: 'warning',
                showConfirmButton: true,
                showCancelButton: true
            }).then((result) => {
                if (result.isConfirmed) {
                    window.livewire.emit('update');
                }
            });
        });

        $("#alertaEliminar").on("click", () => {
            Swal.fire({
                title: '¿Estás seguro?',
                icon: 'error',
                showConfirmButton: true,
                showCancelButton: true
            }).then((result) => {
                if (result.isConfirmed) {
                    window.livewire.emit('updateCompletado');
                }
            });
        });

        $("#alertaFacturar").on("click", () => {
            Swal.fire({
                title: '¿Estás seguro?',
                icon: 'warning',
                showConfirmButton: true,
                showCancelButton: true
            }).then((result) => {
                if (result.isConfirmed) {
                    window.livewire.emit('aceptarFactura');
                }
            });
        });

        $("#alertaCancelar").on("click", () => {
            Swal.fire({
                title: '¿Estás seguro?',
                icon: 'error',
                showConfirmButton: true,
                showCancelButton: true
            }).then((result) => {
                if (result.isConfirmed) {
                    window.livewire.emit('cancelarFactura');
                }
            });
        });

        $("#alertaImprimir").on("click", () => {
            Swal.fire({
                title: '¿Estás seguro?',
                icon: 'info',
                showConfirmButton: true,
                showCancelButton: true
            }).then((result) => {
                if (result.isConfirmed) {
                    window.livewire.emit('imprimirFactura');
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
                placeholder: "-- Seleccione un presupuesto --",
                width: 'resolve'
            }).on('change', function() {
                var selectedValue = $(this).val();
                // Llamamos a la función listarPresupuesto() pasando el valor seleccionado
                Livewire.emit('listarPresupuesto', selectedValue);
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
            jQuery("#id_presupuesto").select2({
                minimumResultsForSearch: 2,
                allowClear: false
            });
        }

        initSelect2();
        window.livewire.on('select2', () => {
            initSelect2();
        });
    </script>
@endsection
