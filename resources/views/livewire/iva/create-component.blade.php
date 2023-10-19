@section('head')

    <link rel="stylesheet" href="https://unpkg.com/tailwindcss@^2/dist/tailwind.min.css">
    @vite(['resources/sass/productos.scss'])
@endsection



    <div class="container mx-auto">
        <h1>IVA</h1>
        <h2>Crear tipo de IVA</h2>
        <br>


                <form wire:submit.prevent="submit">
                    <input type="hidden" name="csrf-token" value="{{ csrf_token() }}">

                    <div class="mb-3 row d-flex align-items-center">
                        <label for="name" class="col-sm-2 col-form-label">Nombre tipo IVA</label>
                        <div class="col-sm-10">
                          <input type="text" class="form-control" wire:model="name" nombre="name" id="name" placeholder="Nombre del tipo de IVA...">
                          @error('name') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div class="mb-3 row d-flex align-items-center">
                        <label for="iva" class="col-sm-2 col-form-label">IVA</label>
                        <div class="col-sm-10">
                          <input type="text" class="form-control" wire:model="iva" nombre="iva" id="iva" placeholder="IVA...">
                          @error('iva') <span class="text-danger">{{ $message }}</span> @enderror
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


