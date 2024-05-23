@php
$EsAdmin = Auth::user()->isAdmin();
$canEdit = $EsAdmin; //|| $estado == 1;
@endphp
<div class="container-fluid">
    <div class="page-title-box">
        <div class="row align-items-center">
            <div class="col-sm-6">
                <h4 class="page-title">MATERIALES</h4>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-right">
                    <li class="breadcrumb-item"><a href="javascript:void(0);">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="javascript:void(0);">Materiales</a></li>
                    <li class="breadcrumb-item active">Ver materiales para productos</li>
                </ol>
            </div>
        </div> <!-- end row -->
    </div>
    <!-- end page-title -->


    <div class="row">
        <div class="col-12">
            <div class="card m-b-30">
                <div class="card-body">
                    <h4 class="mt-0 header-title">Listado de todos los materiales</h4>
                    @if (count($mercaderias) > 0)
                        <div class="col-md-12" wire:ignore>
                            <a href="{{ route('stock-mercaderia.index') }}"
                                class="btn btn-lg btn-primary w-100 mb-1">ENTRADA DE MATERIALES</a>
                            <a href="#" wire:click.prevent="comprobarStockMateriales"
                                class="btn btn-lg btn-primary w-100 mb-1">COMPROBAR STOCK DE MATERIALES</a>
                            <a href="{{ route('mercaderia.create') }}" class="btn btn-lg btn-primary w-100">NUEVO
                                MATERIAL PARA PRODUCTO</a>

                            <div  wire:key='{{ time() . 'juanito' }}'>
                                <label for="categoria">Categoría de los materiales</label>
                                <select class="form-control" name="producto" id="select2-categoria" wire:model="categoria_id"
                                    value="{{ $categoria_id }}">
                                    <option value="0">Todos los materiales</option>
                                    @foreach ($categorias as $categoria)
                                        <option value="{{ $categoria->id }}">{{ $categoria->nombre }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-12" wire:ignore.self>

                            </div>
                            <table id="datatable-buttons"
                                class="table table-striped table-bordered dt-responsive nowrap"
                                style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                                <thead>
                                    <tr>
                                        <th scope="col">Nombre</th>
                                        <th scope="col">Categoría</th>
                                        <th scope="col">Cantidad</th>
                                        <th scope="col">Cantidad asignada</th>
                                        <th scope="col">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($mercaderias as $mercaderia)
                                        <tr>
                                            <td>{{ $mercaderia->nombre }}</td>
                                            <td>{{ $this->getCategoria($mercaderia->categoria_id) }}</td>
                                            <td>{{ $this->getCantidad($mercaderia->id)}}</td>
                                            <td>{{ $this->getCantidadProduccion($mercaderia->id) }}</td>
                                            <td> 
                                                @if($canEdit)
                                                    <a href="mercaderia-edit/{{ $mercaderia->id }}" class="btn btn-primary">Ver/Editar</a> 
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

@section('scripts')
<script>
    $("#comprobarStockMateriales").on("click", () => {
        Swal.fire({
            title: '¿Desea comprobar el stock de materiales?',
            text: 'Pulsa el botón de confirmar para continuar.',
            icon: 'info',
            showConfirmButton: true,
            showCancelButton: true
        }).then((result) => {
            if (result.isConfirmed) {
                window.livewire.emit('comprobarStockMateriales');
            }
        });
    });
</script>
<script src="../assets/js/jquery.slimscroll.js"></script>
<link href="https://cdn.datatables.net/v/bs5/jq-3.7.0/jszip-3.10.1/dt-2.0.3/b-3.0.1/b-colvis-3.0.1/b-html5-3.0.1/b-print-3.0.1/r-3.0.1/datatables.min.css" rel="stylesheet">
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>
<script src="https://cdn.datatables.net/v/bs5/jq-3.7.0/jszip-3.10.1/dt-2.0.3/b-3.0.1/b-colvis-3.0.1/b-html5-3.0.1/b-print-3.0.1/r-3.0.1/datatables.min.js"></script>
<!-- Responsive examples -->
<script src="../assets/pages/datatables.init.js"></script>
@endsection
