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
                            <div class="col-md-3">
                                <label for="peso_neto_unidad" class="col-form-label">Peso neto por unidad (en gramos)</label>
                                <input type="number" class="form-control" wire:model="peso_neto_unidad"
                                    name="peso_neto_unidad" id="peso_neto_unidad" placeholder="Peso...">
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
                            <button class="w-100 btn btn-success mb-2" id="alertaGuardar">Guardar producto </button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card m-b-30">
                <div class="card-body">
                    <h5>Imagen del producto</h5>
                    {{-- @if ($foto_ruta)
                        <div class="mb-3 row d-flex justify-content-center">
                            <div class="col">
                                <img src="{{ $foto_ruta->temporaryUrl() }}"
                                    style="max-width: 100% !important; text-align: center">
                            </div>
                        </div>
                    @endif --}}
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
