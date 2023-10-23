<div class="container-fluid">
    <div class="page-title-box">
        <div class="row align-items-center">
            <div class="col-sm-6">
                <h4 class="page-title">EDITANDO {{ $nombre }}</span></h4>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-right">
                    <li class="breadcrumb-item"><a href="javascript:void(0);">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="javascript:void(0);">Productos</a></li>
                    <li class="breadcrumb-item active">Editar producto {{ $nombre }}</li>
                </ol>
            </div>
        </div> <!-- end row -->
    </div>
    <div class="row" style="align-items: start !important">
        <div class="col-md-9">
            <div class="card m-b-30">
                <div class="card-body">
                    <h5>Datos del producto</h5>
                    <form wire:submit.prevent="submit">
                        <input type="hidden" name="csrf-token" value="{{ csrf_token() }}">
                        <div class="row d-flex align-items-center">
                            <div class="col-md-12">
                                <label for="nombre" class="col-form-label">Nombre del producto</label>
                                <input type="text" class="form-control" wire:model="nombre" name="nombre"
                                    id="nombre" placeholder="Nombre del producto...">
                                @error('nombre')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="mb-3 row d-flex align-items-center">
                            <div class="col-md-6">
                                <label for="precio" class="col-form-label">Precio del producto (Sin IVA)</label>
                                <input type="number" class="form-control" wire:model="precio" name="precio"
                                    id="precio" placeholder="Precio del producto (Sin IVA)...">
                                @error('precio')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="iva" class="col-form-label">Porcentaje de IVA</label>
                                <input type="number" class="form-control" wire:model="iva" name="iva"
                                    id="iva" placeholder="Porcentaje de IVA...">
                                @error('iva')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                </div>
                </form>
            </div>
            <div class="card m-b-30">
                <div class="card-body">
                    <h5>Registro de entrada de stock</h5>

                    <table class="table table-striped table-bordered dt-responsive nowrap">
                        <thead>
                            <tr>
                                <th>Lote</th>
                                <th>Cantidad inicial</th>
                                <th>Cantidad actual</th>
                                <th>Fecha de entrada</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($producto_lotes as $loteIndex => $lote)
                                <tr>
                                    <th>{{ $lote['lote_id'] }}</th>
                                    <td>{{ $lote['cantidad_inicial'] }}</td>
                                    <td>{{ $lote['unidades'] }}</td>
                                    <td>{{ $lote['fecha_entrada'] }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="col-md-3 justify-content-center">
            <div class="card m-b-30">
                <div class="card-body">
                    <h5>Imagen del producto</h5>
                    <div class="row">
                        <div class="col">
                            @if ($foto_ruta || $foto_rutaOld)
                                <img @if ($foto_ruta) src="{{ $foto_ruta->temporaryUrl() }}" @else src="{{ asset('storage/photos/' . $foto_rutaOld) }}" @endif
                                    style="max-width: 100% !important; text-align: center">
                            @endif

                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-12">
                            <input type="file" class="form-control" wire:model="foto_ruta" name="foto_ruta"
                                wire:change='nuevaFoto' id="foto_ruta" placeholder="Imagen del producto...">
                            @error('nombre')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>
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
