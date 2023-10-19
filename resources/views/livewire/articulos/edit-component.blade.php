<div class="container-fluid">
    <div class="page-title-box">
        <div class="row align-items-center">
            <div class="col-sm-6">
                <h4 class="page-title">EDITAR ARTICULO</span></h4>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-right">
                    <li class="breadcrumb-item"><a href="javascript:void(0);">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="javascript:void(0);">Articulos</a></li>
                    <li class="breadcrumb-item active">Editar articulo</li>
                </ol>
            </div>
        </div> <!-- end row -->
    </div>
    <!-- end page-title -->

    <div class="row">
        <div class="col-md-9">
            <div class="card m-b-30">
                <div class="card-body">
                    <form wire:submit.prevent="submit">
                        <input type="hidden" name="csrf-token" value="{{ csrf_token() }}">
                        <input type="hidden" name="id" value="{{ csrf_token() }}">

                        <!-- Nombre -->
                        <div class="form-group row">
                            <label for="name" class="col-sm-12 col-form-label">Nombre</label>
                            <div class="col-sm-11">
                                <input type="text" wire:model="name" class="form-control" name="name" id="name" aria-label="Nombre" placeholder="Nombre">
                                @error('name')
                                <span class="text-danger">{{ $message }}</span>


                                <style>
                                    .nombre {
                                        color: red;
                                    }
                                </style>
                                @enderror
                            </div>
                        </div>

                        <!-- Stock -->
                        <div class="form-group row">
                            <label for="name" class="col-sm-12 col-form-label">Stock</label>
                            <div class="col-sm-11">
                                <input type="text" wire:model="stock" class="form-control" name="stock" id="stock" aria-label="Stock" placeholder="Stock">
                                @error('stock')
                                <span class="text-danger">{{ $message }}</span>


                                <style>
                                    .nombre {
                                        color: red;
                                    }
                                </style>
                                @enderror
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="id_categoria" class="col-sm-12 col-form-label">Categoria </label>
                            <div class="col-sm-11">
                                <select class="form-control" name="id_categoria" required id="id_categoria" wire:model="id_categoria">
                                    <option value="">Elige una categoría de servicio</option>
                                    @foreach ($servicioCategorias as $categoria)
                                    <option value="{{ $categoria->id }}">
                                        {{ $categoria->nombre }}
                                    </option>
                                    @endforeach
                                    @error('id_categoria')
                                    <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </select>
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
                                Artículo</button>
                        </div>
                        <div class="col-12">
                            <button class="w-100 btn btn-danger mb-2" wire:click="destroy">Eliminar
                                Artículo</button>
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
            text: 'Pulsa el botón de confirmar para guardar el artículo.',
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