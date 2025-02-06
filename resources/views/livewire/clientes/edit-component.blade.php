@php
$EsAdmin = Auth::user()->isAdmin();
$canEdit = $EsAdmin; //|| $estado == 1;
@endphp
{{-- {{ var_dump($eventoServicios) }} --}}
<div class="container-fluid">
    <style>
        @media(max-width: 756px){
                .select2{
                    width: 100% !important;
                }
            }
    </style>
    <div class="page-title-box">
        <div class="row align-items-center">
            <div class="col-sm-6 d-flex align-items-center gap-2">
                <h4 class=" col-sm-6 page-title">CLIENTE <span style="text-transform: uppercase">{{$nombre}}</span> </h4>
                <div class="col-sm-6 mt-2 mx-5 d-flex flex-column">
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
            <div class="col-sm-6">
                <ol class="breadcrumb float-right">
                    <li class="breadcrumb-item"><a href="javascript:void(0);">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="javascript:void(0);">Clientes</a></li>
                    <li class="breadcrumb-item active">Cliente {{$nombre}}</li>
                </ol>
            </div>
        </div> <!-- end row -->
    </div>
    <!-- end page-title -->

    <div wire:ignore.self class="modal fade" id="addEmailModal" tabindex="-1" role="dialog">
        <div class="modal-dialog"
            style="min-width: 25vw !important; align-self: center !important; margin-top: 0 !important;">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Añadir Email</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    
                    <div class="form-group row">
                        <label for="fecha_salida" class="col-sm-4 col-form-label">email</label>
                        <div class="col-sm-8">
                            <input type="string" class="form-control" id="fecha_salida" wire:model="emailAnadir">
                        </div>
                    </div>
                    
                        <button wire:click="anadirEmail()" class="btn btn-success mt-2">Añadir email</button>

                </div>

                @if($emails)
                    <div class="modal-body">
                        <table class="table table-bordered table-hover">
                            <thead>
                                <tr>
                                    <th>Email</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($emails as $index => $email)
                                    <tr>
                                        <td>{{ $email }}</td>
                                        <td>
                                            <button wire:click="eliminarEmail({{ $index }})" class="btn btn-danger">Eliminar</button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>
    @php
    $mostrarElemento = Auth::user()->isAdmin();
    @endphp
    <div class="row" style="align-items: start !important">
        <div class="col-md-9">
            <div class="card m-b-30">
                <div class="card-body">
                        <input type="hidden" name="csrf-token" value="{{ csrf_token() }}">
                        <div class="form-group row">
                            <br>
                            <div class="col-sm-12">
                                <h5 class="ms-3"
                                    style="border-bottom: 1px gray solid !important; padding-bottom: 10px !important;">
                                    Tipo de cliente</h5>
                            </div>
                            <div class="form-group col-sm-1 invisible">
                                &nbsp;
                            </div>
                            <div class="col-sm-2">
                                <div class="row tipoCliente">
                                    <div class="col-sm-12 d-inline-flex align-items-center">
                                        <input class="form-check-input mt-0" wire:model="tipo_cliente" type="radio"
                                            value="1" id="check1" @if(!$canEdit) disabled @endif>
                                        <label for="check1" class=" col-form-label">Empresa</label>
                                    </div>
                                    <div class="col-sm-12 d-inline-flex align-items-center">
                                        <input class="form-check-input mt-0" wire:model="tipo_cliente" type="radio"
                                            value="0" id="check2" @if(!$canEdit) disabled @endif>
                                        <label for="check2" class=" col-form-label">Particular</label>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group col-sm-3">
                                <label for="example-text-input" class="col-sm-12 col-form-label">Delegacion</label>
                                <div class="col-sm-12">
                                    <select wire:model="delegacion_COD" class="form-control" name="delegacion_COD" id="delegacion_COD" @if(!$canEdit) disabled @endif>
                                        <option value="" disabled selected>Selecciona una opción</option>
                                        @foreach ($delegaciones as $delegacion )
                                            <option @if(!$delegacion->created_at) style="background-color: #f8d7da; color: black;" @endif value="{{$delegacion->COD}}" @if(!$canEdit) disabled @endif>{{$delegacion->nombre}} @if(!$delegacion->created_at)  <span class="badge badge-warning">**</span> @endif</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="form-group col-sm-3">
                                <label for="example-text-input" class="col-sm-12 col-form-label">Comercial</label>
                                <div class="col-sm-12">
                                    <select wire:model="comercial_id" class="form-control" name="comercial_id" id="comercial_id" @if(!$canEdit) disabled @endif>
                                        <option value="" disabled selected>Selecciona una opción</option>
                                        <option value="0">Otro</option>
                                        @foreach ($comerciales as $comercial )
                                            <option value="{{$comercial->id}}" @if(!$canEdit) disabled @endif>{{$comercial->name}} {{$comercial->surname}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            @if ($mostrarElemento)
                            <div class="form-group col-md-3" wire:ignore>
                                <div x-data="" x-init="$('#select2-estado').select2();
                                    $('#select2-estado').on('change', function(e) {
                                    var data = $('#select2-estado').select2('val');
                                    @this.set('estado', data);
                                    });">
                                    <label for="Estado">Estado</label>
                                        <select class="form-control" wire:model="estado" name="estado" id="select2-estado"
                                        value="{{ $estado }}" @if(!$canEdit) disabled @endif>
                                            <option value="1">Pendiente</option>
                                            <option value="2">Aceptado</option>
                                            <option value="3">Rechazado</option>
                                        </select>
                                </div>
                            </div>
                            @endif
                        </div>
                        <div class="form-group row justify-content-center">
                            <div class="col-sm-12">
                                <h5 class="ms-3"
                                    style="border-bottom: 1px gray solid !important; padding-bottom: 10px !important;">
                                    Datos del cliente</h5>
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
                            <div class="form-group col-md-1 invisible">
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
                            <div class="form-group col-md-1 invisible">
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
                            <div class="form-group col-sm-1 invisible">
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
                        <div class="form-group row justify-content-center pl-3">
                            <div class="col-sm-5">
                                <div class="form-check ">
                                 <input class="form-check-input" type="checkbox" wire:model="usarDireccionEnvio" id="usarDireccionEnvio" @if(!$canEdit) disabled @endif>
                                    <label class="form-check-label" for="usarDireccionEnvio">Usar dirección de envío diferente</label>
                                </div>
                            </div>
                            <div class="col-sm-6">
                            </div>
                        </div>
                        @if($usarDireccionEnvio)

                           
                            <div class="container">
                                <div class="row">
                                    <div class="col-md-12">
                                        @if($direccionenvio != null)

                                        <h5>Direcciones de Envío (Antiguas)</h5>
                                            <table class="table table-bordered mb-2">
                                                <thead>
                                                    <tr>

                                                        <th>Dirección</th>
                                                        <th>Localidad</th>
                                                        <th>Provincia</th>
                                                        <th>Código Postal</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                   <td>{{$direccionenvio}}</td>
                                                   <td>{{$localidadenvio}}</td>
                                                   <td>{{$provinciaenvio}}</td>
                                                   <td>{{$codPostalenvio}}</td>
                                                </tbody>
                                            </table>
                                        @endif
                                        <h5>Direcciones de Envío</h5>
                                        <table class="table table-bordered">
                                            <thead>
                                                <tr>
                                                    <th>Dirección</th>
                                                    <th>Localidad</th>
                                                    <th>Provincia</th>
                                                    <th>Código Postal</th>
                                                    <th>Acciones</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($direcciones as $index => $direccion)
                                                    <tr>
                                                        <td>
                                                            <input type="text" wire:model="direcciones.{{ $index }}.direccion" class="form-control" placeholder="Dirección">
                                                        </td>
                                                        <td>
                                                            <input type="text" wire:model="direcciones.{{ $index }}.localidad" class="form-control" placeholder="Localidad">
                                                        </td>
                                                        <td>
                                                            <input type="text" wire:model="direcciones.{{ $index }}.provincia" class="form-control" placeholder="Provincia">
                                                        </td>
                                                        <td>
                                                            <input type="text" wire:model="direcciones.{{ $index }}.codigopostal" class="form-control" placeholder="Código Postal">
                                                        </td>
                                                        <td>
                                                            <button class="btn btn-danger" type="button" wire:click="removeDireccion({{ $index }})">Eliminar</button>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                        <button class="btn btn-outline-primary mt-3" type="button" wire:click="addDireccion">Añadir Dirección +</button>
                                    </div>
                                </div>
                            </div>
                        @endif

                        <div class="form-group row justify-content-center">
                            <div class="col-sm-5">
                                <label for="example-text-input" class="col-sm-12 col-form-label">Teléfono</label>
                                <div class="col-sm-12">
                                    <input type="number" wire:model="telefono" class="form-control" name="telefono"
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
                                <div class="container">
                                    <div class="row">
                                        @foreach($telefonos as $index => $telefono)
                                            <div class="col-md-12 mb-3 mt-2">
                                                <div class="input-group">
                                                    <input type="text" id="telefono_{{ $index }}" wire:model="telefonos.{{ $index }}.telefono" class="form-control" placeholder="Teléfono {{ $index + 1 }}">
                                                    <button class="btn btn-outline-danger" type="button" wire:click="removeTelefono({{ $index }})">-</button>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                    <button class="btn btn-outline-primary mt-3" type="button" wire:click="addTelefono">Añadir Teléfono +</button>
                                </div>
                            </div>
                            
                            

                            <div class="form-group col-sm-1 invisible">
                                &nbsp;
                            </div>
                            <div class="col-sm-5">
                                <label for="example-text-input" class="col-sm-12 col-form-label">Correo
                                    electrónico</label>
                                <div class="col-sm-12">
                                    <button data-toggle="modal" data-target="#addEmailModal" class="btn btn-secondary botones" style="color: white;">Añadir email</button>

                                    @if($emailsExistentes)              
                                        <ul class="p-2 mt-2" style="border: 1px solid; ">
                                            @foreach ($emailsExistentes as $email)
                                                <li style="list-style: none" class="m-1">{{ $email->email }} - <button wire:click="eliminarEmailExistente({{ $email->id }})" class="btn btn-danger">x</button></li>
                                            @endforeach
                                        </ul>
                                    @endif

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
                        
                        

                        <div wire:ignore.self class="modal fade" id="viewModal" tabindex="-1" role="dialog">
                            <div class="modal-dialog"
                                style="min-width: 25vw !important; align-self: center !important; margin-top: 0 !important;">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title">Anotaciones próximo pedido</h5>
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                    <div class="modal-body">
                                        @if (count($anotacionesProximoPedido) > 0)
                                            <ul>
                                                @foreach ($anotacionesProximoPedido as $anotacion)
                                                    <li>{{ $anotacion->anotacion }} - <span class="badge badge-warning text-uppercase">{{ $anotacion->estado }} </span> <br> <button class="btn btn-info" data-dismiss="modal" wire:click="completarAnotacion('{{ $anotacion->id }}')">Completar</button></li>
                                                @endforeach
                                            </ul>
                                        @endif
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div wire:ignore.self class="modal fade" id="addModal" tabindex="-1" role="dialog">
                            <div class="modal-dialog"
                                style="min-width: 25vw !important; align-self: center !important; margin-top: 0 !important;">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title">Añadir anotación</h5>
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                    <div class="modal-body">
                                        <textarea wire:model="anotacion" class="form-control" name="anotacion"
                                            id="anotacion" placeholder="Anotación"></textarea>
                                        <br>
                                        @error('anotacion')
                                            <span class="text-danger">{{ $message }}</span>

                                            <style>
                                                .anotacion {
                                                    color: red;
                                                }
                                            </style>
                                        @enderror
                                        <button class="btn btn-success" wire:click="addAnotacion" data-dismiss="modal">Añadir</button>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                                    </div>
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
                                        <option value="transferencia">Transferencia</option>
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
                            <div class="form-group col-sm-1 invisible">
                                &nbsp;
                            </div>
                            <div class="col-sm-5">
                                <label for="forma_pago_pref" class="col-sm-12 col-form-label">Vencimiento de factura</label>
                                <div class="col-sm-12">
                                    <select wire:model="vencimiento_factura_pref" class="form-control" name="vencimiento_factura_pref" id="vencimiento_factura_pref" @if(!$canEdit) disabled @endif>
                                        <option value="" disabled selected>Selecciona una opción</option>
                                        <option value="7">7 días</option>
                                        <option value="15">15 días</option>
                                        <option value="30">30 días</option>
                                        <option value="45">45 días</option>
                                        <option value="60">60 días</option>
                                    </select>
                                    @error('vencimiento_factura_pref')
                                        <span class="text-danger">{{ $message }}</span>

                                        <style>
                                            .vencimiento_factura_pref {
                                                color: red;
                                            }
                                        </style>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        <div class="form-group row justify-content-center">
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
                            <div class="form-group col-sm-1 invisible">
                                &nbsp;
                            </div>
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
                        </div>
                        <div class="form-group row justify-content-center">
                            <div class="col-sm-5">
                                <label for="example-text-input" class="col-sm-12 col-form-label">Porcentaje de botellas sin cargo </label>
                                <div class="col-sm-12">
                                    <input type="number" wire:model="porcentaje_bloq" class="form-control"
                                        name="porcentaje_bloq" id="porcentaje_bloq" placeholder="10" @if(!$canEdit) disabled @endif>
                                    @error('porcentaje_bloq')
                                        <span class="text-danger">{{ $message }}</span>

                                        <style>
                                            .porcentaje_bloq {
                                                color: red;
                                            }
                                        </style>
                                    @enderror
                                </div>
                            </div>
                            <div class="form-group col-sm-1 invisible">
                                &nbsp;
                            </div>
                            <div class="col-sm-5">
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
                            <div class="col-sm-5">
                                <label for="example-text-input" class="col-sm-12 col-form-label">Observaciones Descarga</label>
                                <div class="col-sm-12">
                                    <textarea type="textarea" wire:model="observaciones" class="form-control"
                                        name="observaciones" id="observaciones" placeholder="Observaciones..." @if(!$canEdit) disabled @endif></textarea>
                                    @error('observaciones')
                                        <span class="text-danger">{{ $message }}</span>

                                        <style>
                                            .observaciones {
                                                color: red;
                                            }
                                        </style>
                                    @enderror
                                </div>
                            </div>
                            <div class="form-group col-sm-1 invisible">
                                &nbsp;
                            </div>
                            <div class="col-sm-5">
                                <label for="example-text-input" class="col-sm-12 col-form-label">Crédito</label>
                                <div class="col-sm-12">
                                    <input type="number" wire:model="credito" class="form-control "
                                        name="credito" id="credito" placeholder="Crédito">
                                    @error('credito')
                                        <span class="text-danger">{{ $message }}</span>
    
                                        <style>
                                            .credito {
                                                color: red;
                                            }
                                        </style>
                                    @enderror
                                </div>
                            </div>
                            <div class="form-group row justify-content-center px-5">
                                <div class="">
                                    <div class="form-group row justify-content-center px-5 row">
                                        <div class="">
                                            <div class="row">
                                                @foreach ($productos->groupBy('grupo') as $grupo => $productosGrupo)
                                                    <div class="col-12">
                                                        <h5><strong>{{ $grupo ? $grupo : 'Sin Grupo' }}</strong></h5>
                                                    </div>
                                                    @foreach ($productosGrupo->chunk(3) as $chunk)
                                                        <div class="row">
                                                            @foreach ($chunk as $producto)
                                                                <div class="col-md-4">
                                                                    <strong>{{ $producto->nombre }}</strong>
                                                                    <input type="number" step=".01" wire:model="arrProductos.{{ $producto->id }}" class="form-control mt-2" name="{{ $producto->nombre }}" id="{{ $producto->nombre }}" placeholder="8.34">
                                                                </div>
                                                            @endforeach
                                                        </div>
                                                    @endforeach
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
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
                                    Cliente</button>
                            </div>
                            <div class="col-12">
                                <button class="w-100 btn btn-danger mb-2" id="alertaEliminar">Eliminar
                                    Cliente</button>
                            </div>
                        </div>

                        <div class="row">
                            @if(count($anotacionesProximoPedido) > 0 )
                                <div class="col-12">
                                    <button class="w-100 btn btn-info mb-2" id="verAnotaciones" data-toggle="modal" data-target="#viewModal">Ver
                                        Anotaciones próximo pedido</button>
                                </div>
                            @endif
                            <div class="col-12">
                                <button class="w-100 btn btn-dark mb-2" id="añadirAnotacion" data-toggle="modal" data-target="#addModal">Añadir
                                    Anotación próximo pedido </button>
                            </div>
                        </div>

                        <div class="row">
                            <div class="dropdown col-12" style="width: 100%;">
                                <button class="btn btn-secondary dropdown-toggle col-12" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    Acuerdos Comerciales
                                </button>
                                <div class="dropdown-menu col-12" aria-labelledby="dropdownMenuButton">
                                    <!-- Primer botón: Crear nuevo acuerdo comercial -->
                                    <a class="dropdown-item bg-info " style="border: 1px solid black"  href="{{ route('acuerdos-comerciales.create', ['id' => $cliente->id]) }}">
                                        Crear acuerdo comercial
                                    </a>
                            
                                    <!-- Desplegar los acuerdos comerciales que tiene el cliente -->
                                    @foreach ($acuerdos as $acuerdo)
                                        <a class="dropdown-item bg-warning " style="border: 1px solid black" href="{{ route('acuerdos-comerciales.edit', ['id' => $acuerdo->id]) }}">
                                            Acuerdo #{{ $acuerdo->nAcuerdo }}
                                        </a>
                                    @endforeach
                                </div>
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
                text: 'Pulsa el botón de confirmar para cambiar los datos del cliente.',
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
                text: 'Pulsa el botón de confirmar para eliminar los datos del cliente. Esto es irreversible.',
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

