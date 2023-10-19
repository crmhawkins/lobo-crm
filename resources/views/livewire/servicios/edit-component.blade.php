<div class="container-fluid">
    <script src="//unpkg.com/alpinejs" defer></script>
    <div class="page-title-box">
        <div class="row align-items-center">
            <div class="col-sm-6">
                <h4 class="page-title">EDITAR SERVICIO {{ $nombre }}</span></h4>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-right">
                    <li class="breadcrumb-item"><a href="javascript:void(0);">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="javascript:void(0);">Servicios</a></li>
                    <li class="breadcrumb-item active">Editar servicio {{ $nombre }}</li>
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
                        <div class="form-group row">
                            <div class="col-sm-12">
                                <h5 class="ms-3"
                                    style="border-bottom: 1px gray solid !important; padding-bottom: 10px !important;">
                                    Datos
                                    básicos del servicio</h5>
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="col-sm-12">
                                <label for="nombre" class="col-sm-12 col-form-label">Nombre </label>
                                <div class="col-sm-11">
                                    <input type="text" wire:model="nombre" class="form-control" name="nombre"
                                        id="nombre" placeholder="Evento">
                                    @error('nombre')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="col-sm-12">
                                <label for="id_categoria" class="col-sm-12 col-form-label">Categoria </label>
                                <div class="col-sm-11">
                                    <select class="form-control" name="id_categoria" required id="id_categoria"
                                        wire:model="id_categoria" wire:change="refreshArticulos">
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
                        </div>

                        <div class="form-group row">
                            <div class="col-sm-12">
                                <label for="id_pack" class="col-sm-12 col-form-label">Pack </label>
                                <div class="col-sm-11">
                                    <select class="form-control" name="id_pack" required id="id_pack"
                                        wire:model="id_pack">
                                        <option value="">-- No pertenece a un pack de servicios --</option>
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
                        </div>
                        <div class="form-group row">
                            <div class="col-sm-12">
                                <label for="minMonitor" class="col-sm-12 col-form-label">Nº minimo de monitores </label>
                                <div class="col-sm-11">
                                    <input type="number" wire:model="minMonitor" class="form-control"
                                        name="minMonitors" id="minMonitor" placeholder="1">
                                    @error('minEmpleados')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <div class="card mt-5">
                <div class="card-body">
                    <div class="form-group row">
                        <div class="col-sm-12">
                            <h5 class="ms-3"
                                style="border-bottom: 1px gray solid !important; padding-bottom: 10px !important;">
                                Costes del servicio</h5>
                        </div>
                        <div class="form-group row">
                            <div class="col-sm-3">
                                <label for="precioBase" class="col-sm-12 col-form-label">Precio Base </label>
                                <div class="col-sm-9">
                                    <input type="text" wire:model="precioBase" class="form-control" name="precioBase"
                                        id="precioBase" placeholder="0">
                                    @error('precioBase')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <label for="precioMonitor" class="col-sm-12 col-form-label">Precio por monitor
                                    (diurno)
                                </label>
                                <div class="col-sm-9">
                                    <input type="text" wire:model="precioMonitor" class="form-control"
                                        name="precioMonitor" id="precioMonitor" placeholder="20">
                                    @error('precioMonitor')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <label for="precioMonitor" class="col-sm-12 col-form-label">Precio por monitor
                                    (nocturno)
                                </label>
                                <div class="col-sm-9">
                                    <input type="text" wire:model="precioMonitorNocturno" class="form-control"
                                        name="precioMonitor" id="precioMonitorNocturno" placeholder="20">
                                    @error('precioMonitor')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <label for="precioMonitor" class="col-sm-12 col-form-label">Precio por monitor
                                    (animación)
                                </label>
                                <div class="col-sm-9">
                                    <input type="text" wire:model="precioMonitorAnimacion" class="form-control"
                                        name="precioMonitor" id="precioMonitorAnimacion" placeholder="20">
                                    @error('precioMonitor')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card mt-5">
                <div class="card-body">
                    <div class="form-group row">
                        <div class="col-sm-12">
                            <h5 class="ms-3"
                                style="border-bottom: 1px gray solid !important; padding-bottom: 10px !important;">
                                Tiempos del servicio</h5>
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-sm-4">
                            <label for="tiempoServicio" class="col-sm-12 col-form-label">Duración del evento
                            </label>
                            <div class="col-sm-9">
                                <input type="time" wire:model="tiempoServicio" class="form-control"
                                    name="precioBase" id="tiempoServicio" placeholder="0">
                                @error('tiempoServicio')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <label for="tiempoMontaje" class="col-sm-12 col-form-label">Tiempo de montaje</label>
                            <div class="col-sm-9">
                                <input type="time" wire:model="tiempoMontaje" class="form-control"
                                    name="precioBase" id="tiempoMontaje" placeholder="0">
                                @error('tiempoMontaje')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <label for="tiempoDesmontaje" class="col-sm-12 col-form-label">Tiempo de
                                desmontaje</label>
                            <div class="col-sm-9">
                                <input type="time" wire:model="tiempoDesmontaje" class="form-control"
                                    name="precioBase" id="tiempoDesmontaje" placeholder="0">
                                @error('tiempoDesmontaje')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card mt-5">
                <div class="card-body">
                    <div class="form-group row">
                        <div class="col-sm-12">
                            <h5 class="ms-3"
                                style="border-bottom: 1px gray solid !important; padding-bottom: 10px !important;">
                                Artículos</h5>
                        </div>
                    </div>
                    <div class="form-group row justify-content-center">
                        <div class="form-group col-md-6">
                            <div x-data="" x-init="$('#select2-cat').select2();
                            $('#select2-cat').on('change', function(e) {
                                var data = $('#select2-cat').select2('val');
                                @this.set('articulo_seleccionado', data);
                            });" wire:key="{{ rand() }}">
                                <label for="fechaVencimiento">Elige un artículo para asignarlo al servicio:</label>
                                <select class="form-control" name="estado" id="select2-cat">
                                    <option value="">-- SELECCIONE UN ARTÍCULO --</option>
                                    @foreach ($articulosSelect as $articulo)
                                        <option value="{{ $articulo['id'] }}">{{ $articulo['name'] }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="form-group col-md-3">
                            <label for="stock" class="col-sm-12 col-form-label">Stock</label>
                            <div class="col-sm-9">
                                <input type="number" class="form-control" name="stock" id="stock"
                                    wire:model="stock_usado">
                            </div>
                        </div>
                        <div class="form-group col-md-2">
                            <button class="btn btn-primary" wire:click.prevent="addStock">Añadir artículo</button>
                        </div>
                    </div>
                    <div class="form-group row justify-content-center">
                        @if (!empty($listaArticulos))
                            <table class="table table-striped table-bordered nowrap">
                                <thead>
                                    <tr>
                                        <th>Artículo</th>
                                        <th>Stock</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($listaArticulos as $articuloIndex => $articulo)
                                        <tr>
                                            <td>{{ $articulos->where('id', $articulo['id'])->first()->name }}</td>
                                            <td>{{ $articulo['stock_usado'] }}</td>
                                            <td><button class="btn btn-danger"
                                                    wire:click.prevent="deleteStock('{{ $articuloIndex }}')">X</button>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card m-b-30">
                <div class="card-body">
                    <h5>Acciones</h5>
                    <div class="row">
                        <div class="col-12">
                            <button class="w-100 btn btn-success mb-2" id="alertaGuardar">Editar
                                servicio</button>
                            <button class="w-100 btn btn-danger mb-2" wire:click="destroy">Eliminar
                                servicio</button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card m-b-30">
                <div class="card-body">
                    <h5>Opciones</h5>
                    <div class="row">
                        <div class="col-12">
                            <a href="{{ route('servicios-packs.create') }}" class="btn btn-primary w-100"
                                target="_blank">Añadir nuevo pack de servicios</a>
                        </div>
                        <div class="col-12 mt-1">
                            <a href="{{ route('servicios-categorias.create') }}" class="btn btn-primary w-100"
                                target="_blank">Añadir nueva categoría de servicios</a>
                        </div>
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
                    window.livewire.emit('update');
                }
            });
        });
    </script>
@endsection
