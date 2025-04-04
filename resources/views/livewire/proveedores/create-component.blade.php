{{-- {{ var_dump($eventoServicios) }} --}}
{{-- @section('content') --}}
<div class="container-fluid">
    <div class="page-title-box">
        <div class="row align-items-center">
            <div class="col-sm-6">
                <h4 class="page-title">CREAR PROVEEDOR</span></h4>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-right">
                    <li class="breadcrumb-item"><a href="javascript:void(0);">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="javascript:void(0);">Proveedores</a></li>
                    <li class="breadcrumb-item active">Crear proveedor</li>
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
                        <input type="hidden" name="id" value="{{ csrf_token() }}">
                        <div class="form-group row justify-content-center">
                            <div class="col-sm-12">
                                <h5 class="ms-3"
                                    style="border-bottom: 1px gray solid !important; padding-bottom: 10px !important;">
                                    Datos del Proveedor</h5>
                            </div>
                            <div class="col-md-5">
                                <label for="example-text-input" class="col-sm-12 col-form-label">Nombre</label>
                                <div class="col-sm-12">
                                    <input type="text" wire:model.defer="nombre" class="form-control" name="nombre"
                                        id="nombre" placeholder="Nombre">
                                    @error('nombre')
                                        <span class="text-danger">{{ $message }}</span>
                                        <style>
                                            .nombre {
                                                color: red;
                                            }
                                        </style>
                                    @enderror
                                </div>
                            </div>
                            <div class="form-group col-md-1">
                                &nbsp;
                            </div>
                            <div class="col-md-5">
                                <label for="example-text-input" class="col-sm-12 col-form-label">DNI/CIF</label>
                                <div class="col-sm-12">
                                    <input type="text" wire:model.defer="dni_cif" class="form-control" name="dni_cif"
                                        id="dni_cif" placeholder="DNI o CIF">
                                    @error('dni_cif')
                                        <span class="text-danger">{{ $message }}</span>

                                        <style>
                                            .dni_cif {
                                                color: red;
                                            }
                                        </style>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Tipo de Calle -->
                        <div class="form-group row justify-content-center">
                            <div class="col-sm-5">
                                <label for="example-text-input" class="col-sm-12 col-form-label">Dirección</label>
                                <div class="col-sm-12">
                                    <input type="text" wire:model.defer="direccion" class="form-control" name="direccion"
                                        id="direccion" placeholder="Avenida/Plaza/Calle...">
                                    @error('direccion')
                                        <span class="text-danger">{{ $message }}</span>

                                        <style>
                                            .direccion {
                                                color: red;
                                            }
                                        </style>
                                    @enderror
                                </div>
                            </div>
                            <div class="form-group col-md-1">
                                &nbsp;
                            </div>
                            <div class="col-sm-5">
                                <label for="example-text-input" class="col-sm-12 col-form-label">Provincia</label>
                                <div class="col-sm-12">
                                    <input type="text" wire:model.defer="provincia" class="form-control" name="provincia"
                                        id="provincia" placeholder="Provincia">
                                    @error('provincia')
                                        <span class="text-danger">{{ $message }}</span>

                                        <style>
                                            .provincia {
                                                color: red;
                                            }
                                        </style>
                                    @enderror
                                </div>
                            </div>

                        </div>

                        <!-- Dir Adi 1 -->
                        <div class="form-group row justify-content-center">
                            <div class="col-sm-5">
                                <label for="example-text-input" class="col-sm-12 col-form-label">Localidad</label>
                                <div class="col-sm-12">
                                    <input type="text" wire:model.defer="localidad" class="form-control"
                                        name="localidad" id="localidad" placeholder="Localidad">
                                    @error('localidad')
                                        <span class="text-danger">{{ $message }}</span>

                                        <style>
                                            .localidad {
                                                color: red;
                                            }
                                        </style>
                                    @enderror
                                </div>
                            </div>
                            <div class="form-group col-sm-1">
                                &nbsp;
                            </div>
                            <div class="col-sm-5">
                                <label for="example-text-input" class="col-sm-12 col-form-label">Código Postal</label>
                                <div class="col-sm-12">
                                    <input type="text" wire:model.defer="cod_postal" class="form-control"
                                        name="cod_postal" id="cod_postal" placeholder="Código postal">
                                    @error('cod_postal')
                                        <span class="text-danger">{{ $message }}</span>

                                        <style>
                                            .cod_postal {
                                                color: red;
                                            }
                                        </style>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        <div class="form-group row justify-content-center">
                            <div class="col-sm-5">
                                <label for="example-text-input" class="col-sm-12 col-form-label">Teléfono</label>
                                <div class="col-sm-12">
                                    <input type="text" wire:model.defer="telefono" class="form-control" name="telefono"
                                        id="telefono" placeholder="Teléfono">
                                    @error('telefono')
                                        <span class="text-danger">{{ $message }}</span>

                                        <style>
                                            .telefono {
                                                color: red;
                                            }
                                        </style>
                                    @enderror
                                </div>
                            </div>
                            <div class="form-group col-sm-1">
                                &nbsp;
                            </div>
                            <div class="col-sm-5">
                                <label for="example-text-input" class="col-sm-12 col-form-label">Correo electrónico</label>
                                <div class="col-sm-12">
                                    <input type="text" wire:model.defer="email" class="form-control" name="email"
                                        id="email" placeholder="Correo electrónico">
                                    @error('email')
                                        <span class="text-danger">{{ $message }}</span>

                                        <style>
                                            .email {
                                                color: red;
                                            }
                                        </style>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        <div class="form-group row justify-content-center">
                            <div class="col-sm-5">
                                <label for="forma_pago_pref" class="col-sm-12 col-form-label">Forma de pago preferida</label>
                                <div class="col-sm-12">
                                    <select wire:model.defer="forma_pago_pref" class="form-control" name="forma_pago_pref" id="forma_pago_pref">
                                        <option value="" disabled selected>Selecciona una opción</option>
                                        <option value="giro_bancario">Giro Bancario</option>
                                        <option value="pagare">Pagare</option>
                                        <option value="confirming">Confirming</option>
                                        <option value="otros">Otros</option>
                                    </select>
                                    @error('forma_pago_pref')
                                        <span class="text-danger">{{ $message }}</span>

                                        <style>
                                            .forma_pago_pref {
                                                color: red;
                                            }
                                        </style>
                                    @enderror
                                </div>
                            </div>
                            <div class="form-group col-sm-1">
                                &nbsp;
                            </div>
                            <div class="col-sm-5">
                                <label for="example-text-input" class="col-sm-12 col-form-label">Nº de cuenta</label>
                                <div class="col-sm-12">
                                    <input type="text" wire:model.defer="cuenta" class="form-control" name="cuenta"
                                        id="cuenta" placeholder="Cuenta bancaria">
                                    @error('cuenta')
                                        <span class="text-danger">{{ $message }}</span>

                                        <style>
                                            .cuenta {
                                                    color: red;
                                                }
                                        </style>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        <div class="form-group row justify-content-center">
                            <div class="col-sm-5" >

                                <label for="importe" class="col-sm-12 col-form-label">Cuenta Contable</label>
                                <div class="col-md-12" x-data="" x-init="
                                        $('#select2-cuenta-contable').select2();
                                        $('#select2-cuenta-contable').on('change', function(e) {
                                            var data = $('#select2-cuenta-contable').select2('val');
                                            @this.set('cuentaContable_id', data);
                                        });
                                        Livewire.hook('message.processed', (message, component) => {
                                            $('#select2-cuenta-contable').select2(); // Reinicializa Select2 cuando Livewire renderiza
                                        });
                                    " wire:key='rand()'>
                                    <select class="form-control select2" id="select2-cuenta-contable" wire:model.lazy="cuentaContable_id" wire:key = 'rand()'>                                                <option value="">-- Seleccione Cuenta Contable --</option>
                                        @foreach($cuentasContables as $grupo)
                                            <option disabled value="">- {{ $grupo['grupo']['numero'] .'. '. $grupo['grupo']['nombre'] }} -</option>
                                            @foreach($grupo['subGrupo'] as $subGrupo)
                                                <option disabled value="">-- {{ $subGrupo['item']['numero'] .'. '. $subGrupo['item']['nombre'] }} --</option>
                                                @foreach($subGrupo['cuentas'] as $cuenta)
                                                    <option value="{{ $cuenta['item']['numero'] }}">--- {{ $cuenta['item']['numero'] .'. '. $cuenta['item']['nombre'] }} ---</option>
                                                    @foreach($cuenta['subCuentas'] as $subCuenta)
                                                        <option value="{{ $subCuenta['item']['numero'] }}">---- {{ $subCuenta['item']['numero'] .'. '. $subCuenta['item']['nombre'] }} ----</option>
                                                        @foreach($subCuenta['subCuentasHija'] as $subCuentaHija)
                                                            <option value="{{ $subCuentaHija['numero'] }}">----- {{ $subCuentaHija['numero'] .'. '. $subCuentaHija['nombre'] }} -----</option>
                                                        @endforeach
                                                    @endforeach
                                                @endforeach
                                            @endforeach
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="form-group col-sm-1">
                                &nbsp;
                            </div>
                            <div class="form-group col-sm-5">
                                <label for="example-text-input" class="col-sm-12 col-form-label">Delegacion</label>
                                <div class="col-sm-12">
                                    <select wire:model="delegacion_COD" class="form-control" name="delegacion_COD" id="delegacion_COD">
                                        <option value="" disabled selected>Selecciona una opción</option>
                                        @foreach ($delegaciones as $delegacion )
                                        @if(!$delegacion->created_at)
                                            @continue
                                        @endif
                                            <option  value="{{$delegacion->COD}}">{{$delegacion->nombre}} @if(!$delegacion->created_at)  <span class="badge badge-warning">*No seleccionar*</span> @endif</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="form-group row justify-content-center">
                            <div class="col-sm-5">
                                <label for="example-text-input" class="col-sm-12 col-form-label">Cuenta contable</label>
                                <div class="col-sm-12">
                                    <input type="text" wire:model="cuenta_contable" class="form-control" name="cuenta_contable"
                                        id="cuenta_contable" placeholder="Cuenta Contable">
                                    @error('cuenta_contable')
                                        <span class="text-danger">{{ $message }}</span>

                                        <style>
                                            .cuenta_contable {
                                                color: red;
                                            }
                                        </style>
                                    @enderror
                                </div>
                            </div>
                            <div class="form-group col-sm-1">
                                &nbsp;
                            </div>
                            @if(isset($departamentos) && count($departamentos) > 0)
                            <div class="form-group col-sm-5">
                                <label for="example-text-input" class="col-sm-12 col-form-label">Departamento</label>
                                <div class="col-sm-12">
                                    <select wire:model.defer="departamento_id" class="form-control" name="departamento_id" id="departamento_id">
                                        <option value=""  selected>Selecciona una opción</option>
                                        @foreach ($departamentos as $departamento )
                                            <option value="{{$departamento->id}}">{{$departamento->nombre}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            @endif
                            
                        </div>

                        <div class="form-group row justify-content-center">
                            <div class="col-sm-11">
                                <label for="example-text-input" class="col-sm-12 col-form-label">Nota</label>
                                <div class="col-sm-12">
                                    <textarea type="textarea" wire:model.defer="nota" class="form-control"
                                        name="nota" id="nota" placeholder="Nota"></textarea>
                                    @error('nota')
                                        <span class="text-danger">{{ $message }}</span>

                                        <style>
                                            .nota {
                                                color: red;
                                            }
                                        </style>
                                    @enderror
                                </div>
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
                            <button class="w-100 btn btn-success mb-2" id="alertaGuardar">Guardar Proveedor</button>
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
                    text: 'Pulsa el botón de confirmar para guardar el proveedor.',
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
</div>
