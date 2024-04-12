@section('head')
    @vite(['resources/sass/productos.scss'])
@endsection
<div class="container-fluid">
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
                        @if ($this->tipo_movimiento == "Ingreso")
                        <div class="mb-3 row d-flex align-items-center">
                            <label for="nombre" class="col-sm-12 col-form-label">Factura</label>
                            <div class="col-sm-10">
                                <div class="col-md-12" x-data="" x-init="$('#select2-monitor').select2();
                                    $('#select2-monitor').on('change', function(e) {
                                    var data = $('#select2-monitor').select2('val');
                                    @this.set('pedido_id', data);
                                    });" wire:key='rand()'>
                                    <select class="form-control" name="pedido_id" id="select2-monitor"
                                        wire:model.lazy="pedido_id">
                                        <option value="0">-- ELIGE UNA FACTURA --</option>
                                        @foreach ($facturas as $factura)
                                            <option value="{{ $factura->id }}">
                                                ({{ $factura->numero_factura }}) - {{ $this->getCliente($factura->cliente_id) }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div> @error('nombre')
                                <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        @elseif($this->tipo_movimiento == "Gasto")
                        <div class="mb-3 row d-flex align-items-center">
                            <label for="nombre" class="col-sm-12 col-form-label">Proveedor</label>
                            <div class="col-sm-10">
                                <div class="col-md-12" x-data="" x-init="$('#select2-monitor').select2();
                                $('#select2-monitor').on('change', function(e) {
                                    var data = $('#select2-monitor').select2('val');
                                    @this.set('poveedor_id', data);
                                });" wire:key='rand()'>
                                    <select class="form-control" name="poveedor_id" id="select2-monitor"
                                        wire:model.lazy="poveedor_id">
                                        <option value="0">-- ELIGE UN PROVEEDOR
                                            --
                                        </option>
                                        @foreach ($poveedores as $poveedor)
                                            <option value="{{ $poveedor->id }}">
                                                {{ $poveedor->nombre }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div> @error('nombre')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="mb-3 row d-flex align-items-center">
                            <label for="nombre" class="col-sm-12 col-form-label">Estado</label>
                            <div class="col-sm-10">
                                <select class="form-control" name="estado" id="estado"  wire:model.lazy="estado">
                                    <option value="Pendiente">Pendiente</option>
                                    <option value="Pagado">Pagado</option>
                                    <option value="Vencido">Vencido</option>
                                </select>
                            </div>
                        </div>
                        @endif
                        <div class="mb-3 row d-flex align-items-center">
                            <label for="nombre" class="col-sm-12 col-form-label">Importe</label>
                            <div class="col-sm-10">
                                <input type="number" class="form-control" wire:model="importe" nombre="importe"
                                    id="importe" placeholder="Importe...">
                                @error('nombre')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="mb-3 row d-flex align-items-center">
                            <label for="nombre" class="col-sm-12 col-form-label">Fecha</label>
                            <div class="col-sm-10">
                                <input type="date" class="form-control" wire:model="fecha" nombre="fecha"
                                    id="fecha" placeholder="dd/mm/aaaa">
                                @error('nombre')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="mb-3 row d-flex align-items-center">
                            <label for="pago" class="col-sm-12 col-form-label">Método de pago</label>
                            <div class="col-sm-10" wire:ignore.self>
                                @if ($this->tipo_movimiento == "Ingreso")
                                <select id="metodo_pago" class="form-control" wire:model="metodo_pago">
                                        <option value="" disabled selected>Selecciona una opción</option>
                                        <option value="giro_bancario">Giro Bancario</option>
                                        <option value="pagare">Pagare</option>
                                        <option value="confirming">Confirming</option>
                                        <option value="otros">Otros</option>
                                    </select>
                                @error('denominacion')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                                @elseif($this->tipo_movimiento == "Gasto")
                                <input type="text" class="form-control" wire:model="metodo_pago" nombre="metodo_pago"
                                id="metodo_pago" placeholder="Nombre de la categoría...">
                                @endif
                            </div>
                        </div>
                        @if($this->tipo_movimiento == "Gasto")
                        <div class="mb-3 row d-flex align-items-center">
                            <label for="banco" class="col-sm-12 col-form-label">Banco</label>
                            <div class="col-sm-10">
                                <select class="form-control" name="banco" wire:model="banco">
                                <option value="0">-- ELIGE UN BANCO --</option>
                                <option value="1">Santander</option>
                                <option value="2">CaixaBank</option>
                            </select>
                            </div>
                        </div>
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
                            <button class="w-100 btn btn-success mb-2" id="alertaGuardar">Editar Movimiento </button>
                        </div>
                        <div class="col-12">
                            <button class="w-100 btn btn-danger mb-2" wire:click="destroy">Eliminar Movimiento </button>
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
