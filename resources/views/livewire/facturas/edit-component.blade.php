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
                    <input type="number" wire:model="cantidadServicio" class="form-control mb-1" placeholder="Cantidad">
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
    <div wire:ignore.self class="modal fade" id="enviarEmailModal" tabindex="-1" role="dialog">
        <div class="modal-dialog"
            style="min-width: 25vw !important; align-self: center !important; margin-top: 0 !important;">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Enviar emails</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <label for="nombreServicio">Selecciona emails</label>
                    <br>
                    @if(count($emails) > 0)
                       @foreach ($emails as $email)
                           <input type="checkbox" wire:model="emailsSeleccionados" value="{{ $email->email }}"> {{ $email->email }} <br>
                       @endforeach
                    @else
                        <input type="checkbox" wire:model="emailsSeleccionados" value="{{ $cliente->email }}"> {{ $cliente->email }} <br>
                    @endif
                    <br>
                    <input type="text" class="my-2" wire:model="emailNuevo" placeholder="Email adicional">
                    <br>
                    <textarea wire:model="anotacionesEmail" class="form-control mb-1" placeholder="Anotaciones"> </textarea>
                    <br>
                    @if($cliente->delegacion)
                        @if($cliente->delegacion)
                            @if($cliente->delegacion )
                                @if($cliente->delegacion->nombre !== '07 CANARIAS' && $cliente->delegacion->nombre !== '13 GIBRALTAR' && $cliente->delegacion->nombre !== '14 CEUTA' && $cliente->delegacion->nombre !== '15 MELILLA')
                                    <button class="btn btn-success" id="EmailFacturarIva">Enviar con Iva</button>
                                @endif
                            @endif
                        @endif
                    @else
                            <button class="btn btn-success" id="EmailFacturarIva">Enviar con Iva</button>

                    @endif
                    <button class="btn btn-success" id="EmailFacturar">Enviar sin Iva</button>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>
    <div wire:ignore.self class="modal fade" id="transporteModal" tabindex="-1" role="dialog">
        <div class="modal-dialog"
            style="min-width: 25vw !important; align-self: center !important; margin-top: 0 !important;">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Enviar email Transportista</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <label for="nombreServicio">Email</label>
                    <input type="text" class="my-2" wire:model="emailTransporte" placeholder="Email Transportista">
                    <br>
                    <select wire:model="destinosValue">
                        <option value="0">Selecciona un destino</option>
                        @foreach ($destinos as $key =>  $destino)
                            <option value="{{ $key }}">{{ $destino }}</option>
                        @endforeach
                    </select>
                    <br>
                    @if($destinosValue == 4)
                    <br>
                    <div class="d-flex  align-items-center">
                        <label for="nombreServicio">Otro Destino: &nbsp;</label>
                        <textarea type="text" class="my-2" wire:model="otroDestino" placeholder="Introduzca Destino"></textarea>
                    </div>
                    @endif
                    <br>
                    <div class="d-flex  align-items-center">
                        <label for="nombreServicio">Observaciones: &nbsp;</label> 
                        <textarea type="text" class="my-2" wire:model="observacionesEmail" placeholder="Observaciones"></textarea>
                    </div>
                    
                    <br>
                    <button class="btn btn-success" id="enviarTransporte">Enviar Email</button>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
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

                            @if($tipo != 2)
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
                            @endif

                            <div class="col-md-4">
                                <label for="Cliente" class="col-sm-12 col-form-label">Cliente</label>
                                <div class="col-sm-12">
                                    @if(isset($this->pedido_id))
                                        <select class="form-control" name="cliente_id" id="cliente_id"
                                            wire:model="cliente_id" >
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
                                            placeholder="15/02/2023" @if(!$canEdit || $tipo == 2) disabled @endif>
                                        @error('fecha_emision')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                            
                            @if($tipo != 2)
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
                            @endif
                        </div>
                        <div class="form-group row">
                            {{-- @if($tipo != 2 && $tipo != 3)
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
                            @endif --}}
                             
                            @if($tipo != 2)
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
                                <div class="col-md-4">
                                    <label for="fecha_emision" class="col-sm-12 col-form-label">Recargo</label>
                                    <div class="col-sm-12">
                                        
                                        <input type="number" wire:model="recargo" class="form-control"
                                            placeholder="recargo" >

                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <label for="fecha_emision" class="col-sm-12 col-form-label">Total recargo</label>
                                    <div class="col-sm-12">
                                        
                                        <input type="number" wire:model="total_recargo" class="form-control"
                                            placeholder="total recargo" disabled>

                                    </div>
                                </div>

                                    <div class="col-md-4">
                                        <label for="fecha_emision" class="col-sm-12 col-form-label">Recargo vencimiento</label>
                                        <div class="col-sm-12">
                                           <select name="retencion" id="retencion" class="form-control" wire:model="retencion_id" >
                                            <option value="">Selecciona un recargo vencimiento</option>
                                            @foreach($retenciones as $ret)
                                                <option value="{{$ret->id}}">{{$ret->nombre}}</option>
                                            @endforeach
                                           </select>
                                        </div>
                                    </div>

                                    @if($retencion_id)

                                    <div class="col-md-4">
                                        <label for="total_original" class="col-sm-12 col-form-label">Total original</label>
                                        <div class="col-sm-12">
                                            <input type="number" wire:model="total_original" class="form-control"
                                                placeholder="total retencion" disabled>
                                        </div>
                                    </div>


                                    <div class="col-md-4">
                                        <label for="total_retencion" class="col-sm-12 col-form-label">Total Recargo</label>
                                        <div class="col-sm-12">
                                            <input type="number" wire:model="total_retencion" class="form-control"
                                                placeholder="total retencion" disabled>
                                        </div>
                                    </div>

                                @endif


                            @endif
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
                            @if($tipo != 2)
                            <div class="col-md-12">
                                <label for="descripcion" class="col-sm-12 col-form-label">Descripción </label>
                                <div class="col-sm-12">
                                    <textarea wire:model="descripcion" class="form-control" name="descripcion" id="descripcion"
                                        placeholder="Factura para el cliente ..." @if(!$canEdit) disabled @endif></textarea>
                                    @error('descripcion')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            @endif
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
                    </form>
                    @if($pedido == null)
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
                                            <th>Total</th>
                                            <th>Eliminar</th>
                                        </tr>
                                        @php($total = 0)
                                        @foreach ($servicios as $index =>  $servicio)
                                            @if(!isset($servicio['delete']))
                                                @php($total += $servicio['cantidad'] * $servicio['importe'])
                                                <tr>
                                                    <td>{{ $servicio['descripcion'] }}</td>
                                                    <td>{{ $servicio['cantidad'] }}</td>
                                                    <td>{{ $servicio['importe'] }}</td>
                                                    <td>{{  $servicio['cantidad'] * $servicio['importe'] }} €</td>
                                                    <td><button type="button" class="btn btn-danger" wire:click="deleteServicio({{ $index }})">X</button></td>
                                                </tr>
                                            @endif
                                        @endforeach

                                    </table>
                                    <h3 class="ms-3">Total: {{ $total }} €</h3>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
            @if($EsAdmin && count($registroEmails) > 0)
                <div class="card m-b-30">
                    <div class="card-body">
                        <h5>Correos electrónicos enviados</h5>
                        <div class="row">
                            <div class="col-12">
                                <table class="table ms-3 table-striped table-bordered dt-responsive nowrap">
                                    <thead>
                                        <tr>
                                            <th>Correo</th>
                                            <th>Cliente</th>
                                            <th>Usuario</th>
                                            <th>Fecha</th>
                                            <th>Tipo</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($registroEmails as $index => $email)
                                            <tr>
                                                <td>{{ $email->email }}</td>
                                                <td>{{ $this->getCliente( $email->cliente_id) }}</td>
                                                <td>{{ $this->getUser($email->user_id) }}</td>
                                                <td>{{ $email->updated_at }}</td>
                                                <td>{{ $this->getTipo($email->tipo_id) }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

            

            @endif
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
                            @if($tipo == 2)
                                <button class="w-100 btn btn-dark mb-2" data-toggle="modal" data-target="#transporteModal" >Enviar email a transportista</button>

                            @endif
                            {{-- <button class="w-100 btn btn-info mb-2" id="EmailFacturarIva">Enviar factura por correo Con IVA</button>
                            <button class="w-100 btn btn-info mb-2" id="EmailFacturar">Enviar factura por correo Sin IVA</button> --}}
                            <button class="w-100 btn btn-info mb-2" data-toggle="modal" data-target="#enviarEmailModal" >Enviar emails</button>
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

        $("#enviarTransporte").on("click", () => {
            Swal.fire({
                title: '¿Estás seguro?',
                icon: 'info',
                showConfirmButton: true,
                showCancelButton: true
            }).then((result) => {
                if (result.isConfirmed) {
                    window.livewire.emit('enviarTransporte');
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
