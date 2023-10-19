@section('head')
    @vite(['resources/sass/productos.scss'])
    @vite(['resources/sass/alumnos.scss'])
@endsection



<div class="container mx-auto">
    <h1>Servicios</h1>
    <h2>Crear servicio</h2>
    <br>


    <form wire:submit.prevent="submit">
        <input type="hidden" name="csrf-token" value="{{ csrf_token() }}">

        <div class="mb-3 row d-flex align-items-center">
            <label for="nombre" class="col-sm-2 col-form-label">Nombre </label>
            <div class="col-sm-10">
                <input type="text" wire:model="nombre" class="form-control" name="nombre" id="nombre"
                    placeholder="Evento">
                @error('nombre')
                    <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>
        </div>

        <div class="mb-3 row d-flex align-items-center">
            <label for="id_categoria" class="col-sm-2 col-form-label">Categoria </label>
            <div class="col-sm-10">
                <select class="form-control" name="id_categoria" required id="id_categoria" wire:model="id_categoria">
                    <option value="">Categorias</option>
                    @foreach ($servicioCategorias as $categoria)
                        <option value="{{ $categoria->id }}">
                            {{ $categoria->nombre }}</option>
                    @endforeach
                    @error('id_categoria')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </select>
            </div>
        </div>

        <div class="mb-3 row d-flex align-items-center">
            <label for="id_pack" class="col-sm-2 col-form-label">Pack </label>
            <div class="col-sm-10">
                <select class="form-control" name="id_pack" required id="id_pack"
                    wire:model="id_pack">
                    <option value="">Pack</option>
                    @foreach ($servicioPacks as $pack)
                        <option value="{{ $pack->id }}">{{ $pack->nombre }}
                        </option>
                    @endforeach
                    @error('id_pack')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </select>
            </div>
        </div>


        <div class="mb-3 row d-flex align-items-center">
            <label for="precioBase" class="col-sm-2 col-form-label">Precio Base </label>
            <div class="col-sm-10">
                <input type="text" wire:model="precioBase" class="form-control" name="precioBase" id="precioBase"
                    placeholder="0">
                @error('precioBase')
                    <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>
        </div>

        <div class="mb-3 row d-flex align-items-center">
            <label for="minEmpleados" class="col-sm-2 col-form-label">Empleados mínimos </label>
            <div class="col-sm-10">
                <input type="number" wire:model="minEmpleados" class="form-control" name="minEmpleados"
                    id="minEmpleados" placeholder="1">
                @error('minEmpleados')
                    <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>
        </div>

        <div class="mb-3 row d-flex align-items-center">
            <button type="submit" class="btn btn-outline-info">Guardar</button>
        </div>


    </form>
</div>





</div>

</tbody>
</table>
