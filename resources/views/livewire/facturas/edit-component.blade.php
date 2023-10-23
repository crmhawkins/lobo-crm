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
    <div class="row">
        <div class="col-md-9">
            <div class="card m-b-30">
                <div class="card-body">
                    <form wire:submit.prevent="update">
                        <input type="hidden" name="csrf-token" value="{{ csrf_token() }}">
                        <div class="form-group row">
                            <div class="col-md-3">
                                <label for="numero_factura" class="col-sm-12 col-form-label">Número de Factura</label>
                                <div class="col-sm-12">
                                    <input type="text" wire:model="numero_factura" class="form-control"
                                        name="numero_factura" id="numero_factura">
                                    @error('numero_factura')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-9">
                                <label for="id_presupuesto" class="col-sm-12 col-form-label">Presupuesto
                                    asociado</label>
                                <div class="col-sm-12" wire:ignore.self>
                                    <select id="id_presupuesto" class="form-control js-example-responsive"
                                        wire:model="id_presupuesto">
                                        <option value="0">-- Seleccione un presupuesto --</option>
                                        @foreach ($presupuestos as $presup)
                                            <option value="{{ $presup->id }}">{{ $presup->id }} </option>
                                        @endforeach
                                    </select>
                                    @error('id_presupuesto')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        @if ($estadoPresupuesto != 0)
                            <div class="form-group row">
                                <div class="col-md-6">
                                    <label for="detalles_presupuesto" class="col-sm-12 col-form-label">Detalles del
                                        presupuesto</label>
                                    <div class="col-sm-12">
                                        <a href="{{ route('presupuestos.edit', ['id' => $presupuestoSeleccionado->id]) }}"
                                            class="btn btn-primary w-100" target="_blank"> &nbsp;Detalles del
                                            presupuesto</a>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <label for="detalles_presupuesto" class="col-sm-12 col-form-label">&nbsp;</label>
                                    <div class="col-sm-12"> <a
                                            href="{{ route('eventos.edit', ['id' => $presupuestoSeleccionado->id_evento]) }}"
                                            class="btn btn-primary w-100" target="_blank"> &nbsp;Detalles del evento
                                            presupuesto</a>
                                    </div>
                                </div>
                            </div>


                            <div class="form-group row">
                                <div class="col-md-4">
                                    <label for="total_sin_iva" class="col-sm-12 col-form-label">Precio base</label>
                                    <div class="col-sm-12">
                                        <input type="text" disabled
                                            value="{{ $presupuestoSeleccionado->precioBase }}€" class="form-control"
                                            name="total_sin_iva">
                                        @error('detalles_presupuesto')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <label for="descuento" class="col-sm-12 col-form-label">Descuento</label>
                                    <div class="col-sm-12">
                                        <input disabled type="text"
                                            value="{{ $presupuestoSeleccionado->descuento }}€" class="form-control"
                                            name="descuento">
                                        @error('detalles_presupuesto')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-4">

                                    <label for="precio" class="col-sm-12 col-form-label">Precio total</label>
                                    <div class="col-sm-12">
                                        <input disabled type="text"
                                            value="{{ $presupuestoSeleccionado->precioFinal }}€" class="form-control"
                                            name="precio">
                                        @error('detalles_presupuesto')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        @endif

                        <div class="form-group row">
                            <div class="col-md-6">
                                <label for="fecha_emision" class="col-sm-12 col-form-label">Fecha de emisión</label>
                                <div class="col-sm-12">
                                    <input type="text" wire:model.defer="fecha_emision" class="form-control"
                                        placeholder="15/02/2023" id="datepicker">
                                    @error('fecha_emision')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <label for="fecha_vencimiento" class="col-sm-12 col-form-label">Fecha de
                                    vencimiento</label>
                                <div class="col-sm-12">
                                    <input type="text" wire:model.defer="fecha_vencimiento" class="form-control"
                                        placeholder="18/02/2023" id="datepicker2">
                                    @error('fecha_vencimiento')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="form-group row">
                            <div class="col-md-12">
                                <label for="descripcion" class="col-sm-12 col-form-label">Descripción </label>
                                <div class="col-sm-12">
                                    <textarea wire:model="descripcion" class="form-control" name="descripcion" id="descripcion"
                                        placeholder="Factura para el cliente Dani..."></textarea>
                                    @error('descripcion')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="col-md-12">
                                <label for="metodo_pago" class="col-sm-12 col-form-label">Método de pago</label>
                                <div class="col-sm-12" wire:ignore.self>
                                    <select id="metodo_pago" class="form-control" wire:model="metodo_pago">
                                        {{-- <option value="Pendiente">-- Seleccione un estado para el presupuesto--</option> --}}
                                        <option value="No pagado">No pagado</option>
                                        <option value="En efectivo">En efectivo</option>
                                        <option value="Tarjeta de crédito">Tarjeta de crédito</option>
                                        <option value="Transferencia bancaria">Transferencia bancaria</option>
                                    </select>
                                    @error('denominacion')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card m-b-30">
                <div class="card-body">
                    <h5>Opciones de guardado</h5>
                    <div class="row">
                        <div class="col-12">
                            <button class="w-100 btn btn-success mb-2" id="alertaGuardar">Guardar
                                factura</button>
                            <button class="w-100 btn btn-danger mb-2" id="alertaEliminar">Borrar
                                factura</button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card m-b-30">
                <div class="card-body">
                    <h5>Acciones</h5>
                    <div class="row">
                        <div class="col-12">
                            @if($estado == 'Pendiente')
                            <button class="w-100 btn btn-info mb-2" id="alertaFacturar">Marcar como facturada</button>
                            <button class="w-100 btn btn-dark mb-2" id="alertaCancelar">Marcar como cancelada</button>
                            @else
                            <button class="w-100 btn btn-warning mb-2" wire:click.prevent="imprimirFactura">Descargar PDF de la factura</button>
                            <button class="w-100 btn btn-info mb-2" id="alertaPDF">Enviar factura por correo</button>
                            @endif

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
                    window.livewire.emit('confirmDelete');
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
