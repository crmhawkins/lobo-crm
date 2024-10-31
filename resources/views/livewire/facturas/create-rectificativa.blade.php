<div class="container-fluid">
    <script src="//unpkg.com/alpinejs" defer></script>
    <div class="page-title-box">
        <div class="row align-items-center">
            <div class="col-sm-6">
                <h4 class="page-title">CREAR FACTURA RECTIFICATIVA</span></h4>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-right">
                    <li class="breadcrumb-item"><a href="javascript:void(0);">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="javascript:void(0);">Facturas</a></li>
                    <li class="breadcrumb-item active">Crear Factura</li>
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

                        <div class="col-md-3" wire:ignore>
                            <label for="rectificativa" class="col-md-12 col-form-label">
                                Seleccione la factura a rectificar
                            </label>
                            <div x-data="" x-init="$nextTick(() => {
                                $('#facturaSeleccionadaId').select2();
                                $('#facturaSeleccionadaId').on('change', function(e) {
                                    var data = $('#facturaSeleccionadaId').select2('val');
                                    @this.set('facturaSeleccionadaId', data);
                                    console.log(data);
                                });
                            })">
                                <select class="form-control" name="facturaSeleccionadaId" id="facturaSeleccionadaId"
                                    wire:model="facturaSeleccionadaId">
                                    <option value="">---SELECCIONE UNA FACTURA---</option>
                                    @foreach ($facturas as $factura)
                                        <option value="{{ $factura->id }}">{{ $factura->numero_factura }} - {{ $factura->cliente->nombre }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
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
                            @if(isset($this->idpedido))
                            <div class="col-md-4">
                                <div class="col-sm-12">
                                    <label for="pedido_id" class="col-sm-12 col-form-label">Nº del pedido</label>
                                    <input type="text" wire:model="pedido_id" class="form-control" disabled>
                                </div>
                            </div>
                            @else

                            @endif

                            <div class="col-md-4" style="display:none">
                            <label for="Cliente" class="col-sm-12 col-form-label">Estado</label>
                                <div class="col-sm-12">
                                    <select class="form-control" name="estado" id="estado"
                                    wire:model="estado" >
                                        <option value="Pendiente">Pendiente</option>
                                        <option value="Cancelado">Cancelado</option>
                                        <option value="Pagado">Pagado</option>
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <label for="Cliente" class="col-sm-12 col-form-label">Cliente</label>
                                <div class="col-sm-12">
                                    @if(isset($this->idpedido))
                                        <select class="form-control" name="cliente_id" id="cliente_id"
                                            wire:model="cliente_id" disabled>
                                    @else
                                        <select class="form-control" name="cliente_id" id="cliente_id"
                                        wire:model="cliente_id"  wire:change="selectCliente()" disabled>
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
                                        placeholder="15/02/2023" disabled>
                                    @error('fecha_emision')
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


                    </form>
                </div>
                @if(count($productos_pedido) > 0)
                    <div class="card-body">
                        <div class="form-row justify-content-center">
                            <div class="form-group col-md-12">
                                <h5 class="ms-3"
                                    style="border-bottom: 1px gray solid !important;padding-bottom: 10px !important;display: flex !important;flex-direction: row;justify-content: space-between;">
                                    Lista de productos
                                </h5>
                                <div class="form-group col-md-12">
                                    @if (count($productos_pedido) > 0)
                                        <table class="table ms-3 table-striped table-bordered dt-responsive nowrap">
                                            <thead>
                                                <tr>
                                                    <th>Producto</th>
                                                    <th>Cantidad</th>
                                                    <th>Precio unidad</th>
                                                    <th>Precio total</th>
                                                    <th>Unidades a descontar</th>
                                                    <th>Eliminar</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($productos_pedido as $productoIndex => $producto)
                                                    <tr>
                                                        <td>{{ $this->getNombreTabla($producto['producto_pedido_id']) }}</td>
                                                        <td>{{ $this->getUnidadesTabla($productoIndex) }}</td>
                                                        <td><input type="number" wire:model.lazy="productos_pedido.{{ $productoIndex }}.precio_ud" wire:change="actualizarPrecioTotal({{$productoIndex}})" class="form-control" style="width:70%; display:inline-block" disabled>€</td>
                                                        <td>{{ $producto['precio_total']}} €</td>
                                                        <td><input type="number" wire:model="productos_pedido.{{ $productoIndex }}.descontar_ud" wire:change="changeDescontar({{$productoIndex}})" class="form-control" style="width:70%; display:inline-block" @if(!$this->hasStockEntrante($productos_pedido[$productoIndex]['lote_id'])) disabled @endif></td>
                                                        <td><button type="button" class="btn btn-danger" wire:click="deleteArticulo('{{$productoIndex}}')">X</button></td>
                                                    </tr>
                                                @endforeach
                                                {{-- <tr>
                                                    <th colspan="3">Precio estimado</th>
                                                    <th>{{ $precioSinDescuento }} €</td>
                                                </tr> --}}
                                            </tbody>
                                        </table>
                                        {{-- {{ var_dump($productos_pedido) }} --}}
                                    @endif
                                </div>
                            </div>

                        </div>

                    </div>
                @endif
            </div>

        </div>

        <div class="col-md-3">
            <div class="card m-b-30">
                <div class="card-body">
                    <h5>Acciones</h5>
                    <div class="row">
                        <div class="col-12">
                            <button class="w-100 btn btn-success mb-2" id="alertaGuardar">Guardar
                                factura</button>
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
                    window.livewire.emit('submit');
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

@endsection
