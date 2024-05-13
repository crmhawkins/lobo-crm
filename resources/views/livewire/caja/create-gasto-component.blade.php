<div class="container-fluid">
    <div class="page-title-box">
        <div class="row align-items-center">
            <div class="col-sm-6">
                <h4 class="page-title">AÑADIR MOVIMIENTO DE CAJA (GASTO)</span></h4>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-right">
                    <li class="breadcrumb-item"><a href="javascript:void(0);">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="javascript:void(0);">Caja</a></li>
                    <li class="breadcrumb-item active">Añadir movimiento de gasto</li>
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

                        <div class="mb-3 row d-flex align-items-center ">
                            <div class="col-sm-4">
                                <label for="Proveedor" class="col-sm-12 col-form-label">Proveedor</label>
                                <div class="col-md-12" x-data="" x-init="$('#select2-monitor').select2();
                                $('#select2-monitor').on('change', function(e) {
                                    var data = $('#select2-monitor').select2('val');
                                    @this.set('poveedor_id', data);
                                });" wire:key='rand()'>
                                    <select class="form-control" name="poveedor_id" id="select2-monitor"
                                        wire:model.lazy="poveedor_id">
                                        <option value="0">-- ELIGE UN PROVEEDOR
                                            --
                                        </option>
                                        @foreach ($poveedores as $poveedor)
                                            <option value="{{ $poveedor->id }}">
                                                {{ $poveedor->nombre }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                                <div class="col-sm-4">
                                    <label for="importe" class="col-sm-12 col-form-label">Departamento</label>
                                    <select class="form-control" name="departamento" id="departamento" wire:model="departamento">
                                        <option value="0">-- ELIGE UN DEPARTAMENTO --</option>
                                        <option value="administracion">Administración</option>
                                        <option value="rrhh">RRHH</option>
                                        <option value="marketing">Marketing</option>
                                        <option value="comercial">Comercial</option>
                                        <option value="produccion">Producción</option>
                                        <option value="patrocinios">Patrocinios</option>
                                        <option value="exportacion">Exportación</option>
                                    </select>
                                </div>
                                <div class="col-sm-4">
                                    <label for="importe" class="col-sm-12 col-form-label">Delegación</label>
                                    <select class="form-control" name="delegacion_id" id="delegacion_id" wire:model="delegacion_id">
                                        <option value="0">-- ELIGE UNA DELEGACIÓN --</option>
                                        @foreach ($delegaciones as $delegacion)
                                            <option value="{{ $delegacion->id }}">
                                                {{ $delegacion->nombre }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                        </div>

                        {{-- <div class="mb-3 row d-flex align-items-center">
                            <label for="estado" class="col-sm-12 col-form-label">Estado</label>
                            <div class="col-sm-10">
                                <select class="form-control" name="estado" id="estado"  wire:model.lazy="estado">
                                    <option value="Pendiente">Pendiente</option>
                                    <option value="Pagado">Pagado</option>
                                    <option value="Vencido">Vencido</option>
                                </select>
                            </div>
                        </div> --}}
                        

                        <div class="mb-3 row d-flex align-items-center justify-content-center">
                            <div class="col-sm-3">
                                <label for="importe" class="col-sm-12 col-form-label">Importe Neto</label>
                                <input type="number"  step="0.1" class="form-control" wire:model="importe" nombre="importe"
                                    id="importe" placeholder="Importe" wire:change="calcularTotal()">
                            </div>
                            <div class="col-sm-3">
                                <label for="importe" class="col-sm-12 col-form-label">% Iva</label>
                                <input type="number"  step="0.1" class="form-control" wire:model="iva" nombre="iva"
                                    id="iva" placeholder="iva" wire:change="calcularTotal()">
                            </div>
                            <div class="col-sm-3">
                                <label for="importe" class="col-sm-12 col-form-label">Importe Iva</label>
                                <input type="number"  step="0.1" class="form-control" wire:model="importeIva" nombre="importeIva"
                                    id="importeIva" placeholder="Importe Iva" wire:change="calcularTotal()">
                            </div>
                            <div class="col-sm-3">
                                <label for="importe" class="col-sm-12 col-form-label">% Retención</label>
                                <input type="number"  step="0.1" class="form-control" wire:model="retencion" nombre="retencion"
                                    id="retencion" placeholder="retencion" wire:change="calcularTotal()">
                            </div>
                            
                        </div>
                        <div class="mb-3 row d-flex align-items-center justify-content-center">
                            <div class="col-sm-3">
                                
                                <label for="fecha" class="col-sm-12 col-form-label">Fecha vencimiento</label>
                                <input type="date" class="form-control" wire:model="fecha_vencimiento" nombre="fecha_vencimiento"
                                    id="fecha_vencimiento" placeholder="fecha_vencimiento...">
                            </div>
                            <div class="col-sm-3">
                                <label for="fecha" class="col-sm-12 col-form-label">Fecha de pago</label>
                                <input type="date" class="form-control" wire:model="fecha_pago" nombre="fecha_pago"
                                id="fecha_pago" placeholder="fecha_pago...">
                            </div>
                            <div class="col-sm-3">
                                <label for="fecha" class="col-sm-12 col-form-label">Fecha</label>
                                <input type="date" class="form-control" wire:model="fecha" nombre="fecha"
                                    id="fecha" placeholder="Nombre de la categoría...">
                            </div>
                            <div class="col-sm-3">
                                <label for="pago" class="col-sm-12 col-form-label">Método de pago</label>
                                <input type="text" class="form-control" wire:model="metodo_pago" nombre="metodo_pago"
                                    id="metodo_pago" placeholder="Método de pago...">
                            </div>

                        </div>
                        <div class="mb-3 row d-flex align-items-center ">

                            <div class="col-sm-3">
                                <label for="importe" class="col-sm-12 col-form-label">% Descuento</label>
                                <input type="number"  step="0.1" class="form-control" wire:model="descuento" nombre="descuento"
                                    id="descuento" placeholder="descuento" wire:change="calcularTotal()">
                            </div>
                            <div class="col-sm-3">
                                <label for="importe" class="col-sm-12 col-form-label">Total</label>
                                <input type="number"  step="0.1" class="form-control" wire:model="total" nombre="total"
                                    id="total" placeholder="total">
                            </div>
                            <div class="col-sm-3">
                                <label for="pago" class="col-sm-12 col-form-label">Cuenta</label>
                                <input type="text" class="form-control" wire:model="cuenta" nombre="cuenta"
                                    id="cuenta" placeholder="Cuenta...">
                            </div>
                        </div>
                        
                        {{-- <div class="mb-3 row d-flex align-items-center">
                            <label for="banco" class="col-sm-12 col-form-label">Banco</label>
                            <div class="col-sm-10">
                                <select class="form-control" name="banco" wire:model="banco">
                                <option value="0">-- ELIGE UN BANCO --</option>
                                <option value="1">Santander</option>
                                <option value="2">CaixaBank</option>
                            </select>
                            </div>
                        </div> --}}
                        <div class="mb-3 row d-flex align-items-center">
                            <label for="nombre" class="col-sm-12 col-form-label">Detalle</label>
                            <div class="col-sm-10">
                                <textarea wire:model="descripcion" nombre="descripcion" id="descripcion" placeholder="Nombre de la categoría..." rows="4" cols="150"></textarea>
                            </div>
                        </div>
                        <div >
                            <label for="documento" class="col-sm-12 col-form-label">Documento Adjunto</label>
                            <input type="file" class="btn btn-info text-dark" wire:model="documento">
                         
                            @error('documento') <span class="error">{{ $message }}</span> @enderror
                         
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
                            <button class="w-100 btn btn-success mb-2" id="alertaGuardar">Guardar nuevo Gasto </button>
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
                text: 'Pulsa el botón de confirmar para guardar la nueva categoría.',
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
