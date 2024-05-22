@php
$EsAdmin = Auth::user()->isAdmin();
$canEdit = $EsAdmin; //|| $estado == 1;
@endphp
<div class="container-fluid">
    <div class="page-title-box">
        <div class="row align-items-center">
            <div class="col-sm-6">
                <h4 class="page-title">EDITAR FACTURA {{ $this->numero_factura }}</span></h4>
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
                            <div class="col-md-4">
                                <label for="numero_factura" class="col-sm-12 col-form-label">Número de Factura</label>
                                <div class="col-sm-12">
                                    <input type="text" wire:model="numero_factura" class="form-control"
                                        name="numero_factura" id="numero_factura" disabled>
                                    @error('numero_factura')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            @if(isset($this->pedido_id))
                            <div class="col-md-4">
                                <div class="col-sm-12">
                                    <label for="pedido_id" class="col-sm-12 col-form-label">Nº del pedido</label>
                                    <input type="text" wire:model="pedido_id" class="form-control" disabled>
                                </div>
                            </div>
                            @else

                            @endif

                            <div class="col-md-4">
                            <label for="Cliente" class="col-sm-12 col-form-label">Estado</label>
                                <div class="col-sm-12">
                                    <select class="form-control" name="estado" id="estado"
                                    wire:model="estado" @if(!$canEdit) disabled @endif>
                                        <option value="Pendiente">Pendiente</option>
                                        <option value="Cancelado">Cancelado</option>
                                        <option value="Pagado">Pagado</option>
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <label for="Cliente" class="col-sm-12 col-form-label">Cliente</label>
                                <div class="col-sm-12">
                                    @if(isset($this->pedido_id))
                                        <select class="form-control" name="cliente_id" id="cliente_id"
                                            wire:model="cliente_id" disabled>
                                    @else
                                        <select class="form-control" name="cliente_id" id="cliente_id"
                                        wire:model="cliente_id" @if(!$canEdit) disabled @endif>
                                    @endif
                                            <option value="">---SELECCIONE UN CLIENTE---</option>
                                            @foreach ($clientes as $cliente)
                                                <option value="{{ $cliente->id }}">{{ $cliente->nombre }}</option>
                                            @endforeach
                                        </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <label for="fecha_emision" class="col-sm-12 col-form-label">Fecha de emisión</label>
                                <div class="col-sm-12">
                                    <input type="date" wire:model="fecha_emision" class="form-control"
                                        placeholder="15/02/2023" @if(!$canEdit) disabled @endif>
                                    @error('fecha_emision')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-4">
                                <label for="fecha_vencimiento" class="col-sm-12 col-form-label">Fecha de vencimiento</label>
                                <div class="col-sm-12">
                                    <input type="date" wire:model="fecha_vencimiento" class="form-control"
                                        placeholder="18/02/2023" @if(!$canEdit) disabled @endif>
                                    @error('fecha_vencimiento')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            @if(is_null($this->pedido_id))
                            <div class="col-md-4">
                                <label for="Cliente" class="col-sm-12 col-form-label">Producto</label>
                                <div class="col-sm-12">
                                    <select class="form-control" name="producto_id" id="producto_id" wire:model="producto_id" wire:change='calculoPrecio()' @if(!$canEdit) disabled @endif>
                                        <option value="0">---SELECCIONE UN PRODUCTO---</option>
                                        @foreach ($productos as $producto)
                                            <option value="{{ $producto->id }}">{{ $producto->nombre }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            @endif
                        </div>
                        <div class="form-group row">
                            <div class="col-md-4">
                                <label for="fecha_emision" class="col-sm-12 col-form-label">Cantidad</label>
                                <div class="col-sm-12">
                                    @if(isset($this->pedido_id))
                                    <input type="number" wire:model="cantidad" class="form-control"
                                        placeholder="cantidad" disabled>
                                    @else
                                    <input type="number" wire:model="cantidad" class="form-control"
                                        placeholder="cantidad" wire:change='calculoPrecio()' @if(!$canEdit) disabled @endif>
                                    @endif
                                    @error('cantidad')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-4">
                                <label for="metodo_pago" class="col-sm-12 col-form-label">Método de pago</label>
                                <div class="col-sm-12" wire:ignore.self>
                                    <select id="metodo_pago" class="form-control" wire:model="metodo_pago" @if(!$canEdit) disabled @endif>
                                            <option value="" disabled selected>Selecciona una opción</option>
                                            <option value="giro_bancario">Giro Bancario</option>
                                            <option value="pagare">Pagare</option>
                                            <option value="confirming">Confirming</option>
                                            <option value="transferencia">Transferencia</option>
                                            <option value="otros">Otros</option>
                                        </select>
                                    @error('denominacion')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="col-md-4">
                                <label for="fecha_emision" class="col-sm-12 col-form-label">Importe sin descuento</label>
                                <div class="col-sm-12">
                                    
                                    <input type="number" wire:model="subtotal_pedido" class="form-control"
                                        placeholder="subtotal pedido" readonly>

                                </div>
                            </div>
                            <div class="col-md-4">
                                <label for="descuento" class="col-sm-12 col-form-label">Descuento</label>
                                <div class="col-sm-12">
                                    <input type="number" wire:model="descuento" class="form-control"
                                        placeholder="descuento" readonly>
                                        @error('precio')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <label for="fecha_emision" class="col-sm-12 col-form-label">Total descuento</label>
                                <div class="col-sm-12">
                                    
                                    <input type="number" wire:model="descuento_total_pedido" class="form-control"
                                        placeholder="descuento total pedido" readonly>

                                </div>
                            </div>
                            <div class="col-md-4">
                                <label for="fecha_emision" class="col-sm-12 col-form-label">Importe</label>
                                <div class="col-sm-12">
                                    
                                    <input type="number" wire:model="precio" class="form-control"
                                        placeholder="importe" readonly>

                                </div>
                            </div>
                            <div class="col-md-4">
                                <label for="iva_total_pedido" class="col-sm-12 col-form-label">Iva total</label>
                                <div class="col-sm-12">
                                    
                                    <input type="number" wire:model="iva_total_pedido" class="form-control"
                                        placeholder="iva total pedido" readonly>

                                </div>
                            </div>

                            <div class="col-md-4">
                                <label for="total" class="col-sm-12 col-form-label">Total</label>
                                <div class="col-sm-12">
                                    
                                    <input type="number" wire:model="total" class="form-control"
                                        placeholder="total" readonly>

                                </div>
                            </div>

                            
                        </div>
                        
                        <div class="form-group row">
                            <div class="col-md-12">
                                <label for="descripcion" class="col-sm-12 col-form-label">Descripción </label>
                                <div class="col-sm-12">
                                    <textarea wire:model="descripcion" class="form-control" name="descripcion" id="descripcion"
                                        placeholder="Factura para el cliente Dani..." @if(!$canEdit) disabled @endif></textarea>
                                    @error('descripcion')
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
            @if ($canEdit)
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
            @endif
            <div class="card m-b-30">
                <div class="card-body">
                    <h5>Acciones</h5>
                    <div class="row">
                        <div class="col-12">
                            @if ($estado == "pendiente")
                            <button class="w-100 btn btn-info mb-2" id="alertaFacturar">Marcar como facturada</button>
                            @endif
                            <button class="w-100 btn btn-info mb-2" id="EmailFacturarIva">Enviar factura por correo Con IVA</button>
                            <button class="w-100 btn btn-info mb-2" id="EmailFacturar">Enviar factura por correo Sin IVA</button>
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

        $("#EmailFacturarIva").on("click", () => {
            Swal.fire({
                title: '¿Estás seguro?',
                icon: 'info',
                showConfirmButton: true,
                showCancelButton: true
            }).then((result) => {
                if (result.isConfirmed) {
                    window.livewire.emit('imprimirFacturaIva');
                }
            });
        });

        $("#EmailFacturar").on("click", () => {
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

        initSelect2();
        window.livewire.on('select2', () => {
            initSelect2();
        });
    </script>
@endsection
