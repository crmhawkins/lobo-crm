<div class="container-fluid">
    <div class="page-title-box">
        <div class="row align-items-center">
            <div class="col-sm-6">
                <h4 class="page-title">NUEVO PRODUCTO</span></h4>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-right">
                    <li class="breadcrumb-item"><a href="javascript:void(0);">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="javascript:void(0);">Productos</a></li>
                    <li class="breadcrumb-item active">Nuevo producto</li>
                </ol>
            </div>
        </div> <!-- end row -->
    </div>
    <!-- end page-title -->
    <div class="row" style="align-items: start !important">
        <div class="col-md-9">
            <div class="card m-b-30">
                <div class="card-body">
                    <form wire:submit.prevent="submit">
                        <input type="hidden" name="csrf-token" value="{{ csrf_token() }}">
                        <div class="row d-flex align-items-center">
                            <div class="col-md-4">
                                <label for="nombre" class="col-form-label">Nombre del producto</label>
                                <input type="text" class="form-control" wire:model="nombre" name="nombre"
                                    id="nombre" placeholder="Nombre del producto...">
                                @error('nombre')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="col-md-4">
                            <label for="tipo_precio">Tipo de precio</label>
                                <select class="form-control" name="tipo_precio" id="select-tipo_precio"
                                    wire:model="tipo_precio">

                                        <option value="1">Crema</option>
                                        <option value="2">Vodka 0,7L</option>
                                        <option value="3">Vodka 1,75L</option>
                                        <option value="4">Vodka 3L</option>
                                        <option value="5">Otros</option>

                                </select>
                            </div>
                            <div class="col-md-4">
                                <label for="iva" class="col-form-label">Precio para otros</label>
                                <input type="number" class="form-control" wire:model="precio"
                                    name="precio" id="precio" placeholder="Precio de productos con tipo de precio otros">
                                @error('iva')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="row d-flex align-items-center">
                            <div class="col-md-4">
                                <label for="iva" class="col-form-label">Unidades por caja</label>
                                <input type="number" class="form-control" wire:model="unidades_por_caja"
                                    name="unidades_por_caja" id="iva" placeholder="Porcentaje de IVA...">
                                @error('iva')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="col-md-4">
                                <label for="iva" class="col-form-label">Cajas por pallet</label>
                                <input type="number" class="form-control" wire:model="cajas_por_pallet"
                                    name="cajas_por_pallet" id="iva" placeholder="Porcentaje de IVA...">
                                @error('iva')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="col-md-4">
                                <label for="precio" class="col-form-label">Stock de seguridad (en cajas)</label>
                                <input type="number" class="form-control" wire:model="stock_seguridad" name="stock_seguridad"
                                    id="stock_seguridad" placeholder="Stock de seguridad">
                                @error('precio')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="row d-flex align-items-center">
                            <div class="col-md-4">
                                <label for="descripcion" class="col-form-label">Descripción del producto</label>
                                <textarea class="form-control" wire:model="descripcion" name="descripcion" id="descripcion"
                                    placeholder="Descripción del producto"></textarea>
                                @error('iva')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="col-md-4">
                                <label for="descripcion" class="col-form-label">Materiales usados para su
                                    producción</label>
                                <textarea class="form-control" wire:model="materiales" name="materiales" id="materiales"
                                    placeholder="Materiales del producto"></textarea>
                                @error('iva')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="col-md-4">
                                <label for="medidas_botella" class="col-form-label">Medidas de la botella</label>
                                <textarea class="form-control" wire:model="medidas_botella" name="medidas_botella" id="medidas_botella"
                                    placeholder="?? x ?? mm"></textarea>
                                @error('precio')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="row d-flex align-items-center justify-content-center">
                            <div class="col-md-4">
                                <label for="temp_conservacion" class="col-form-label">Temperatura de
                                    conservación</label>
                                <textarea class="form-control" wire:model="temp_conservacion" name="temp_conservacion" id="temp_conservacion"
                                    placeholder="??-?? ºC"></textarea>
                                @error('iva')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="col-md-2 align-items-center">
                                <label for="descripcion" class="col-form-label">Caducidad</label><br>
                                <label for="caducidad" class="col-form-label"><input type="radio"
                                        wire:model="caducidad" name="caducidad" id="caducidad" value="Sí tiene">
                                    Sí tiene</label>
                            </div>
                            <div class="col-md-2 align-items-center">
                                <label for="descripcion" class="col-form-label">&nbsp;</label><br>
                                <label for="caducidad" class="col-form-label"><input type="radio"
                                        wire:model="caducidad" name="caducidad" id="caducidad" value="No tiene">
                                    No tiene</label>
                                @error('iva')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="col-md-4">
                                <label for="medidas_botella" class="col-form-label">Ingredientes del producto</label>
                                <textarea class="form-control" wire:model="ingredientes" name="ingredientes" id="ingredientes"
                                    placeholder="Ingredientes del producto"></textarea>
                                @error('precio')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="row d-flex align-items-center justify-content-center">
                            <div class="col-md-4">
                                <label for="alergenos" class="col-form-label">Alérgenos del producto</label>
                                <textarea class="form-control" wire:model="alergenos" name="alergenos" id="alergenos"
                                    placeholder="??-?? ºC"></textarea>
                                @error('iva')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="col-md-4">
                                <label for="proceso_elaboracion" class="col-form-label">Proceso de elaboración</label>
                                <textarea class="form-control" wire:model="proceso_elaboracion" name="proceso_elaboracion" id="proceso_elaboracion"
                                    placeholder="Proceso de elaboración"></textarea>
                                @error('precio')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="col-md-4">
                                <label for="info_nutricional" class="col-form-label">Información nutricional (por 100gr)</label>
                                <textarea class="form-control" wire:model="info_nutricional" name="info_nutricional" id="info_nutricional"
                                    placeholder="Ingredientes del producto"></textarea>
                                @error('precio')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="row d-flex align-items-center justify-content-center">
                            <div class="col-md-3">
                                <label for="peso_neto_unidad" class="col-form-label">Peso neto por unidad (en gramos)</label>
                                <input type="number" class="form-control" wire:model="peso_neto_unidad"
                                    name="peso_neto_unidad" id="peso_neto_unidad" placeholder="Peso...">
                                @error('iva')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="col-md-3">
                                <label for="grad_alcohol" class="col-form-label">Grado de alcohol (% por volumen)</label>
                                <input type="number" class="form-control" wire:model="grad_alcohol"
                                    name="grad_alcohol" id="grad_alcohol" placeholder="Grado de alcohol...">
                                @error('iva')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="col-md-3">
                                <label for="domicilio_fabricante" class="col-form-label">Domicilio de fabricación</label>
                                <input type="text" class="form-control" wire:model="domicilio_fabricante"
                                name="domicilio_fabricante" id="domicilio_fabricante" placeholder="Domicilio...">
                                @error('precio')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                            @if($ivas)
                                <div class="col-md-3">
                                    <label for="iva" class="col-form-label">Seleccione Iva</label>
                                    <select class="form-control" name="iva" id="select-iva"
                                        wire:model="iva_id">
                                        @foreach ($ivas as $iva)
                                            <option value="{{ $iva->id }}">{{ $iva->iva }}%</option>
                                        @endforeach
                                    </select>
                                    @error('IVA')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            @endif
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
                                producto </button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card m-b-30">
                <div class="card-body">
                    <h5>Imagen del producto</h5>
                    @if ($foto_ruta)
                        <div class="mb-3 row d-flex justify-content-center">
                            <div class="col">
                                <img src="{{ $foto_ruta->temporaryUrl() }}"
                                    style="max-width: 100% !important; text-align: center">
                            </div>
                        </div>
                    @endif
                    <div class="mb-3 row d-flex align-items-center">
                        <div class="col-sm-12">
                            <input type="file" class="form-control" wire:model="foto_ruta" name="foto_ruta"
                                id="foto_ruta" placeholder="Imagen del producto...">
                            @error('nombre')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>

            <div class="card m-b-30">
                {{-- preguntar si es pack con check --}}
                <div class="card-body">
                    <label for="is_pack" class="col-form-label">¿Es un pack?</label>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" wire:model="is_pack" name="is_pack" id="is_pack_si" value="1">
                        <label class="form-check-label" for="is_pack_si">Sí</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" wire:model="is_pack" name="is_pack" id="is_pack_no" value="0">
                        <label class="form-check-label" for="is_pack_no">No</label>
                    </div>
                </div>

                @if($is_pack)
                    <div class="card-body">
                        <h5>Seleccionar productos <span class="text-danger">Normales</span> para el pack</h5>
                        <small>*Estos no son productos marketing y tienen stock en almacén normal*</small>
                        <div class="input-group mb-3">
                            <div class="input-group-prepend">
                                <span class="input-group-text" id="basic-addon1">
                                    <i class="fa fa-search"></i>
                                </span>
                            </div>
                            <input type="text" class="form-control" placeholder="Buscar productos..." wire:model="searchTerm" aria-label="Buscar productos" aria-describedby="basic-addon1">
                        </div>

                        <ul class="list-group mb-3">
                            @foreach($this->filteredProductos as $producto)
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    {{ $producto->nombre }}
                                    <button type="button" class="btn btn-primary btn-sm" wire:click="agregarProducto({{ $producto->id }})" {{ in_array($producto->id, $productosSeleccionados) ? 'disabled' : '' }}>Añadir</button>
                                </li>
                            @endforeach
                        </ul>

                        <h5>Productos seleccionados</h5>
                        <ul class="list-group">
                            @foreach($productosSeleccionados as $productoId)
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    {{ $productosDisponibles->find($productoId)->nombre }}
                                    <button type="button" class="btn btn-danger btn-sm" wire:click="eliminarProducto({{ $productoId }})">Eliminar</button>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                    <div class="card-body">
                        <h5>Seleccionar productos <span class="text-danger">marketing</span> para el pack</h5>
                        <small>*Estos son productos marketing y tienen stock en almacén marketing*</small>
                        <div class="input-group mb-3">
                            <div class="input-group-prepend">
                                <span class="input-group-text" id="basic-addon1">
                                    <i class="fa fa-search"></i>
                                </span>
                            </div>
                            <input type="text" class="form-control" placeholder="Buscar productos..." wire:model="searchTerm2" aria-label="Buscar productos" aria-describedby="basic-addon1">
                        </div>

                        <ul class="list-group mb-3">
                            @foreach($this->filteredProductosMarketing as $producto)
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    {{ $producto->nombre }}
                                    <button type="button" class="btn btn-primary btn-sm" wire:click="agregarProductoMarketing({{ $producto->id }})" {{ in_array($producto->id, $productosMarketingSeleccionados) ? 'disabled' : '' }}>Añadir</button>
                                </li>
                            @endforeach
                        </ul>

                        <h5>Productos seleccionados</h5>
                        <ul class="list-group">
                            @foreach($productosMarketingSeleccionados as $productoId)
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    {{ $productosMarketingDisponibles->find($productoId)->nombre }}
                                    <button type="button" class="btn btn-danger btn-sm" wire:click="eliminarProductoMarketing({{ $productoId }})">Eliminar</button>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @endif
            </div>

            
        </div>
    </div>
</div>
    @section('scripts')
        <script>
            $("#alertaGuardar").on("click", () => {
                Swal.fire({
                    title: '¿Estás seguro?',
                    text: 'Pulsa el botón de confirmar para guardar el producto.',
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
