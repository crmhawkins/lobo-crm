<div class="container-fluid">
    <style>
        textarea{
            width: 100%;
        }
    </style>
    <div class="page-title-box">
        <div class="row align-items-center">
            <div class="col-sm-6">
                <h4 class="page-title">AÑADIR MOVIMIENTO DE CAJA (INGRESO)</span></h4>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-right">
                    <li class="breadcrumb-item"><a href="javascript:void(0);">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="javascript:void(0);">Caja</a></li>
                    <li class="breadcrumb-item active">Añadir movimiento de ingreso</li>
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
                                
                                {{-- <div class="col-sm-4">
                                    <label for="importe" class="col-sm-12 col-form-label">Cuenta Contable</label>
                                    <div class="col-md-12" x-data="" x-init="
                                            $('#select2-cuenta-contable').select2();
                                            $('#select2-cuenta-contable').on('change', function(e) {
                                                var data = $('#select2-cuenta-contable').select2('val');
                                                @this.set('cuentaContable_id', data);
                                            });
                                            Livewire.hook('message.processed', (message, component) => {
                                                $('#select2-cuenta-contable').select2(); // Reinicializa Select2 cuando Livewire renderiza
                                            });
                                        " wire:key='rand()'>
                                        <select class="form-control select2" id="select2-cuenta-contable" wire:model.lazy="cuentaContable_id">                                                <option value="">-- Seleccione Cuenta Contable --</option>
                                            @foreach($cuentasContables as $grupo)
                                                <option disabled value="">- {{ $grupo['grupo']['numero'] .'. '. $grupo['grupo']['nombre'] }} -</option>
                                                @foreach($grupo['subGrupo'] as $subGrupo)
                                                    <option disabled value="">-- {{ $subGrupo['item']['numero'] .'. '. $subGrupo['item']['nombre'] }} --</option>
                                                    @foreach($subGrupo['cuentas'] as $cuenta)
                                                        <option value="{{ $cuenta['item']['numero'] }}">--- {{ $cuenta['item']['numero'] .'. '. $cuenta['item']['nombre'] }} ---</option>
                                                        @foreach($cuenta['subCuentas'] as $subCuenta)
                                                            <option value="{{ $subCuenta['item']['numero'] }}">---- {{ $subCuenta['item']['numero'] .'. '. $subCuenta['item']['nombre'] }} ----</option>
                                                            @foreach($subCuenta['subCuentasHija'] as $subCuentaHija)
                                                                <option value="{{ $subCuentaHija['numero'] }}">----- {{ $subCuentaHija['numero'] .'. '. $subCuentaHija['nombre'] }} -----</option>
                                                            @endforeach
                                                        @endforeach
                                                    @endforeach
                                                @endforeach
                                            @endforeach
                                        </select>
                                    </div>
                                </div> --}}
                                <div class="col-sm-4">
                                    <label for="Proveedor" class="col-sm-12 col-form-label">Asiento Contable</label>
                                        <input class="form-control" type="text" value="" wire:model="asientoContable" > 
                                </div>

                                <div class="col-sm-2">
                                    <label for="Proveedor" class="col-sm-12 col-form-label">Ingreso Proveedor</label>
                                    <select name="" id="" class="form-select" wire:model="isIngresoProveedor">
                                        <option value="0">No</option>
                                        <option value="1">Sí</option>
                                    </select>
                                </div>
                                    
                            </div>

                        <div class="mb-3 row d-flex align-items-center">
                                <div style="@if($isIngresoProveedor) display: none !important; @endif width:100%;" class="mb-3 row d-flex align-items-center">
                                    <label for="nombre" class="col-sm-12 col-form-label">Facturas</label>
                                    <div class="col-sm-10">
                                        <div class="col-md-12" x-data="" x-init="$('#select2-monitor').select2();
                                        $('#select2-monitor').on('change', function(e) {
                                            var data = $('#select2-monitor').select2('val');
                                            @this.set('pedido_id', data);
                                        });" wire:key='rand()'>
                                            <select class="form-control" name="pedido_id" id="select2-monitor"
                                            wire:model.lazy="pedido_id"  wire:change="onFacturaChange({{$pedido_id}})"  >
                                                <option value="0">-- ELIGE UNA FACTURA
                                                    --
                                                </option>
                                                @foreach ($facturas as $factura)
                                                    <option value="{{ $factura->id }}">
                                                        ({{ $factura->numero_factura }}) - {{ $this->getCliente($factura->cliente_id) }} @if(!$this->facturaHasIva($factura->id)) * @endif
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div> @error('nombre')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                                
                            <div style="@if(!$isIngresoProveedor) display: none !important; @endif width:100%;" class="mb-3 row d-flex align-items-center">

                                <label for="nombre" class="col-sm-12 col-form-label">Gasto asociado</label>
                                <div class="col-sm-10" wire:ignore x-data x-init="
                                    $nextTick(() => {
                                        $('#select2-gastos').select2({
                                            ajax: {
                                                url: '{{ route('buscarGastos') }}', // Ruta de Livewire que hará la búsqueda
                                                dataType: 'json',
                                                delay: 250, // Retardo de búsqueda para evitar demasiadas peticiones
                                                data: function(params) {
                                                    return {
                                                        search: params.term, // Término de búsqueda
                                                        page: params.page || 1 // Página actual
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
                                            allowClear: true,
                                        });

                                        $('#select2-gastos').on('change', function() {
                                            var data = $(this).val();
                                            @this.set('gasto_id', data);
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

                                
                        </div>
                        @if(!$compensacion_factura)
                            <div class="mb-3 row d-flex align-items-center">
                                <label for="nombre" class="col-sm-12 col-form-label">Importe</label>
                                <div class="col-sm-10">
                                    <input type="number" class="form-control" wire:model="importe" nombre="importe"
                                        id="importe" placeholder="Importe...">
                                    @error('importe')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        @else
                            <div class="mb-3 row d-flex align-items-center">
                                <label for="nombre" class="col-sm-12 col-form-label">Importe</label>
                                <div class="col-sm-10">
                                    <input type="number" class="form-control" wire:model="importe" nombre="importe"
                                        id="importe" placeholder="Importe..." disabled>
                                    @error('importe')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="mb-3 row d-flex align-items-center">
                                <label for="nombre" class="col-sm-12 col-form-label">Importe Compensado</label>
                                <div class="col-sm-10">
                                    <input type="number" class="form-control" wire:model="importeCompensado" nombre="importeCompensado"
                                        id="importeCompensado" placeholder="importeCompensado..." disabled>
                                    @error('importeCompensado')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="mb-3 row d-flex align-items-center">
                                <label for="nombre" class="col-sm-12 col-form-label">Importe Pendiente</label>
                                <div class="col-sm-10">
                                    <input type="number" class="form-control" wire:model="importeFacturaCompensada" nombre="importeFacturaCompensada"
                                        id="importeFacturaCompensada" placeholder="importeFacturaCompensada..." >
                                    @error('importeFacturaCompensada')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        @endif
                        <div class="mb-3 row d-flex align-items-center">
                            <label for="nombre" class="col-sm-12 col-form-label">Fecha</label>
                            <div class="col-sm-10">
                                <input type="date" class="form-control" wire:model="fecha" nombre="fecha"
                                    id="fecha" placeholder="dd/mm/aaaa">
                                @error('fecha')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="mb-3 row d-flex align-items-center">
                            <label for="nombre" class="col-sm-12 col-form-label">Método de pago</label>
                            <div class="col-sm-10" wire:ignore.self>
                                <select id="metodo_pago" class="form-control" wire:model="metodo_pago">
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
                        @if(count($bancos) > 0)
                            <div class="mb-3 row d-flex align-items-center">
                                <label for="nombre" class="col-sm-12 col-form-label">Cuenta Bancaria</label>
                                <div class="col-sm-10" wire:ignore.self>
                                    <select id="banco" class="form-control" wire:model="banco">
                                            <option value="" selected>Selecciona una opción</option>
                                            @foreach ($bancos as $banco)
                                                <option value="{{ $banco->id }}">{{ $banco->nombre }}</option>     
                                            @endforeach
                                        </select>
                                    @error('banco')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        @endif
                        {{-- <div class="mb-3 row d-flex align-items-center">
                            <div class="col-sm-10" wire:ignore.self>
                                <label >
                                    <input type="checkbox" wire:model="compensacion" nombre="compensacion" id="compensacion">
                                    ¿Compensación?
                                </label>
                            </div>
                        </div> --}}
                        @if($compensacion === 1)
                            <!-- Si la compensación está activada, se muestra ... -->
                        @endif
                        <div class="mb-3 row d-flex align-items-center">
                            <label for="nombre" class="col-sm-12 col-form-label">Descripción</label>
                            <div class="col-sm-10">
                                <textarea wire:model="descripcion" nombre="descripcion" id="descripcion" placeholder="Descripción" rows="4" cols="150"></textarea>
                                @error('nombre')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
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
                            <button class="w-100 btn btn-success mb-2" id="alertaGuardar">Guardar nuevo ingreso </button>
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
                text: 'Pulsa el botón de confirmar para guardar la nueva categoría.',
                icon: 'warning',
                showConfirmButton: true,
                showCancelButton: true
            }).then((result) => {
                if (result.isConfirmed) {
                    window.livewire.emit('submit');
                }
            });
        });

        $(document).ready(function() {
            //select2 monitor on change emit to livewire pedido_id with value and onchange event
            $('#select2-monitor').on('change', function(e) {
                console.log('change');
                var data = $('#select2-monitor').select2('val');
                console.log(data);
                @this.set('pedido_id', data);
                //emit function onFacturaChange
                window.livewire.emit('onFacturaChange', data);
            });
            $('#select2-gastos').on('change', function(e) {
                console.log('change');
                var data = $('#select2-gastos').select2('val');
                console.log(data);
                @this.set('gasto_id', data);
                //emit function onFacturaChange
            });
        });

    // Cuando Livewire redibuje, reinicializa Select2
    document.addEventListener('livewire:updated', function () {
        $('#select2-monitor').select2();
        $('#select2-gastos').select2();
    });
       
   
    </script>
@endsection
