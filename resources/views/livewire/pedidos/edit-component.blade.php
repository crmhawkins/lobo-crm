@php
$mostrarElemento = Auth::user()->role == 2;
$EsAdmin = Auth::user()->isAdmin();
$canEdit = $EsAdmin; //|| $estado == 1;
@endphp
<div class="container-fluid">
    <div class="page-title-box">
        <div class="row align-items-center">
            <div class="col-sm-6">
                <h4 class="page-title">TRAMITAR PEDIDO</span></h4>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-right">
                    <li class="breadcrumb-item"><a href="javascript:void(0);">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="javascript:void(0);">Pedidos</a></li>
                    <li class="breadcrumb-item active">Tramitar pedido</li>
                </ol>
            </div>
        </div>
    </div>
    <div class="row" style="align-items: start !important">
        <div class="col-md-9">
            <div class="card m-b-30">
                <div class="card-body">
                    <div class="form-row justify-content-center">
                        <div class="form-group col-md-12">
                            <h5 class="ms-3"
                                style="border-bottom: 1px gray solid !important; padding-bottom: 10px !important;">Datos
                                básicos del pedido</h5>
                        </div>

                        <div class="form-group col-md-4" wire:ignore >
                            <div x-data="" x-init="
                                $('#select2-cliente').select2();
                                $('#select2-cliente').on('change', function (e) {
                                    var data = $(this).select2('val');
                                    @this.set('cliente_id', data);
                                    @this.call('selectCliente');
                                });">
                                <label for="select2-cliente">Cliente</label>
                                @if ($canEdit)
                                    <select class="form-control" name="cliente_id" id="select2-cliente" wire:model="cliente_id">
                                        <option value=""></option>
                                        @foreach ($clientes as $client)
                                            <option value="{{ $client->id }}">{{ $client->nombre }}</option>
                                        @endforeach
                                    </select>
                                @else
                                    <select class="form-control" name="cliente_id" id="select2-cliente" wire:model="cliente_id" disabled>
                                        <option value=""></option>
                                        @foreach ($clientes as $client)
                                            <option value="{{ $client->id }}">{{ $client->nombre }}</option>
                                        @endforeach
                                    </select>
                                @endif
                            </div>
                        </div>
                        <div class="form-group col-md-4">
                            <label for="fecha">Fecha</label>
                            <input type="text" value="{{ $fecha }}" class="form-control" disabled>
                        </div>
                        <div class="form-group col-md-4" wire:ignore>
                            <label for="estado">Estado</label>
                            <input type="text" value="{{ $this->getEstadoNombre() }}" class="form-control" disabled>
                        </div>
                    </div>

                    <div class="form-row justify-content-center">
                        <div class="form-group col-md-6" wire:ignore>
                            <div x-data="" x-init="$('#select2-tipo').select2();
                                $('#select2-tipo').on('change', function(e) {
                                var data = $('#select2-tipo').select2('val');
                                @this.set('tipo_pedido_id', data);
                                });">
                                <label for="fechaVencimiento">Tipo de pedido</label>
                                @if ($canEdit)
                                    <select class="form-control" name="estado" id="select2-tipo" wire:model= "tipo_pedido_id">
                                        <option value="0">Albarán y factura</option>
                                        <option value="1">Albarán sin factura</option>
                                    </select>
                                @else
                                    <select class="form-control" name="estado" id="select2-tipo" wire:model= "tipo_pedido_id" disabled>
                                        <option value="0">Albarán y factura</option>
                                        <option value="1">Albarán sin factura</option>
                                    </select>
                                @endif
                            </div>
                        </div>
                        @if($mostrarElemento || $canEdit)
                            <!-- Si la condición es verdadera, muestra esto -->
                            <div class="form-group col-md-6" wire:ignore>

                                <div x-data="" x-init="$('#select2-almacen').select2();
                                    $('#select2-almacen').on('change', function(e) {
                                    var data = $('#select2-almacen').select2('val');
                                    @this.set('almacen_id', data);
                                    });">
                                    <label for="fechaVencimiento">Almacen</label>
                                    @if ($canEdit || $mostrarElemento)
                                        <select name="almacen" id="select2-almacen" wire:model="almacen_id" style="width: 100% !important">
                                            <option value="{{ null }}">-- Selecciona un almacén --</option>
                                            @foreach ($almacenes as $presup)
                                                <option value="{{ $presup->id }}">{{ $presup->almacen }}</option>
                                            @endforeach
                                        </select>
                                    @else
                                        <select name="almacen" id="select2-almacen" wire:model="almacen_id" style="width: 100% !important" disabled>
                                            <option value="{{ null }}">-- Selecciona un almacén --</option>
                                            @foreach ($almacenes as $presup)
                                                <option value="{{ $presup->id }}">{{ $presup->almacen }}</option>
                                            @endforeach
                                        </select>
                                    @endif
                                </div>
                            </div>
                        @else
                            <div class="form-group col-md-6" wire:ignore>
                                <!-- Aquí va tu código HTML pero con el select deshabilitado -->
                                <div x-data="" x-init="$('#select2-tipo1').select2();
                                    $('#select2-tipo1').on('change', function(e) {
                                    var data = $('#select2-tipo1').select2('val');
                                    @this.set('almacen_id', data);
                                    });" style="pointer-events: none; opacity: 0.5;">
                                    <label for="fechaVencimiento">Almacen</label>
                                    <select name="almacen" id="select2-almacen" wire:model="almacen_id" style="width: 100% !important" disabled>
                                        <option value="{{ null }}">-- Selecciona un almacén --</option>
                                        @foreach ($almacenes as $presup)
                                            <option value="{{ $presup->id }}">{{ $presup->almacen }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        @endif
                    </div>

                    <div class="form-row justify-content-center">
                        <div class="form-group col-md-12">
                            <h5 class="ms-3"
                                style="border-bottom: 1px gray solid !important; padding-bottom: 10px !important;">Datos
                                de envío</h5>
                        </div>
                        @if($fecha_salida != null && $empresa_transporte != null)
                            <div class="form-group col-md-5">
                                <label for="localidad_entrega">Fecha de Salida @if($estado ==8)<span class="badge badge-warning">En ruta</span> @endif</label>
                                <input type="date" wire:model="fecha_salida" class="form-control" readonly>
                            </div>
                            <div class="form-group col-md-1">
                                &nbsp;
                            </div>
                            <div class="form-group col-md-5">
                                <label for="localidad_entrega">Empresa de transporte @if($estado ==8)<span class="badge badge-warning">En ruta</span>@endif</label>
                                <input type="text" wire:model="empresa_transporte" class="form-control" readonly>
                            </div>
                        @endif
                    </div>
                    <div class="form-row justify-content-center">
                        <div class="form-group col-md-5">
                            <label for="localidad_entrega">Dirección</label>
                            <input type="text" wire:model="direccion_entrega" class="form-control" readonly>
                        </div>
                        <div class="form-group col-md-1">
                            &nbsp;
                        </div>
                        <div class="form-group col-md-5">
                            <label for="localidad_entrega">Localidad</label>
                            <input type="text" wire:model="localidad_entrega" class="form-control" readonly>
                        </div>
                    </div>
                    <div class="form-row justify-content-center">
                        <div class="form-group col-md-5">
                            <label for="provincia_entrega">Provincia</label>
                            <input type="text" wire:model="provincia_entrega" class="form-control" readonly>
                        </div>
                        <div class="form-group col-md-1">
                            &nbsp;
                        </div>
                        <div class="form-group col-md-5">
                            <label for="cod_postal_entrega">Código postal</label>
                            <input type="text" wire:model="cod_postal_entrega" class="form-control" readonly>
                        </div>
                        <div class="form-group col-md-5">
                            <label for="npedido_cliente" >Nº Pedido Cliente</label>
                            <input wire:model="npedido_cliente" class="form-control">
                        </div>
                        <div class="form-group col-md-1">
                            &nbsp;
                        </div>
                        <div class="form-group col-md-5">
                           
                        </div>
                    </div>
                    <div class="form-row mb-4 justify-content-center">
                       {{-- <div class="form-group col-md-5">
                            <label for="fecha">Órden de entrega</label>
                            <input type="text" wire:model="orden_entrega" class="form-control">
                        </div>
                        <div class="form-group col-md-1">
                            &nbsp;
                        </div>--}}
                        <div class="form-group col-md-11">
                            <label for="fecha">Observaciones</label>
                            @if ($canEdit)
                                <textarea wire:model="observaciones" class="form-control"></textarea>
                            @else
                                <textarea wire:model="observaciones" class="form-control" disabled></textarea>
                            @endif

                        </div>
                    </div>
                </div>
            </div>
            <div class="card m-b-30">
                <div class="card-body">
                    <div class="form-row justify-content-center">
                        <div class="form-group col-md-12">
                            <h5 class="ms-3"
                                style="border-bottom: 1px gray solid !important;padding-bottom: 10px !important;display: flex !important;flex-direction: row;justify-content: space-between;">
                                Lista de productos
                                @if ($canEdit)
                                    <button type="button" class="btn btn-primary" data-toggle="modal" style="align-self: end !important;" data-target="#addProductModal">Añadir</button>
                                @else
                                    <button type="button" class="btn btn-secondary"  style="align-self: end !important;">Añadir</button>
                                @endif
                            </h5>
                            <div class="form-group col-md-12">
                                @if (count($productos_pedido) > 0)
                                    <div class="table-responsive">
                                        <table class="table ms-3 table-striped table-bordered dt-responsive nowrap">
                                            <thead>
                                                <tr>
                                                    <th>Producto</th>
                                                    <th>Cantidad</th>
                                                    <th>Precio unidad</th>
                                                    <th>Precio total</th>
                                                    <th>Acciones</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($productos_pedido as $productoIndex => $producto)
                                                    <tr>
                                                        <td>{{ $this->getNombreTabla($producto['producto_pedido_id']) }}
                                                        </td>
                                                        <td>{{ $this->getUnidadesTabla($productoIndex) }}</td>
                                                        @if ($canEdit)
                                                            <td><input type="number" wire:model.lazy="productos_pedido.{{ $productoIndex }}.precio_ud" wire:change="actualizarPrecioTotal({{$productoIndex}})" class="form-control" style="width:70%; display:inline-block ; min-width: 80px;">€</td>
                                                        @else
                                                            <td><input type="number" wire:model.lazy="productos_pedido.{{ $productoIndex }}.precio_ud" wire:change="actualizarPrecioTotal({{$productoIndex}})" class="form-control" style="width:70%; display:inline-block; min-width: 80px;" disabled>€</td>
                                                        @endif
                                                        <td>{{ $producto['precio_total']}} €</td>
                                                        @if ($canEdit)
                                                            <td class="">
                                                                @if(Auth::user()->role != 3 && Auth::user()->role != 2)
                                                                    <button type="button" class="btn btn-danger" wire:click="deleteArticulo('{{ $productoIndex }}')">X</button>
                                                                    <button type="button" class="btn btn-primary" data-toggle="modal" style="align-self: end !important;" data-target="#editProductModal" wire:click="selectProduct({{$producto['producto_pedido_id']}}, {{ $producto['precio_ud'] }}, {{ $producto['unidades'] }}, {{ $productoIndex }})">Editar</button>
                                                                @endif
                                                            </td>
                                                        @else
                                                            <td class="d-flex flex-nowrap">
                                                                <button type="button" class="btn btn-secondary">X</button>
                                                                <button class="btn btn-info">Editar</button>
                                                            </td>
                                                        @endif
                                                    </tr>
                                                @endforeach
                                                <tr>
                                                    <th colspan="3">Precio estimado</th>
                                                    <th>{{ $precioSinDescuento }} €</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                @endif
                            </div>
                        </div>
                        <div class="d-flex col-12">
                            <div class="form-group col-md-6 d-flex align-items-center">
                                <div class="form-group col-md-4 d-flex align-items-center">
                                <label for="descuento">Descuento</label>
                                @if ($canEdit)
                                    <input type="checkbox" id="descuento" wire:model="descuento" class="form-checkbox" wire:change='setPrecioEstimado()' style="margin-left: 10px; width: 20px; height: 20px;">
                                @else
                                    <input type="checkbox" id="descuento" wire:model="descuento" class="form-checkbox" wire:change='setPrecioEstimado()' style="margin-left: 10px; width: 20px; height: 20px;" disabled>
                                @endif
                            </div>
                            @if ($descuento)
                                <div class="form-group col-md-4 d-flex flex-column justify-content-center">
                                    <label for="porcentaje_descuento">Porcentaje descuento</label>
                                    @if ($canEdit)
                                        <input type="number" wire:model="porcentaje_descuento"  wire:change='setPrecioEstimado()' placeholder="Ingrese el valor del descuento">
                                    @else

                                    <input type="number" wire:model="porcentaje_descuento"  wire:change='setPrecioEstimado()' placeholder="Ingrese el valor del descuento" disabled>
                                    @endif
                                </div>
                            @endif
                            <div class="form-group col-md-4">
                                <label for="fecha">Gastos de envío</label>
                                <input type="number" min=0 wire:model="gastos_envio" wire:change='setPrecioEstimado()' class="form-control" >
                            </div>
                            <div class="form-group col-md-4">
                                <label for="fecha">Empresa de transporte</label>
                                <input type="text" wire:model="transporte" class="form-control" >
                            </div>
                            <div class="form-group col-md-4">
                                <label for="fecha">Precio final</label>
                                <input type="text" wire:model="precio" class="form-control" readonly>
                            </div>
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
                                <label for="subtotal">Total Iva</label>
                                <input type="text" wire:model="iva_total" class="form-control" readonly>
                            </div>
                        </div>
                    @endif
                </div>

                <div wire:ignore.self class="modal fade" id="addProductModal" tabindex="-1" role="dialog">
                    <div class="modal-dialog"
                        style="min-width: 25vw !important; align-self: center !important; margin-top: 0 !important;">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">Añadir Producto</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                @if ($producto_seleccionado != null)
                                    <div class="row justify-content-center">
                                        <div class="col-md-12">
                                            <div class="card border border-dark border-1"
                                                style="margin-bottom: 5px !important">
                                                <div class="card-body"
                                                            style="
                                                        display: flex;
                                                        flex-direction: column;
                                                        flex-wrap: wrap;
                                                        align-items: center;
                                                        justify-content: center;
                                                    ">
                                                    <h2 class="card-title mt-0 font-32"
                                                        style="text-align: center; margin-bottom: -0.25rem !important;">
                                                        {{ $this->getProductoNombre() }}</h2>
                                                    <img class="mx-auto" src="{{ $this->getProductoImg() }}"
                                                        style="max-width: 30%; text-align:center;"
                                                        alt="Card image cap">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                                <div class="row justify-content-center">
                                    <div class="col-md-10" style="text-align: center !important;">
                                        <label for="fechaVencimiento">Producto seleccionado</label>
                                    </div>
                                    <div class="col-md-10" wire:ignore>
                                        <div x-data="" x-init="$('#select2-producto').select2();
                                        $('#select2-producto').on('change', function(e) {
                                            var data = $('#select2-producto').select2('val');
                                            @this.set('producto_seleccionado', data);
                                            @this.set('unidades_pallet_producto', 0);
                                            @this.set('unidades_caja_producto', 0);
                                            @this.set('unidades_producto', 0);
                                            console.log('data');
                                            });">
                                            <select name="producto" id="select2-producto"
                                                wire:model="producto_seleccionado" style="width: 100% !important">
                                                <option value="{{ null }}">-- Selecciona un producto --
                                                </option>
                                                @foreach ($productos as $presup)
                                                    <option value="{{ $presup->id }}">{{ $presup->nombre }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                @if ($producto_seleccionado != null)
                                    <div class="row justify-content-center mt-1">
                                        <div class="col-md-3" style="text-align: center !important;">
                                            <label for="fechaVencimiento">Pallets</label>
                                        </div>
                                        <div class="col-md-3" style="text-align: center !important;">
                                            <label for="fechaVencimiento">Cajas</label>
                                        </div>
                                        <div class="col-md-3" style="text-align: center !important;">
                                            <label for="unidades">Uds.</label>
                                        </div>
                                        <div class="col-md-3" style="text-align: center !important;">
                                            <label for="unidades">&nbsp; </label>
                                        </div>
                                    </div>
                                    <div class="row justify-content-center mt-1">
                                        <div class="col-md-3">
                                            <input type="number" class="form-control" wire:model="unidades_pallet_producto" wire:change='updatePallet()'>
                                        </div>
                                        <div class="col-md-3">
                                            <input type="number" class="form-control" wire:model="unidades_caja_producto" wire:change='updateCaja()'>
                                        </div>
                                        <div class="col-md-3">
                                            <input type="number" class="form-control" wire:model="unidades_producto" wire:change='updateUnidad()'>
                                        </div>
                                        <div class="col-md-3" style="justify-content: start !important"
                                            style="display: flex;flex-direction: column;align-content: center;justify-content: center;align-items: center;">
                                            <button type="button" class="btn btn-primary w-100"
                                                wire:click.prevent="addProductos('{{ $producto_seleccionado }}')"
                                                data-dismiss="modal" aria-label="Close">+</a>
                                        </div>
                                    </div>
                                    <div class="row justify-content-center">
                                        <div class="col-11 mt-3">

                                            <input name="sinCargo" class="form-check-input" type="checkbox" id="sinCargo" wire:model="sinCargo">
                                            <label for="sinCargo" style="cursor:pointer"> Producto sin cargos.</label>
                                        </div>
                                    </div>
                                @endif
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                            </div>
                        </div>
                    </div>
                </div>
                <div wire:ignore.self class="modal fade" id="editProductModal" tabindex="-1" role="dialog">
                    <div class="modal-dialog"
                        style="min-width: 25vw !important; align-self: center !important; margin-top: 0 !important;">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">Editar Producto</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                @if ($productoEditar != null)
                                    <div class="row justify-content-center">
                                        <div class="col-md-12">
                                            <div class="card border border-dark border-1"
                                                    style="margin-bottom: 5px !important">
                                                <div class="card-body"
                                                        style="
                                                    display: flex;
                                                    flex-direction: column;
                                                    flex-wrap: wrap;
                                                    align-items: center;
                                                    justify-content: center;
                                                    ">
                                                    <h2 class="card-title mt-0 font-32"
                                                        style="text-align: center; margin-bottom: -0.25rem !important;">
                                                        {{ $this->getProductoNombre() }}</h2>
                                                    <img class="mx-auto" src="{{ $this->getProductoImg() }}"
                                                        style="max-width: 30%; text-align:center;"
                                                        alt="Card image cap">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                                <div class="row justify-content-center">
                                    <div class="col-md-10" style="text-align: center !important;">
                                        <label for="fechaVencimiento">Producto seleccionado</label>
                                    </div>
                                    <div class="col-md-10" wire:ignore>
                                        <div x-data="" x-init="$('#select2-producto').select2();
                                            $('#select2-producto').on('change', function(e) {
                                                var data = $('#select2-producto').select2('val');
                                                @this.set('producto_seleccionado', data);
                                                @this.set('unidades_pallet_producto', 0);
                                                @this.set('unidades_caja_producto', 0);
                                                @this.set('unidades_producto', 0);
                                                console.log('data');
                                            });">
                                            <input type="text" value="{{ $productoEditarNombre}}" class="form-control" disabled>
                                            
                                        </div>
                                    </div>
                                </div>
                                @if ($productoEditar != null)
                                    <div class="row justify-content-center mt-1">
                                        <div class="col-md-3" style="text-align: center !important;">
                                            <label for="fechaVencimiento">Pallets</label>
                                        </div>
                                        <div class="col-md-3" style="text-align: center !important;">
                                            <label for="fechaVencimiento">Cajas</label>
                                        </div>
                                        <div class="col-md-3" style="text-align: center !important;">
                                            <label for="unidades">Uds.</label>
                                        </div>
                                        <div class="col-md-3" style="text-align: center !important;">
                                            <label for="unidades">&nbsp; </label>
                                        </div>
                                    </div>
                                    <div class="row justify-content-center mt-1">
                                        <div class="col-md-3">
                                            <input type="number" class="form-control" wire:model="unidades_pallet_producto" wire:change='updatePallet()'>
                                        </div>
                                        <div class="col-md-3">
                                            <input type="number" class="form-control" wire:model="unidades_caja_producto" wire:change='updateCaja()'>
                                        </div>
                                        <div class="col-md-3">
                                            <input type="number" class="form-control" wire:model="unidades_producto" wire:change='updateUnidad()'>
                                        </div>
                                        <div class="col-md-3" style="justify-content: start !important"
                                            style="display: flex;flex-direction: column;align-content: center;justify-content: center;align-items: center;">
                                            <button type="button" class="btn btn-primary w-100"
                                                wire:click.prevent="editProductos('{{ $indexPedidoProductoEditar }}')"
                                                data-dismiss="modal" aria-label="Close">Editar</a>
                                        </div>
                                    </div>
                                    <div class="row justify-content-center">
                                        <div class="col-11 mt-3">

                                            <input name="sinCargo" class="form-check-input" type="checkbox" id="sinCargo" wire:model="sinCargo">
                                            <label for="sinCargo" style="cursor:pointer"> Producto sin cargos.</label>
                                        </div>
                                    </div>
                                @endif
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card m-b-30">
            <div class="card-body">
                <h5>Opciones</h5>
                <div class="row">
                    <div class="col-12">
                        <a  href="{{ route('pedidos.cartatransporte' , $identificador) }}"  class="w-100 btn btn-info mb-2"  id="carta">Carta transporte</a>
                    </div>
                    <div class="col-12">
                        <button class="w-100 btn btn-info mb-2"  id="imprimirPedido">Enviar por Email</button>
                    </div>
                    @if(Auth::user()->role != 3 && Auth::user()->role != 2)

                        <div class="col-12">
                            <button class="w-100 btn btn-primary mb-2" wire:click.prevent="alertaGuardar">Guardar
                                datos del
                                pedido</button>
                        </div>
                    @endif
                        
                    @if ($bloqueado)
                        @if ($this->getEstadoNombre() == 'Recibido' && $EsAdmin)
                            <div class="col-12">
                                <button class="w-100 btn btn-success mb-2" wire:click.prevent="alertaAceptar">Aceptar pedido</button>
                            </div>
                            <div class="col-12">
                                <button class="w-100 btn btn-warning mb-2" wire:click.prevent="alertaRechazar">Rechazar pedido</button>
                            </div>
                        @endif
                    @else
                        @if ($this->getEstadoNombre() == 'Recibido' && $mostrarElemento || $this->getEstadoNombre() == 'Recibido' && $EsAdmin)
                            <div class="col-12">
                                <button class="w-100 btn btn-success mb-2" wire:click.prevent="alertaAceptar">Aceptar pedido</button>
                            </div>
                            <div class="col-12">
                                <button class="w-100 btn btn-warning mb-2" wire:click.prevent="alertaRechazar">Rechazar pedido</button>
                            </div>
                        @endif
                    @endif
                    @if ($canEdit)
                        <div class="col-12">
                            <button class="w-100 btn btn-danger mb-2" id="alertaEliminar">Eliminar pedido</button>
                        </div>
                    @endif

                </div>
            </div>
        </div>
    </div>
        <style>
            fieldset.scheduler-border {
                border: 1px groove #ddd !important;
                padding: 0 1.4em 1.4em 1.4em !important;
                margin: 0 0 1.5em 0 !important;
                -webkit-box-shadow: 0px 0px 0px 0px #000;
                box-shadow: 0px 0px 0px 0px #000;
            }

            table {
                border: 1px black solid !important;
            }

            th {
                border-bottom: 1px black solid !important;
                border: 1px black solid !important;
                border-top: 1px black solid !important;
            }

            th.header {
                border-bottom: 1px black solid !important;
                border: 1px black solid !important;
                border-top: 2px black solid !important;
            }

            td.izquierda {
                border-left: 1px black solid !important;

            }

            td.derecha {
                border-right: 1px black solid !important;

            }

            
            td.suelo {}

            @media(max-width: 756px){
                .select2{
                    width: 100% !important;
                }
            }

        </style>
        <script>
            window.addEventListener('initializeMapKit', () => {
                fetch('/admin/service/jwt')
                    .then(response => response.json())
                    .then(data => {
                        mapkit.init({
                            authorizationCallback: function(done) {
                                done(data.token);
                            }
                        });
                        // Aquí puedes inicializar tu mapa u otras funcionalidades relacionadas
                    });
            });
        </script>
    
</div>
    @section('scripts')
        {{-- <script src="https://cdn.datatables.net/responsive/2.4.0/js/dataTables.responsive.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.2/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.3.4/js/dataTables.buttons.min.js"></script> --}}
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
        {{-- <script src="https://cdn.datatables.net/buttons/2.3.4/js/buttons.html5.min.js"></script> --}}
        {{-- <script src="https://cdn.datatables.net/buttons/2.3.4/js/buttons.print.min.js"></script> --}}
        <script>
            // In your Javascript (external .js resource or <script> tag)

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

            $("#imprimirPedido").on("click", () => {
                Swal.fire({
                    title: '¿Estás seguro?',
                    text: 'Pulsa el botón de confirmar para enviara el pedido al cliente. Esto es irreversible.',
                    icon: 'info',
                    showConfirmButton: true,
                    showCancelButton: true
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.livewire.emit('imprimirPedido');
                    }
                });
            });
            $("#alertaEliminar").on("click", () => {
            Swal.fire({
                title: '¿Estás seguro?',
                text: 'Pulsa el botón de confirmar para eliminar el pedido. Esto es irreversible.',
                icon: 'error',
                showConfirmButton: true,
                showCancelButton: true
            }).then((result) => {
                if (result.isConfirmed) {
                    window.livewire.emit('destroy');
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
                dateFormat: 'yy-mm-dd',
                firstDay: 1,
                isRTL: false,
                showMonthAfterYear: false,
                yearSuffix: ''
            };
            $.datepicker.setDefaults($.datepicker.regional['es']);
            // document.addEventListener('livewire:load', function() {


            // })
            document.addEventListener("livewire:load", () => {
                Livewire.hook('message.processed', (message, component) => {
                    $('.js-example-basic-single').select2();
                });

                // $('#id_cliente').on('change', function (e) {
                // console.log('change')
                // console.log( e.target.value)
                // // var data = $('.js-example-basic-single').select2("val");
                // })
            });
            $('#addProductModal').on('shown.bs.modal', function () {

                $('#select2-producto').select2({
                    dropdownParent: $('#addProductModal') // asegurando que el dropdown de Select2 se adjunte dentro del modal
                });
            });

            $('#editProductModal').on('shown.bs.modal', function () {
                $('#select2-producto').select2({
                    dropdownParent: $('#editProductModal') // asegurando que el dropdown de Select2 se adjunte dentro del modal
                });
            });



            $(document).ready(function() {
                $('.js-example-basic-single').select2();
                // $('.js-example-basic-single').on('change', function (e) {
                // console.log('change')
                // console.log( e.target.value)
                // var data = $('.js-example-basic-single').select2("val");

                // @this.set('foo', data);
                //     livewire.emit('selectedCompanyItem', e.target.value)
                // });
                // $('#tableServicios').DataTable({
                //     responsive: true,
                //     dom: 'Bfrtip',
                //     buttons: [
                //         'copy', 'csv', 'excel', 'pdf', 'print'
                //     ],
                //     buttons: [{
                //         extend: 'collection',
                //         text: 'Export',
                //         buttons: [{
                //                 extend: 'pdf',
                //                 className: 'btn-export'
                //             },
                //             {
                //                 extend: 'excel',
                //                 className: 'btn-export'
                //             }
                //         ],
                //         className: 'btn btn-info text-white'
                //     }],
                //     "language": {
                //         "lengthMenu": "Mostrando _MENU_ registros por página",
                //         "zeroRecords": "Nothing found - sorry",
                //         "info": "Mostrando página _PAGE_ of _PAGES_",
                //         "infoEmpty": "No hay registros disponibles",
                //         "infoFiltered": "(filtrado de _MAX_ total registros)",
                //         "search": "Buscar:",
                //         "paginate": {
                //             "first": "Primero",
                //             "last": "Ultimo",
                //             "next": "Siguiente",
                //             "previous": "Anterior"
                //         },
                //         "zeroRecords": "No se encontraron registros coincidentes",
                //     }

            });



            // $("#fechaEmision").datepicker();


            // $("#fechaEmision").on('change', function(e) {
            //     @this.set('fechaEmision', $('#fechaEmision').val());
            // });



            function togglePasswordVisibility() {
                var passwordInput = document.getElementById("password");
                var eyeIcon = document.getElementById("eye-icon");
                if (passwordInput.type === "password") {
                    passwordInput.type = "text";
                    eyeIcon.className = "fas fa-eye-slash";
                } else {
                    passwordInput.type = "password";
                    eyeIcon.className = "fas fa-eye";
                }
            }
            //observer para aplicar el datepicker de evento
            // const observer = new MutationObserver((mutations, observer) => {
            //     console.log(mutations, observer);
            // });
            // observer.observe(document, {
            //     subtree: true,
            //     attributes: true
            // });



            document.addEventListener('DOMSubtreeModified', (e) => {
                $("#diaEvento").datepicker();

                // $("#diaEvento").on('focus', function(e) {
                //     document.getElementById("guardar-evento").style.visibility = "hidden";
                // })
                // $("#diaEvento").on('focusout', function(e) {
                //     if ($('#diaEvento').val() != "") {
                //         document.getElementById("guardar-evento").style.visibility = "visible";
                //     }

                // })
                // $("#diaFinal").on('focus', function(e) {
                //     document.getElementById("guardar-evento").style.visibility = "hidden";
                // })
                // $("#diaFinal").on('focusout', function(e) {
                //     if ($('#diaFinal').val() != "") {
                //         document.getElementById("guardar-evento").style.visibility = "visible";
                //     }

                // })

                $("#diaFinal").datepicker();

                $("#diaFinal").on('change', function(e) {
                    @this.set('diaFinal', $('#diaFinal').val());

                });

                $("#diaEvento").on('change', function(e) {
                    @this.set('diaEvento', $('#diaEvento').val());
                    @this.set('diaFinal', $('#diaEvento').val());

                });

                $('#id_cliente').on('change', function(e) {
                    console.log('change')
                    console.log(e.target.value)
                    var data = $('#id_cliente').select2("val");
                    @this.set('id_cliente', data);
                    Livewire.emit('selectCliente')

                    // livewire.emit('selectedCompanyItem', data)
                })
            })

            function OpenSecondPage() {
                var id = @this.id_cliente
                window.open(`/admin/clientes-edit/` + id, '_blank'); // default page
            };
        </script>
    @endsection
