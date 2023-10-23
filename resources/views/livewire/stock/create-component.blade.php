<div class="container-fluid">
    <div class="page-title-box">
        <div class="row align-items-center">
            <div class="col-sm-6">
                <h4 class="page-title">NUEVO LOTE DE {{$nombre}}</span></h4>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-right">
                    <li class="breadcrumb-item"><a href="javascript:void(0);">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="javascript:void(0);">Stock</a></li>
                    <li class="breadcrumb-item active">Nuevo lote de {{$nombre}}</li>
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
                            <div class="col-md-12">
                                <label for="lote_id" class="col-form-label">Identificador del lote</label>
                                <input type="text" class="form-control" wire:model="lote_id" name="lote_id"
                                    id="lote_id" placeholder="Identificador del lote">
                                @error('lote_id')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="col-md-12">
                                <label for="cantidad_inicial" class="col-form-label">Unidades del producto en el lote</label>
                                <input type="number" class="form-control" wire:model="cantidad_inicial" name="cantidad_inicial"
                                    id="cantidad_inicial" placeholder="Unidades del producto incluidas en el lote">
                                @error('cantidad_inicial')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="col-md-12">
                                <label for="cantidad_inicial" class="col-form-label">Fecha de entrada</label>
                                <input type="date" class="form-control" wire:model="fecha_entrada" name="fecha_entrada"
                                    id="cantidad_inicial" placeholder="Fecha de entrada del lote">
                                @error('cantidad_inicial')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                </div>
                </form>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card m-b-30">
                <div class="card-body">
                    <h5>Acciones</h5>
                    <div class="row">
                        <div class="col-12">
                            <button class="w-100 btn btn-lg btn-success mb-2" id="alertaGuardar">Guardar
                                lote</button>
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
                text: 'Pulsa el botón de confirmar para guardar el producto.',
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
