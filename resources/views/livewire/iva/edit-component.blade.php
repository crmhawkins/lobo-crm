@section('head')
    <link rel="stylesheet" href="https://unpkg.com/tailwindcss@^2/dist/tailwind.min.css">
    @vite(['resources/sass/productos.scss'])
@endsection
<div class="container mx-auto">
    <h1>IVA</h1>
    <h2>Editar tipo de IVA</h2>
    <br>


            <form wire:submit.prevent="update">
                <input type="hidden" name="csrf-token" value="{{ csrf_token() }}">

                <div class="mb-3 row d-flex align-items-center">
                    <label for="name" class="col-sm-2 col-form-label">Tipo de IVA</label>
                    <div class="col-sm-10">
                      <input type="text" class="form-control" wire:model="name" nombre="name" id="name" placeholder="Nombre del tipo de IVA...">
                      @error('name') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div class="mb-3 row d-flex align-items-center">
                    <label for="iva" class="col-sm-2 col-form-label">IVA</label>
                    <div class="col-sm-10">
                      <input type="text" class="form-control" wire:model="iva" nombre="iva" id="iva" placeholder="IVA...">
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

