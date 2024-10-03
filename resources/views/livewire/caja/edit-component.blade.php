@section('head')
    @vite(['resources/sass/productos.scss'])
@endsection
@php
$EsAdmin = Auth::user()->isAdmin();
$canEdit = $EsAdmin || Auth::user()->role = 7 || Auth::user()->role = 6     //|| $estado == 1;
@endphp
<div class="container-fluid">
    <style>
        textarea{
            width: 100%;
        }
    </style>
    <div class="page-title-box">
        <div class="row align-items-center">
            <div class="col-sm-6">
                <h4 class="page-title">MOVIMIENTOS</h4>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-right">
                    <li class="breadcrumb-item"><a href="javascript:void(0);">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="javascript:void(0);">Movimientos</a></li>
                    <li class="breadcrumb-item active">Editar Movimiento</li>
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
                        <div class="mb-3 row d-flex align-items-center ">
                                <div class="col-sm-4">
                                    <label for="Proveedor" class="col-sm-12 col-form-label">Asiento Contable</label>
                                        <input class="form-control" type="text" value="" wire:model="asientoContable" > 
                                </div>
                                @if($this->tipo_movimiento == "Ingreso")
                                    <div class="col-sm-2">
                                        <label for="Proveedor" class="col-sm-12 col-form-label">Ingreso Proveedor</label>
                                        <select name="" id="" class="form-select" wire:model="isIngresoProveedor" disabled>
                                            <option value="0">No</option>
                                            <option value="1">Sí</option>
                                        </select>
                                    </div>
                                @endif
                                    
                            </div>
                        @if ($this->tipo_movimiento == "Ingreso")
                            <div class="mb-3 row d-flex align-items-center" @if($isIngresoProveedor) style="display: none !important;" @endif>
                                <label for="nombre" class="col-sm-12 col-form-label">Factura</label>
                                <div class="col-sm-10">
                                    <div class="col-md-12" x-data="" x-init="$('#select2-monitor').select2();
                                        $('#select2-monitor').on('change', function(e) {
                                        var data = $('#select2-monitor').select2('val');
                                        @this.set('pedido_id', data);
                                        });" wire:key='rand()'>
                                        <select class="form-control" name="pedido_id" id="select2-monitor"
                                            wire:model.lazy="pedido_id" @if(!$canEdit) disabled @endif>
                                            <option value="0">-- ELIGE UNA FACTURA --</option>
                                            @foreach ($facturas as $factura)
                                                <option value="{{ $factura->id }}">
                                                    ({{ $factura->numero_factura }}) - {{ $this->getCliente($factura->cliente_id) }}  @if(!$this->facturaHasIva($factura->id)) * @endif
                                                </option>
                                            @endforeach
                                        </select>
                                    </div> @error('nombre')
                                    <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="mb-3 row d-flex align-items-center col-sm-6" @if(!$isIngresoProveedor) style="display: none !important;" @endif>
                                <label for="nombre" class="col-sm-12 col-form-label">Gasto asociado</label>
                                <div class="col-sm-10" wire:ignore x-data x-init="
                                    $nextTick(() => {
                                        let selectedGasto = {{ $gasto_id ?? 'null' }};
                                        
                                        $('#select2-gastos').select2({
                                            ajax: {
                                                url: '{{ route('buscarGastos') }}', // Ruta para obtener los datos
                                                dataType: 'json',
                                                delay: 250,
                                                data: function(params) {
                                                    return {
                                                        search: params.term,
                                                        page: params.page || 1
                                                    };
                                                },
                                                processResults: function(data, params) {
                                                    params.page = params.page || 1;
                                                    return {
                                                        results: $.map(data.data, function(item) {
                                                            return { id: item.id, text: item.nFactura };
                                                        }),
                                                        pagination: {
                                                            more: data.more
                                                        }
                                                    };
                                                },
                                                cache: true
                                            },
                                            placeholder: '-- ELIGE UN GASTO --',
                                            minimumInputLength: 1,
                                            allowClear: true
                                        });

                                        // Cargar el gasto ya seleccionado
                                        if (selectedGasto !== 'null') {
                                            // Crear una opción temporal con el gasto seleccionado
                                            let option = new Option('Cargando...', selectedGasto, true, true);
                                            $('#select2-gastos').append(option).trigger('change');

                                            // Realizar una petición AJAX para obtener los detalles del gasto seleccionado
                                            $.ajax({
                                                url: '{{ route('buscarGastos') }}', // Debe permitir buscar por id
                                                data: { id: selectedGasto }, // Pasamos el id del gasto seleccionado
                                                success: function(data) {
                                                    // Reemplazamos la opción temporal por los datos reales
                                                    let option = new Option(data.nFactura, data.id, true, true);
                                                    $('#select2-gastos').append(option).trigger('change');
                                                },
                                                error: function() {
                                                    // Si no se encuentra el gasto, podemos dejar la opción temporal o manejar el error
                                                    let option = new Option('Gasto no encontrado', selectedGasto, true, true);
                                                    $('#select2-gastos').append(option).trigger('change');
                                                }
                                            });
                                        }

                                        // Evento para manejar los cambios en Select2
                                        $('#select2-gastos').on('change', function() {
                                            var data = $(this).val();
                                            @this.set('gasto_id', data); // Actualizar el gasto en Livewire
                                        });
                                    });
                                ">
                                    <select class="form-control" style="width:100%;" name="gasto_id" id="select2-gastos">
                                        {{-- <option value="0">-- ELIGE UN GASTO --</option>
                                        @if($gastos)
                                            @foreach ($gastos as $gasto)
                                                <option value="{{ $gasto->id }}">
                                                    {{$gasto->nFactura}}
                                                </option>
                                            @endforeach
                                        @endif --}}
                                    </select>
                                </div>
                            
                            </div>
                            <div class="col-sm-12 row">
                                <div class="mb-3 row d-flex align-items-center col-sm-3">
                                    <label for="nombre" class="col-sm-12 col-form-label">Importe</label>
                                    <div class="col-sm-10">
                                        <input type="number" class="form-control" wire:model="importe" nombre="importe"
                                            id="importe" placeholder="Importe..." @if(!$canEdit) disabled @endif>
                                        @error('nombre')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="mb-3 row d-flex align-items-center col-sm-3">
                                    <label for="nombre" class="col-sm-12 col-form-label">Fecha</label>
                                    <div class="col-sm-10">
                                        <input type="date" class="form-control" wire:model="fecha" nombre="fecha"
                                            id="fecha" placeholder="dd/mm/aaaa" @if(!$canEdit) disabled @endif>
                                        @error('nombre')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="mb-3 row d-flex align-items-center col-sm-3">
                                    <label for="pago" class="col-sm-12 col-form-label">Método de pago</label>
                                    <div class="col-sm-10" wire:ignore.self>
                                        @if ($this->tipo_movimiento == "Ingreso")
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
                                        @elseif($this->tipo_movimiento == "Gasto")
                                        <input type="text" class="form-control" wire:model="metodo_pago" nombre="metodo_pago"
                                        id="metodo_pago" placeholder="Nombre de la categoría..." @if(!$canEdit) disabled @endif>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            
                            
                        @elseif($this->tipo_movimiento == "Gasto")
                            <div class="mb-3 row d-flex align-items-center ">
                                <div class="col-sm-4">
                                    <label for="Proveedor" class="col-sm-12 col-form-label">Proveedor</label>
                                    <div class="col-md-12" x-data="" x-init="$('#select2-monitor').select2();
                                    $('#select2-monitor').on('change', function(e) {
                                        var data = $('#select2-monitor').select2('val');
                                        @this.set('poveedor_id', data);
                                    });" wire:key='rand()'>
                                        <select class="form-control" name="poveedor_id" id="select2-monitor"
                                            wire:model.lazy="poveedor_id" @if(!$canEdit) disabled @endif>
                                            <option value="0">-- ELIGE UN PROVEEDOR
                                                --
                                            </option>
                                            @foreach ($poveedores as $poveedor)
                                                <option value="{{ $poveedor->id }}">
                                                    {{ $poveedor->nombre }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                    <div class="col-sm-4">
                                        <label for="importe" class="col-sm-12 col-form-label">Departamento</label>
                                        <select class="form-control" name="departamento" id="departamento" wire:model="departamento" @if(!$canEdit) disabled @endif>
                                            <option value="0">-- ELIGE UN DEPARTAMENTO --</option>
                                            <option value="administracion">Administración</option>
                                            <option value="rrhh">RRHH</option>
                                            <option value="marketing">Marketing</option>
                                            <option value="comercial">Comercial</option>
                                            <option value="produccion">Producción</option>
                                            <option value="patrocinios">Patrocinios</option>
                                            <option value="exportacion">Exportación</option>
                                            <option value="logistica">Logística</option>
                                        </select>
                                    </div>
                                    <div class="col-sm-4">
                                        <label for="importe" class="col-sm-12 col-form-label">Delegación</label>
                                        <select class="form-control" name="delegacion_id" id="delegacion_id" wire:model="delegacion_id" @if(!$canEdit) disabled @endif>
                                            <option value="0">-- ELIGE UNA DELEGACIÓN --</option>
                                            @foreach ($delegaciones as $delegacion)
                                                <option value="{{ $delegacion->id }}">
                                                    {{ $delegacion->nombre }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                            </div>
                            {{-- <div class="mb-3 row d-flex align-items-center ">
                                <div class="col-sm-4">
                                    <label for="Proveedor" class="col-sm-12 col-form-label">Asiento Contable</label>
                                        <input class="form-control" type="text" value="" wire:model="asientoContable" > 
                                </div>
                                    
                                    
                            </div> --}}

                            {{-- <div class="mb-3 row d-flex align-items-center">
                                <label for="estado" class="col-sm-12 col-form-label">Estado</label>
                                <div class="col-sm-10">
                                    <select class="form-control" name="estado" id="estado"  wire:model.lazy="estado">
                                        <option value="Pendiente">Pendiente</option>
                                        <option value="Pagado">Pagado</option>
                                        <option value="Vencido">Vencido</option>
                                    </select>
                                </div>
                            </div> --}}
                            

                            <div class="mb-3 row d-flex align-items-center justify-content-center">
                                <div class="col-sm-3">
                                    <label for="importe" class="col-sm-12 col-form-label">Importe Neto</label>
                                    <input type="number"  step="0.1" class="form-control" wire:model="importe" nombre="importe"
                                        id="importe" placeholder="Importe" wire:change="calcularTotal()"  @if(!$canEdit) disabled @endif>
                                </div>
                                
                                <div class="col-sm-3">
                                    <label for="importe" class="col-sm-12 col-form-label">% Iva</label>
                                    <input type="number"  step="0.1" class="form-control" wire:model="iva" nombre="iva"
                                        id="iva" placeholder="iva" wire:change="calcularTotal()" @if(!$canEdit) disabled @endif>
                                </div>
                                <div class="col-sm-3">
                                    <label for="importe" class="col-sm-12 col-form-label">Importe Iva</label>
                                    <input type="number"  step="0.1" class="form-control" wire:model="importeIva" nombre="importeIva"
                                        id="importeIva" placeholder="Importe Iva" wire:change="calcularTotal()" @if(!$canEdit) disabled @endif>
                                </div>
                                
                                <div class="col-sm-3">
                                    <label for="importe" class="col-sm-12 col-form-label">Retención</label>
                                    <input type="number"  step="0.1" class="form-control" wire:model="retencion" nombre="retencion"
                                        id="retencion" placeholder="retencion" wire:change="calcularTotal()" @if(!$canEdit) disabled @endif>
                                </div>
                                
                            </div>
                            <div class="mb-3 row d-flex align-items-center justify-content-center">
                                <div class="col-sm-3">
                                    <label for="fecha" class="col-sm-12 col-form-label">Fecha vencimiento</label>
                                    <input type="date" class="form-control" wire:model="fecha_vencimiento" nombre="fecha_vencimiento"
                                        id="fecha_vencimiento" placeholder="fecha_vencimiento..." @if(!$canEdit) disabled @endif>
                                </div>
                                <div class="col-sm-3">
                                    <label for="fecha" class="col-sm-12 col-form-label">Fecha de pago</label>
                                    <input type="date" class="form-control" wire:model="fecha_pago" nombre="fecha_pago"
                                    id="fecha_pago" placeholder="fecha_pago..." @if(!$canEdit) disabled @endif>
                                </div>
                                <div class="col-sm-3">
                                    <label for="fecha" class="col-sm-12 col-form-label">Fecha</label>
                                    <input type="date" class="form-control" wire:model="fecha" nombre="fecha"
                                        id="fecha" placeholder="Nombre de la categoría..." @if(!$canEdit) disabled @endif>
                                </div>
                                <div class="col-sm-3">
                                    <label for="pago" class="col-sm-12 col-form-label">Método de pago</label>
                                    <input type="text" class="form-control" wire:model="metodo_pago" nombre="metodo_pago"
                                        id="metodo_pago" placeholder="Método de pago..." @if(!$canEdit) disabled @endif>
                                </div>

                            </div>
                            <div class="mb-3 row d-flex align-items-center ">
                                <div class="col-sm-3">
                                    <label for="importe" class="col-sm-12 col-form-label">Descuento</label>
                                    <input type="number"  step="0.1" class="form-control" wire:model="descuento" nombre="descuento"
                                        id="descuento" placeholder="descuento" wire:change="calcularTotal()" @if(!$canEdit) disabled @endif>
                                </div>
                                <div class="col-sm-3">
                                    <label for="importe" class="col-sm-12 col-form-label">Total</label>
                                    <input type="number"  step="0.1" class="form-control" wire:model="total" nombre="total"
                                        id="total" placeholder="total" @if(!$canEdit) disabled @endif>
                                </div>
                                <div class="col-sm-3">
                                    <label for="importe" class="col-sm-12 col-form-label">Pagado</label>
                                    <input type="number"  step="0.1" class="form-control" wire:model="pagado" nombre="pagado"
                                        id="pagado" placeholder="pagado">
                                </div>
                                <div class="col-sm-3">
                                    <label for="importe" class="col-sm-12 col-form-label">Pendiente</label>
                                    <input type="number"  step="0.1" class="form-control" wire:model="pendiente" nombre="pendiente"
                                        id="pendiente" placeholder="pendiente" disabled>
                                </div>
                                
                            </div>
                            
                            <div class="mb-3 row d-flex align-items-center ">

                                <div class="col-sm-3">
                                    <label for="nInterno" class="col-sm-12 col-form-label">Número Interno</label>
                                    <input type="text"  class="form-control" wire:model="nInterno" nombre="nInterno"
                                        id="descuento" placeholder="Número interno" >
                                </div>
                                <div class="col-sm-3">
                                    <label for="nFactura" class="col-sm-12 col-form-label">Número factura</label>
                                    <input type="text" class="form-control" wire:model="nFactura" nombre="nFactura"
                                        id="total" placeholder="Número de factura">
                                </div>
                                <div class="col-sm-3">
                                    <label for="pago" class="col-sm-12 col-form-label">Número Cuenta</label>
                                    <div class="col-sm-10">
                                        <input type="text" class="form-control" wire:model="cuenta" nombre="cuenta"
                                            id="cuenta" placeholder="Cuenta..." @if(!$canEdit) disabled @endif>
                                    </div>
                                </div>
                                <div class="col-sm-3">
                                    <label for="pago" class="col-sm-12 col-form-label">
                                    <input  type="checkbox"  wire:model="compensacion" nombre="cuenta"
                                        id="cuenta" placeholder="Cuenta..." >
                                        ¿Compensar factura?</label>
                                </div>
                                @if($compensacion)
                                    <div class="mb-3 row d-flex align-items-center">
                                        <!-- Botón para abrir el modal -->
                                        <div class="col-sm-3">
                                            <button type="button" class="btn btn-primary mt-2" data-toggle="modal" data-target="#facturasModal">
                                                Editar Facturas Compensadas
                                            </button>
                                        </div>
                                    </div>
                            
                                    <!-- Modal -->
                                    <div class="modal fade" id="facturasModal" tabindex="-1" role="dialog" aria-labelledby="facturasModalLabel" aria-hidden="true" wire:ignore.self>
                                        <div class="modal-dialog" role="document">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="facturasModalLabel">Seleccionar Facturas Compensadas</h5>
                                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                        <span aria-hidden="true">&times;</span>
                                                    </button>
                                                </div>
                                                <div class="modal-body">
                                                    <!-- Seleccionar facturas -->
                                                    <div class="form-group">
                                                        <label for="facturasCompensadas">Facturas</label>
                                                        <select class="form-control" id="facturasCompensadas" wire:model="facturasSeleccionadas" style="width: 100%" multiple>
                                                            @foreach($facturas as $factura)
                                                            <option value="{{ $factura->id }}">
                                                                <span style="font-weight: bold;">{{ $factura->numero_factura }}</span> 
                                                                &nbsp;|&nbsp; 
                                                                <span style="color: gray;">{{ $factura->cliente->nombre }}</span> 
                                                                &nbsp;|&nbsp; 
                                                                <span style="color: blue;">{{ $factura->total }}€</span>
                                                            </option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                
                                                    <!-- Inputs de pago por factura seleccionada -->
                                                    @foreach($facturasSeleccionadas as $index => $factura_id)
                                                        @php
                                                            $factura = $facturas->find($factura_id);
                                                        @endphp
                                                        <div class="form-group">
                                                            <label for="pagadoFactura">
                                                                Factura: {{ $factura->numero_factura }} - Total: {{ $factura->total }} €
                                                            </label>
                                                            <input type="number" class="form-control" wire:model="pagos.{{ $index }}" placeholder="Importe pagado para esta factura" value="{{ $factura->total }}">
                                                        </div>
                                                    @endforeach
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                                                    <button type="button" class="btn btn-primary" wire:click="guardarFacturasCompensadas">Guardar cambios</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                                
                            </div>
                            
                        @endif
                        
                        
                        <div class="mb-3 row d-flex align-items-center">
                            <label for="nombre" class="col-sm-12 col-form-label">Descripción</label>
                            <div class="col-sm-10">
                                <textarea class="form-control" wire:model="descripcion" nombre="descripcion" id="descripcion" placeholder="Descripción" rows="4" cols="150" @if(!$canEdit) disabled @endif></textarea>
                                @error('nombre')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        @if($canEdit)
                            <div >
                                <label for="documento" class="col-sm-12 col-form-label">Documento Adjunto</label>
                                <input type="file" class="btn btn-info text-dark" wire:model="documentoSubido" >
                            
                                @error('documento') <span class="error">{{ $message }}</span> @enderror
                            
                            </div>
                        @endif
                    </form>

                    <div>
                        @if(count($facturas_compensadas) > 0 )
                            <h5>Facturas compensadas</h5>
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Gasto</th>
                                        <th>Factura</th>
                                        <th>Importe</th>
                                        <th>Compensado</th>
                                        <th>Fecha</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($facturas_compensadas as $factura)
                                        <tr>
                                            <td><a class="badge badge-info" href="{{ route('caja.edit',  $factura->caja_id) }}"> {{ $factura->caja_id }}</a></td>
                                            <td><a class="badge badge-info" href="{{ route('facturas.edit',  $factura->factura_id) }}">{{ $this->getFacturaNumber($factura->factura_id) }}</a></td>
                                            <td>{{ $factura->importe }}€</td>
                                            <td>{{ $factura->pagado }}€</td>
                                            <td>{{ $factura->fecha }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        @endif
                    </div>
                </div>
            </div>
        </div>
       
            <div class="col-md-3">
                <div class="card m-b-30">
                    @if($canEdit)
                        <div class="card-body">
                        
                            <h5>Acciones</h5>
                            <div class="row">
                                <div class="col-12">
                                    <button class="w-100 btn btn-success mb-2" id="alertaGuardar">Editar Movimiento </button>
                                </div>
                                <div class="col-12">
                                    <button class="w-100 btn btn-danger mb-2" wire:click="destroy">Eliminar Movimiento </button>
                                </div>
                            </div>
                        
                        </div>
                        @if($this->tipo_movimiento == "Gasto")
                            <div class="card-body">
                                <h5>Estado</h5>
                                <div class="row">
                                    <div class="col-12">
                                        <select class="form-control" name="estado" id="estado"  wire:model.lazy="estado">
                                            <option value="Pendiente">Pendiente</option>
                                            <option value="Pagado">Pagado</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        @endif
                    @endif
                    @if($documento)
                        <div class="card-body">
                            <h5>Descarga</h5>
                            <div class="row">
                                <div class="col-12">
                                    <button class="w-100 btn btn-success mb-2" wire:click="descargarDocumento">Documento</button>
                                </div>
                            </div>
                        </div>
                    @endif        
                </div>
            </div>
    </div>
</div>

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
  document.addEventListener('livewire:load', function () {
    function initSelect2() {
        // Inicializar Select2
        $('#facturasCompensadas').select2({
            placeholder: 'Selecciona Facturas', // Texto de búsqueda inicial
            allowClear: true // Opción para permitir limpiar la selección
        });

        // Capturar el evento de cambio en Select2 y sincronizar con Livewire
        $('#facturasCompensadas').on('change', function (e) {
            console.log('cambio'); // Comprobar que el evento se dispara
            var data = $(this).val();
            console.log(data); // Mostrar los valores seleccionados en la consola
            @this.set('facturasSeleccionadas', data); // Sincronizar con Livewire
        });
    }

    // Inicializar Select2 cuando se cargue la página
    initSelect2();

    // Reinicializar Select2 cada vez que Livewire actualice el componente
    Livewire.hook('message.processed', (message, component) => {
        initSelect2();
    });
});
</script>
<script>
    $("#alertaGuardar").on("click", () => {
        Swal.fire({
            title: '¿Estás seguro?',
            text: 'Pulsa el botón de confirmar para guardar el tipo de evento.',
            icon: 'warning',
            showConfirmButton: true,
            showCancelButton: true
        }).then((result) => {
            if (result.isConfirmed) {
                window.livewire.emit('update');
            }
        });
    });
</script>
@endsection
