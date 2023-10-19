<div class="container-fluid">
    <div class="page-title-box">
        <div class="row align-items-center">
            <div class="col-sm-6">
                <h4 class="page-title">OPCIONES DEL CRM</span></h4>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-right">
                    <li class="breadcrumb-item"><a href="javascript:void(0);">Dashboard</a></li>
                    <li class="breadcrumb-item active">Opciones</li>
                </ol>
            </div>
        </div> <!-- end row -->
    </div>
    <div class="row">
        <div class="col-md-9">
            <div class="card m-b-30">
                <div class="card-body">
                    <div class="form-group col-md-4">
                        <label for="eventoNiños" class="col-sm-12 col-form-label">Precio del gasoil por
                            kilómetro</label>
                        <div class="col-sm-10">
                            <input type="number" wire:model="precio_gasoil_km" class="form-control"
                                name="precio_gasoil_km" placeholder="0" step="0.01">
                        </div>
                    </div>
                    <div class="form-group col-md-4">
                        <label for="eventoNiños" class="col-sm-12 col-form-label">Saldo inicial</label>
                        <div class="col-sm-10">
                            <input type="number" wire:model="precio_gasoil_km" class="form-control"
                                name="precio_gasoil_km" placeholder="0" step="0.01">
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 justify-content-center">
            <div class="card m-b-30 position-fixed">
                <div class="card-body">
                    <h5>Opciones de guardado</h5>
                    <div class="row">
                        <div class="col-12">
                            <button class="w-100 btn btn-success mb-2" id="alertaGuardar">Guardar
                                opciones</button>
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
    </script>
@endsection
