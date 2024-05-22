<div class="container-fluid">
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

                        <div class="mb-3 row d-flex align-items-center">
                            <label for="nombre" class="col-sm-12 col-form-label">Facturas</label>
                            <div class="col-sm-10">
                                <div class="col-md-12" x-data="" x-init="$('#select2-monitor').select2();
                                $('#select2-monitor').on('change', function(e) {
                                    var data = $('#select2-monitor').select2('val');
                                    @this.set('pedido_id', data);
                                });" wire:key='rand()'>
                                    <select class="form-control" name="pedido_id" id="select2-monitor"
                                    wire:model.lazy="pedido_id"  >
                                        <option value="0">-- ELIGE UNA FACTURA
                                            --
                                        </option>
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
                            <button class="w-100 btn btn-success mb-2" id="alertaGuardar">Guardar
                                nuevo ingreso </button>
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

        //on document ready with jquery
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

        });
       
   
    </script>
@endsection
