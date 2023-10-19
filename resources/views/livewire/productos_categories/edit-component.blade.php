@section('head')
    @vite(['resources/sass/productos.scss'])
@endsection
<div class="container mx-auto">
    <h1>Productos - Categorías</h1>
    <h2>Editar Categoría</h2>
    <br>


            <form wire:submit.prevent="update">
                <input type="hidden" name="csrf-token" value="{{ csrf_token() }}">

                <div class="mb-3 row d-flex align-items-center">
                    <label for="nombre" class="col-sm-2 col-form-label">Nombre de la categoría</label>
                    <div class="col-sm-10">
                      <input type="text" class="form-control" wire:model="nombre" nombre="nombre" id="nombre" placeholder="Nombre del producto...">
                      @error('name') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>
                </div>


                <div class="mb-3 row d-flex align-items-center">
                    <button type="submit" class="btn btn-outline-info">Guardar</button>
                </div>


            </form>
            <div class="mb-3 row d-flex align-items-center">
              <button wire:click="destroy" class="btn btn-outline-danger">Eliminar</button>
          </div>
        </div>





</div>

