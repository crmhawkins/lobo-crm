<div class="container-fluid">
    <script src="//unpkg.com/alpinejs" defer></script>
    <div class="page-title-box">
        <div class="row align-items-center">
            <div class="col-sm-6">
                <h4 class="page-title">CREAR FACTURA</span></h4>
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
    <div wire:ignore.self class="modal fade" id="viewModal" tabindex="-1" role="dialog">
        <div class="modal-dialog"
            style="min-width: 25vw !important; align-self: center !important; margin-top: 0 !important;">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Añadir servicios</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <label for="nombreServicio">Nombre del servicio</label>
                    <textarea  wire:model="descripcionServicio" class="form-control mb-1" placeholder="Descripción"> </textarea>
                    <label for="cantidad">Cantidad</label>
                    <input type="number" wire:model="cantidad" class="form-control mb-1" placeholder="Cantidad">
                    <label for="importeServicio">Importe</label>
                    <input type="number" wire:model="importeServicio" class="form-control mb-1" placeholder="Precio">
                    <button class="btn btn-success" wire:click="addArticulo()">Añadir</button>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>
    <!-- end page-title -->
    <div class="row" style="align-items: start !important">
        <div class="col-md-9">
            <div class="card m-b-30">
                <div class="card-body">
                    <form wire:submit.prevent="submit">
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
                            @if(isset($this->idpedido))
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
                                        wire:model="cliente_id"  wire:change="selectCliente()">
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
                                        placeholder="15/02/2023">
                                    @error('fecha_emision')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-4">
                                <label for="fecha_vencimiento" class="col-sm-12 col-form-label">Fecha de vencimiento</label>
                                <div class="col-sm-12">
                                    <input type="date" wire:model="fecha_vencimiento" class="form-control"
                                        placeholder="18/02/2023">
                                    @error('fecha_vencimiento')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            {{-- @if(is_null($this->idpedido))
                            <div class="col-md-4">
                                <label for="Cliente" class="col-sm-12 col-form-label">Producto</label>
                                <div class="col-sm-12">
                                    <select class="form-control" name="producto_id" id="producto_id" wire:model="producto_id" wire:change='calculoPrecio()'>
                                        <option value="">---SELECCIONE UN PRODUCTO---</option>
                                        @foreach ($productos as $producto)
                                            <option value="{{ $producto->id }}">{{ $producto->nombre }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            @endif --}}
                        </div>
                        <div class="form-group row">
                            

                            <div class="col-md-4">
                                <label for="metodo_pago" class="col-sm-12 col-form-label">Método de pago</label>
                                <div class="col-sm-12" wire:ignore.self>
                                    <select id="metodo_pago" class="form-control" wire:model="metodo_pago" >
                                            <option value="No pagado"  selected>Selecciona una opción</option>
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
                        
                        
                        @if($idpedido != null)
                            
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
                        @endif
                        
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
                                <label for="descripcion" class="col-sm-12 col-form-label">Observaciones Descarga </label>
                                <div class="col-sm-12">
                                    <textarea wire:model="observacionesDescarga" class="form-control" name="observacionesDescarga" id="observacionesDescarga"
                                        placeholder="Observaciones..." disabled></textarea>
                                    @error('observacionesDescarga')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </form>
                    
                    @if($idpedido == null)
                        <button class="btn btn-info" data-toggle="modal" data-target="#viewModal">Añadir Servicio</button>
                    @endif
                    
                    <div class="form-group row mt-2">
                        @if(count($servicios) > 0)
                            <h3 class="ms-3">Lista de servicios</h3>
                            <div class="form-group col-md-11">
                                    <table class="table ms-3 table-striped table-bordered dt-responsive nowrap">

                                        <tr>
                                            <th>Nombre</th>
                                            <th>Cantidad</th>
                                            <th>Importe</th>
                                            <th>Eliminar</th>
                                        </tr>
                                        @foreach ($servicios as $index =>  $servicio)
                                            <tr>
                                                <td>{{ $servicio['descripcion'] }}</td>
                                                <td>{{ $servicio['cantidad'] }}</td>
                                                <td>{{ $servicio['importe'] }}</td>
                                                <td><button type="button" class="btn btn-danger" wire:click="deleteServicio({{ $index }})">X</button></td>
                                            </tr>
                                        @endforeach

                                    </table>
                            </div>
                        @endif
                    </div>
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
                                                    <th>Eliminar</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($productos_pedido as $productoIndex => $producto)
                                                    <tr>
                                                        <td>{{ $this->getNombreTabla($producto['producto_pedido_id']) }}</td>
                                                        <td>{{ $this->getUnidadesTabla($productoIndex) }}</td>
                                                        <td><input type="number" wire:model.lazy="productos_pedido.{{ $productoIndex }}.precio_ud" wire:change="actualizarPrecioTotal({{$productoIndex}})" class="form-control" style="width:70%; display:inline-block">€</td>
                                                        <td>{{ $producto['precio_total']}} €</td>
                                                        <td><button type="button" class="btn btn-danger" wire:click="deleteArticulo('{{$productoIndex}}')">X</button></td>
                                                    </tr>
                                                @endforeach
                                                {{-- <tr>
                                                    <th colspan="3">Precio estimado</th>
                                                    <th>{{ $precioSinDescuento }} €</td>
                                                </tr> --}}
                                            </tbody>
                                        </table>
                                    @endif
                                </div>
                            </div>
                            <div class="form-group col-md-5 d-flex align-items-center">
                                <div class="form-group col-md-6 d-flex align-items-center">
                                    <label for="descuento">Descuento</label>
                                    <input type="checkbox" id="descuento" wire:model="descuento" class="form-checkbox" wire:change='setPrecioEstimado()' style="margin-left: 10px; width: 20px; height: 20px;">
                                </div>
                                @if ($descuento)
                                    <div class="form-group col-md-6 d-flex flex-column justify-content-center">
                                        <label>Porcentaje descuento</label>
                                        <input class="form-control"  type="number" wire:model="porcentaje_descuento"  wire:change='setPrecioEstimado()' placeholder="Ingrese el valor del descuento">
                                    </div>
                                @endif
                            </div>
                            <div class="form-group col-md-1">
                            &nbsp;
                            </div>
                            
                        </div>
                        @if (count($productos_pedido) > 0)
                            <div class="d-flex col-12">
                                <div class="form-group col-md-4">
                                    <label for="subtotal">Subtotal</label>
                                    <input type="text" wire:model="subtotal" class="form-control" readonly>
                                </div>

                                <div class="form-group col-md-4">
                                    <label for="subtotal">Descuento total</label>
                                    <input type="text" wire:model="descuento_total" class="form-control" readonly>
                                </div>
                                <div class="form-group col-md-4">
                                    <label for="fecha">Precio final</label>
                                    <input type="text" wire:model="precio" class="form-control" readonly>
                                </div>
                            </div>
                        @endif

                        
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
@endsection
