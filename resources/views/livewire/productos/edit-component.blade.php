
@php
$EsAdmin = Auth::user()->isAdmin();
$canEdit = $EsAdmin; //|| $estado == 1;
@endphp
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
                    <form wire:submit.prevent="update">
                        <input type="hidden" name="csrf-token" value="{{ csrf_token() }}">
                        <div class="row d-flex align-items-center">
                            <div class="col-md-4">
                                <label for="nombre" class="col-form-label" >Nombre del producto</label>
                                <input type="text" class="form-control" wire:model="nombre" name="nombre"
                                    id="nombre" placeholder="Nombre del producto..."  @if(!$canEdit) disabled @endif>
                                @error('nombre')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="col-md-4">
                            <label for="tipo_precio">Tipo de precio</label>
                                <select class="form-control" name="tipo_precio" id="select-tipo_precio"
                                    wire:model="tipo_precio" @if(!$canEdit) disabled @endif>
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
                                    name="precio" id="precio" placeholder="Precio de productos con tipo de precio otros"  @if(!$canEdit) disabled @endif>
                                @error('iva')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="row d-flex align-items-center">
                            <div class="col-md-4">
                                <label for="iva" class="col-form-label">Unidades por caja</label>
                                <input type="number" class="form-control" wire:model="unidades_por_caja"
                                    name="unidades_por_caja" id="iva" placeholder="Porcentaje de IVA..."  @if(!$canEdit) disabled @endif>
                                @error('unidades_por_caja')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="col-md-4">
                                <label for="iva" class="col-form-label">Cajas por pallet</label>
                                <input type="number" class="form-control" wire:model="cajas_por_pallet"
                                    name="cajas_por_pallet" id="iva" placeholder="Porcentaje de IVA..."  @if(!$canEdit) disabled @endif>
                                @error('cajas_por_pallet')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="col-md-4">
                                <label for="precio" class="col-form-label">Stock de seguridad (en cajas)</label>
                                <input type="number" class="form-control" wire:model="stock_seguridad" name="stock_seguridad"
                                    id="stock_seguridad" placeholder="Stock de seguridad"  @if(!$canEdit) disabled @endif>
                                @error('precio')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="row d-flex align-items-center">
                            <div class="col-md-4">
                                <label for="descripcion" class="col-form-label">Descripción del producto</label>
                                <textarea class="form-control" wire:model="descripcion" name="descripcion" id="descripcion"
                                    placeholder="Descripción del producto"  @if(!$canEdit) disabled @endif></textarea>
                                @error('iva')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="col-md-4">
                                <label for="descripcion" class="col-form-label">Materiales usados para su
                                    producción</label>
                                <textarea class="form-control" wire:model="materiales" name="materiales" id="materiales"
                                    placeholder="Materiales del producto"  @if(!$canEdit) disabled @endif></textarea>
                                @error('iva')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="col-md-4">
                                <label for="medidas_botella" class="col-form-label">Medidas de la botella</label>
                                <textarea class="form-control" wire:model="medidas_botella" name="medidas_botella" id="medidas_botella"
                                    placeholder="?? x ?? mm"  @if(!$canEdit) disabled @endif></textarea>
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
                                    placeholder="??-?? ºC"  @if(!$canEdit) disabled @endif></textarea>
                                @error('iva')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="col-md-2 align-items-center">
                                <label for="descripcion" class="col-form-label">Caducidad</label><br>
                                <label for="caducidad" class="col-form-label"><input type="radio"
                                        wire:model="caducidad" name="caducidad" id="caducidad" value="Sí tiene"  @if(!$canEdit) disabled @endif>
                                    Sí tiene</label>
                            </div>
                            <div class="col-md-2 align-items-center">
                                <label for="descripcion" class="col-form-label">&nbsp;</label><br>
                                <label for="caducidad" class="col-form-label"><input type="radio"
                                        wire:model="caducidad" name="caducidad" id="caducidad" value="No tiene"  @if(!$canEdit) disabled @endif>
                                    No tiene</label>
                                @error('iva')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="col-md-4">
                                <label for="medidas_botella" class="col-form-label">Ingredientes del producto</label>
                                <textarea class="form-control" wire:model="ingredientes" name="ingredientes" id="ingredientes"
                                    placeholder="Ingredientes del producto"  @if(!$canEdit) disabled @endif></textarea>
                                @error('precio')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="row d-flex align-items-center justify-content-center">
                            <div class="col-md-4">
                                <label for="alergenos" class="col-form-label">Alérgenos del producto</label>
                                <textarea class="form-control" wire:model="alergenos" name="alergenos" id="alergenos"
                                    placeholder="??-?? ºC"  @if(!$canEdit) disabled @endif></textarea>
                                @error('iva')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="col-md-4">
                                <label for="proceso_elaboracion" class="col-form-label">Proceso de elaboración</label>
                                <textarea class="form-control" wire:model="proceso_elaboracion" name="proceso_elaboracion" id="proceso_elaboracion"
                                    placeholder="Proceso de elaboración"  @if(!$canEdit) disabled @endif></textarea>
                                @error('precio')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="col-md-4">
                                <label for="info_nutricional" class="col-form-label">Información nutricional (por 100gr)</label>
                                <textarea class="form-control" wire:model="info_nutricional" name="info_nutricional" id="info_nutricional"
                                    placeholder="Ingredientes del producto"  @if(!$canEdit) disabled @endif></textarea>
                                @error('precio')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="row d-flex align-items-center justify-content-center">
                            <div class="col-md-3">
                                <label for="peso_neto_unidad" class="col-form-label">Peso neto por unidad (en gramos)</label>
                                <input type="number" class="form-control" wire:model="peso_neto_unidad"
                                    name="peso_neto_unidad" id="peso_neto_unidad" placeholder="Porcentaje de IVA..."  @if(!$canEdit) disabled @endif>
                                @error('iva')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="col-md-3">
                                <label for="grad_alcohol" class="col-form-label">Grado de alcohol (% por volumen)</label>
                                <input type="number" class="form-control" wire:model="grad_alcohol"
                                    name="grad_alcohol" id="grad_alcohol" placeholder="Porcentaje de IVA..." @if(!$canEdit) disabled @endif>
                                @error('iva')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="col-md-3">
                                <label for="domicilio_fabricante" class="col-form-label">Domicilio de fabricación</label>
                                <input type="text" class="form-control" wire:model="domicilio_fabricante"
                                name="domicilio_fabricante" id="domicilio_fabricante" placeholder="Porcentaje de IVA..."  @if(!$canEdit) disabled @endif>
                                @error('precio')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                            @if($ivas)
                                <div class="col-md-3">
                                    <label for="iva" class="col-form-label">Seleccione Iva</label>
                                    <select class="form-control" name="iva" id="select-iva"
                                        wire:model="iva_id" @if(!$canEdit) disabled  @endif>
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

                </div>
                </form>
                <div class="card m-b-30">
                    <div class="card-body">
                        <h5>Costes del producto</h5>
                        <div class="row">
                            <div class="col-12">
                                <div class="input-group mb-3">
                                    <input type="number" class="form-control" wire:model="nuevoCoste" placeholder="Nuevo coste">
                                    <div class="input-group-append">
                                        <button class="btn btn-primary" wire:click="agregarCoste">Añadir</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Fecha</th>
                                        <th>Coste</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($costes as $coste)
                                        <tr>
                                            <td>    <input type="date" class="form-control" 
                                                                wire:model="costesEditados.{{ $coste->id }}.fecha"></td>
                                            <td>
                                                <input type="number" class="form-control" 
                                                       wire:model="costesEditados.{{ $coste->id }}.coste">
                                            </td>
                                            <td>
                                                <button class="btn btn-sm btn-success" 
                                                        wire:click="actualizarCoste({{ $coste->id }})">
                                                    <i class="fas fa-save"></i>
                                                </button>
                                                <button class="btn btn-sm btn-danger" 
                                                        wire:click="eliminarCoste({{ $coste->id }})">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @if($canEdit)
            <div class="col-md-3 justify-content-center">
                <div class="card m-b-30">
                    <div class="card-body">
                        <h5>Acciones</h5>
                        <div class="row">
                            <div class="col-12">
                                <button class="w-100 btn btn-success mb-2" id="alertaGuardar">Guardar producto</button>
                            </div>
                            <div class="col-12">
                                <button class="w-100 btn btn-danger mb-2" wire:click="destroy">Borrar producto</button>
                            </div>
                        </div>
                    </div>
                </div>

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
                            <h5>Seleccionar productos <span class="text-danger">normales</span> para el pack</h5>
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
        @endif
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
