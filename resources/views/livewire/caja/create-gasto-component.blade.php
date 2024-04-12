<div class="container-fluid">
    <div class="page-title-box">
        <div class="row align-items-center">
            <div class="col-sm-6">
                <h4 class="page-title">AÑADIR MOVIMIENTO DE CAJA (GASTO)</span></h4>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-right">
                    <li class="breadcrumb-item"><a href="javascript:void(0);">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="javascript:void(0);">Caja</a></li>
                    <li class="breadcrumb-item active">Añadir movimiento de gasto</li>
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
                            <label for="Proveedor" class="col-sm-12 col-form-label">Proveedor</label>
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
                                </div>
                            </div>
                        </div>
                        <div class="mb-3 row d-flex align-items-center">
                            <label for="estado" class="col-sm-12 col-form-label">Estado</label>
                            <div class="col-sm-10">
                                <select class="form-control" name="estado" id="estado"  wire:model.lazy="estado">
                                    <option value="Pendiente">Pendiente</option>
                                    <option value="Pagado">Pagado</option>
                                    <option value="Vencido">Vencido</option>
                                </select>
                            </div>
                        </div>
                        <div class="mb-3 row d-flex align-items-center">
                            <label for="importe" class="col-sm-12 col-form-label">Importe</label>
                            <div class="col-sm-10">
                                <input type="number"  step="0.1" class="form-control" wire:model="importe" nombre="importe"
                                    id="importe" placeholder="Importe">
                            </div>
                        </div>
                        <div class="mb-3 row d-flex align-items-center">
                            <label for="fecha" class="col-sm-12 col-form-label">Fecha</label>
                            <div class="col-sm-10">
                                <input type="date" class="form-control" wire:model="fecha" nombre="fecha"
                                    id="fecha" placeholder="Nombre de la categoría...">
                            </div>
                        </div>
                        <div class="mb-3 row d-flex align-items-center">
                            <label for="pago" class="col-sm-12 col-form-label">Método de pago</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control" wire:model="metodo_pago" nombre="metodo_pago"
                                    id="metodo_pago" placeholder="Nombre de la categoría...">
                            </div>
                        </div>
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
                        <div class="mb-3 row d-flex align-items-center">
                            <label for="nombre" class="col-sm-12 col-form-label">Descripción</label>
                            <div class="col-sm-10">
                                <textarea wire:model="descripcion" nombre="descripcion" id="descripcion" placeholder="Nombre de la categoría..." rows="4" cols="150"></textarea>
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
                            <button class="w-100 btn btn-success mb-2" id="alertaGuardar">Guardar nuevo Gasto </button>
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
    </script>
@endsection
