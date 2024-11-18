@php
$EsAdmin = Auth::user()->isAdmin();
$canEdit = $EsAdmin;
@endphp

<div class="container-fluid">
    <div class="page-title-box">
        <div class="row align-items-center">
            <div class="col-sm-6">
                <h4 class="page-title">EDITAR PRODUCTO</h4>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-right">
                    <li class="breadcrumb-item"><a href="javascript:void(0);">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="javascript:void(0);">Productos</a></li>
                    <li class="breadcrumb-item active">Editar producto</li>
                </ol>
            </div>
        </div>
    </div>
    
    <div class="row" style="align-items: start !important">
        <div class="col-md-9">
            <div class="card m-b-30">
                <div class="card-body">
                    <form wire:submit.prevent="submit">
                        <div class="row d-flex align-items-center">
                            <div class="col-md-4">
                                <label for="nombre" class="col-form-label">Nombre del producto</label>
                                <input type="text" class="form-control" wire:model="nombre" name="nombre" id="nombre" placeholder="Nombre del producto..." @if(!$canEdit) disabled @endif>
                                @error('nombre') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>
                            <div class="col-md-4">
                                <label for="peso_neto_unidad" class="col-form-label">Peso neto por unidad (en gramos)</label>
                                <input type="number" class="form-control" wire:model="peso_neto_unidad" name="peso_neto_unidad" id="peso_neto_unidad" placeholder="Peso..." @if(!$canEdit) disabled @endif>
                                @error('peso_neto_unidad') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        <div class="row d-flex align-items-center">
                            <div class="col-md-4">
                                <label for="unidades_por_caja" class="col-form-label">Unidades por caja</label>
                                <input type="number" class="form-control" wire:model="unidades_por_caja" name="unidades_por_caja" id="unidades_por_caja" placeholder="Unidades por caja..." @if(!$canEdit) disabled @endif>
                                @error('unidades_por_caja') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>
                            <div class="col-md-4">
                                <label for="cajas_por_pallet" class="col-form-label">Cajas por pallet</label>
                                <input type="number" class="form-control" wire:model="cajas_por_pallet" name="cajas_por_pallet" id="cajas_por_pallet" placeholder="Cajas por pallet..." @if(!$canEdit) disabled @endif>
                                @error('cajas_por_pallet') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        <div class="row d-flex align-items-center">
                            <div class="col-md-4">
                                <label for="descripcion" class="col-form-label">Descripción del producto</label>
                                <textarea class="form-control" wire:model="descripcion" name="descripcion" id="descripcion" placeholder="Descripción del producto..." @if(!$canEdit) disabled @endif></textarea>
                                @error('descripcion') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>
                            <div class="col-md-4">
                                <label for="materiales" class="col-form-label">Materiales usados para su producción</label>
                                <textarea class="form-control" wire:model="materiales" name="materiales" id="materiales" placeholder="Materiales del producto..." @if(!$canEdit) disabled @endif></textarea>
                                @error('materiales') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        @if($canEdit)
            <div class="col-md-3">
                <div class="card m-b-30">
                    <div class="card-body">
                        <h5>Acciones</h5>
                        <button class="w-100 btn btn-success mb-2" id="alertaGuardar">Guardar producto</button>
                        <button class="w-100 btn btn-danger mb-2" id="alertaEliminar">Eliminar producto</button>
                    </div>
                </div>

                <div class="card m-b-30">
                    <div class="card-body">
                        <h5>Imagen del producto</h5>
                        @if ($foto_ruta || $foto_rutaOld)
                            <img 
                                @if ($foto_ruta) 
                                    src="{{ $foto_ruta->temporaryUrl() }}" 
                                @else 
                                    src="{{ asset('storage/' . $foto_rutaOld) }}" 
                                @endif 
                                style="max-width: 100%;">
                        @endif
                        <input type="file" class="form-control" wire:model="foto_ruta" id="foto_ruta">
                        @error('foto_ruta') 
                            <span class="text-danger">{{ $message }}</span> 
                        @enderror
                    </div>
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

    $("#alertaEliminar").on("click", () => {
        Swal.fire({
            title: '¿Estás seguro?',
            text: 'Pulsa el botón de confirmar para eliminar el producto.',
            icon: 'warning',
            showConfirmButton: true,
            showCancelButton: true
        }).then((result) => {
            if (result.isConfirmed) {
                window.livewire.emit('confirmDelete');
            }
        });
    });
</script>
@endsection
