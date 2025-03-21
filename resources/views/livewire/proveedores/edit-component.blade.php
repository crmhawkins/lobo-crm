@php
$EsAdmin = Auth::user()->isAdmin();
$canEdit = $EsAdmin; //|| $estado == 1;
@endphp
{{-- {{ var_dump($eventoServicios) }} --}}
<div class="container-fluid">
    <div class="page-title-box">
        <div class="row align-items-center gap-5">
            <div class="col-sm-12 d-flex align-items-center gap-2">
                <h4 class="page-title">PROVEEDOR <span style="text-transform: uppercase">{{$nombre}}</span></h4>
                <div class="col-sm-4 mt-2 mx-5 d-flex flex-column">
                    <label for="">Cuenta contable</label>
                    <span style="font-size: 20px; padding: 2px 5px; border-radius: 5px;" class="fw-bold text-light bg-success @if(!$cuentaContable_id ) bg-danger @endif">
                        @if(!$cuentaContable_id )
                            --- Sin cuenta contable asignada ---

                        @else

                            @foreach($cuentasContables as $grupo)
                                @foreach($grupo['subGrupo'] as $subGrupo)
                                    @foreach($subGrupo['cuentas'] as $cuenta)
                                        @if($cuenta['item']['numero'] == $cuentaContable_id)
                                            --- {{ $cuenta['item']['numero'] .'. '. $cuenta['item']['nombre'] }} ---
                                        @endif
                                        @foreach($cuenta['subCuentas'] as $subCuenta)
                                            @if($subCuenta['item']['numero'] == $cuentaContable_id)
                                                ---- {{ $subCuenta['item']['numero'] .'. '. $subCuenta['item']['nombre'] }} ----
                                            @endif
                                            @foreach($subCuenta['subCuentasHija'] as $subCuentaHija)
                                                @if($subCuentaHija['numero'] == $cuentaContable_id)
                                                    ----- {{ $subCuentaHija['numero'] .'. '. $subCuentaHija['nombre'] }} -----
                                                @endif
                                            @endforeach
                                        @endforeach
                                    @endforeach
                                @endforeach
                            @endforeach
                        @endif
                    </span>
                </div>
            </div>
            <div class="col-sm-12">
                <ol class="breadcrumb float-right">
                    <li class="breadcrumb-item"><a href="javascript:void(0);">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="javascript:void(0);">Proveedores</a></li>
                    <li class="breadcrumb-item active">Proveedor {{$nombre}}</li>
                </ol>
            </div>
        </div> <!-- end row -->
    </div>
    <!-- end page-title -->

    <div class="row" style="align-items: start !important">
        <div class="col-md-9">
            <div class="card m-b-30">
                <div class="card-body">
                    <form wire:submit.prevent="">
                        <input type="hidden" name="csrf-token" value="{{ csrf_token() }}">
                        <div class="form-group row justify-content-center">
                            <div class="col-sm-12">
                                <h5 class="ms-3"
                                    style="border-bottom: 1px gray solid !important; padding-bottom: 10px !important;">
                                    Datos del Proveedor</h5>
                            </div>
                            <div class="col-md-5">
                                <label for="example-text-input" class="col-sm-12 col-form-label">Nombre</label>
                                <div class="col-sm-12">
                                    <input type="text" wire:model="nombre" class="form-control" name="nombre"
                                        id="nombre" placeholder="Nombre" @if(!$canEdit) disabled @endif>
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
                                    <input type="text" wire:model="dni_cif" class="form-control" name="dni_cif"
                                        id="dni_cif" placeholder="DNI o CIF" @if(!$canEdit) disabled @endif>
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
                                    <input type="text" wire:model="direccion" class="form-control" name="direccion"
                                        id="direccion" placeholder="Avenida/Plaza/Calle..." @if(!$canEdit) disabled @endif>
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
                                    <input type="text" wire:model="provincia" class="form-control" name="provincia"
                                        id="provincia" placeholder="Provincia" @if(!$canEdit) disabled @endif>
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
                                    <input type="text" wire:model="localidad" class="form-control"
                                        name="localidad" id="localidad" placeholder="Localidad" @if(!$canEdit) disabled @endif>
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
                                    <input type="text" wire:model="cod_postal" class="form-control"
                                        name="cod_postal" id="cod_postal" placeholder="Código postal" @if(!$canEdit) disabled @endif>
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
                                    <input type="text" wire:model="telefono" class="form-control" name="telefono"
                                        id="telefono" placeholder="Teléfono" @if(!$canEdit) disabled @endif>
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
                                <label for="example-text-input" class="col-sm-12 col-form-label">Correo
                                    electrónico</label>
                                <div class="col-sm-12">
                                    <input type="text" wire:model="email" class="form-control" name="email"
                                        id="email" placeholder="Correo electrónico" @if(!$canEdit) disabled @endif>
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
                                    <select wire:model="forma_pago_pref" class="form-control" name="forma_pago_pref" id="forma_pago_pref" @if(!$canEdit) disabled @endif>
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
                                    <input type="text" wire:model="cuenta" class="form-control" name="cuenta"
                                        id="cuenta" placeholder="Cuenta bancaria" @if(!$canEdit) disabled @endif>
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
                            <div class="col-sm-5">
                                <label for="example-text-input" class="col-sm-12 col-form-label">Cuenta contable</label>
                                <div class="col-sm-12">
                                    <input type="text" wire:model="cuenta_contable" class="form-control" name="cuenta_contable"
                                        id="cuenta_contable" placeholder="Cuenta Contable" @if(!$canEdit) disabled @endif>
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
                            <div class="form-group col-sm-5">
                                <label for="example-text-input" class="col-sm-12 col-form-label">Delegacion</label>
                                <div class="col-sm-12">
                                    <select wire:model="delegacion_COD" class="form-control" name="delegacion_COD" id="delegacion_COD" @if(!$canEdit) disabled @endif>
                                        <option value="" disabled selected>Selecciona una opción</option>
                                        @foreach ($delegaciones as $delegacion )
                                            <option @if(!$delegacion->created_at) style="background-color: #f8d7da; color: black;" @endif value="{{$delegacion->COD}}">{{$delegacion->nombre}} @if(!$delegacion->created_at)  <span class="badge badge-warning">*No seleccionar*</span> @endif</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        @if(isset($departamentos) && count($departamentos) > 0)
                            <div class="form-group row justify-content-center">
                                <div class="form-group col-sm-5">
                                    <label for="example-text-input" class="col-sm-12 col-form-label">Departamento</label>
                                    <div class="col-sm-12">
                                        <select wire:model="departamentoSeleccionadoId" class="form-control" name="departamentoSeleccionadoId" id="departamentoSeleccionadoId" @if(!$canEdit) disabled @endif>
                                            <option value=""  selected>Selecciona una opción</option>
                                            @foreach ($departamentos as $departamento )
                                                <option value="{{$departamento->id}}">{{$departamento->nombre}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group col-sm-1">
                                    &nbsp;
                                </div>
                                <div class="form-group col-sm-5">
                                </div>
                            </div>
                        @endif
                        <div class="form-group row justify-content-center">
                            <div class="col-sm-11">
                                <label for="example-text-input" class="col-sm-12 col-form-label">Nota</label>
                                <div class="col-sm-12">
                                    <input type="textarea" wire:model="nota" class="form-control"
                                        name="nota" id="nota" placeholder="Nota" @if(!$canEdit) disabled @endif>
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
        @if($canEdit)
            <div class="col-md-3">
                <div class="card m-b-30">
                    <div class="card-body">
                        <h5>Acciones</h5>
                        <div class="row">
                            <div class="col-12">
                                <button class="w-100 btn btn-success mb-2" id="alertaGuardar">Guardar
                                    Proveedor</button>
                            </div>
                            <div class="col-12">
                                <button class="w-100 btn btn-danger mb-2" id="alertaEliminar">Eliminar
                                    Proveedor</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>

    @section('scripts')
    <script src="../assets/js/jquery.slimscroll.js"></script>
    <script>
        $("#alertaGuardar").on("click", () => {
            Swal.fire({
                title: '¿Estás seguro?',
                text: 'Pulsa el botón de confirmar para cambiar los datos del Proveedor.',
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
                text: 'Pulsa el botón de confirmar para eliminar los datos del Proveedor. Esto es irreversible.',
                icon: 'error',
                showConfirmButton: true,
                showCancelButton: true
            }).then((result) => {
                if (result.isConfirmed) {
                    window.livewire.emit('destroy');
                }
            });
        });
    </script>
    @endsection

