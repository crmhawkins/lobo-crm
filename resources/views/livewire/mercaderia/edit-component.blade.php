<div class="container-fluid">
    <div class="page-title-box">
        <div class="row align-items-center">
            <div class="col-sm-6">
                <h4 class="page-title">EDITANDO {{ $nombre }}</span></h4>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-right">
                    <li class="breadcrumb-item"><a href="javascript:void(0);">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="javascript:void(0);">Materiales</a></li>
                    <li class="breadcrumb-item active">Editar material {{ $nombre }}</li>
                </ol>
            </div>
        </div> <!-- end row -->
    </div>
    <div class="row" style="align-items: start !important">
        <div class="col-md-9">
            <div class="card m-b-30">
                <div class="card-body">
                    <form wire:submit.prevent="submit">
                        <input type="hidden" name="csrf-token" value="{{ csrf_token() }}">
                        <div class="row d-flex align-items-center">
                            <div class="col-md-6">
                                <label for="nombre" class="col-form-label">Nombre del material</label>
                                <input type="text" class="form-control" wire:model="nombre" name="nombre"
                                    id="nombre" placeholder="Nombre del producto...">
                                @error('nombre')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="col-md-4" wire:ignore>
                                <div x-data="" x-init="$('#select2-categoria').select2({ tags: true });
                                $('#select2-categoria').on('change', function(e) {
                                    var data = $('#select2-categoria').select2('val');
                                    @this.set('categoria_id', data);
                                });">
                                    <label for="categoria">Categoría del material</label>
                                    <select class="form-control" name="producto" id="select2-categoria"
                                        value="{{ $categoria_id }}">
                                        @foreach ($categorias as $categoria)
                                            <option value="{{ $categoria->id }}">{{ $categoria->nombre }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <label for="stock_seguridad" class="col-form-label">Stock de seguridad</label>
                                <input type="number" class="form-control" wire:model="stock_seguridad" name="stock_seguridad"
                                    id="stock_seguridad" placeholder="Stock de seguridad...">
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div class="col-md-3 justify-content-center">
            <div class="card m-b-30">
                <div class="card-body">
                    <h5>Acciones</h5>
                    <div class="row">
                        <div class="col-12">
                            <button class="w-100 btn btn-success mb-2" id="alertaGuardar">Guardar
                                producto </button>
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
                text: 'Pulsa el botón de confirmar para actualizar el producto.',
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
    <script src="../../assets/js/jquery.slimscroll.js"></script>

    <script src="../../plugins/datatables/jquery.dataTables.min.js"></script>
    <script src="../../plugins/datatables/dataTables.bootstrap4.min.js"></script>
    <!-- Buttons examples -->
    <script src="../../plugins/datatables/dataTables.buttons.min.js"></script>
    <script src="../../plugins/datatables/buttons.bootstrap4.min.js"></script>
    <script src="../../plugins/datatables/jszip.min.js"></script>
    <script src="../../plugins/datatables/pdfmake.min.js"></script>
    <script src="../../plugins/datatables/vfs_fonts.js"></script>
    <script src="../../plugins/datatables/buttons.html5.min.js"></script>
    <script src="../../plugins/datatables/buttons.print.min.js"></script>
    <script src="../../plugins/datatables/buttons.colVis.min.js"></script>
    <!-- Responsive examples -->
    <script src="../../plugins/datatables/dataTables.responsive.min.js"></script>
    <script src="../../plugins/datatables/responsive.bootstrap4.min.js"></script>
    <script src="../../assets/pages/datatables.init.js"></script>
@endsection
