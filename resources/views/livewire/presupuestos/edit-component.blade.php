<div class="container-fluid">
    <script src="//unpkg.com/alpinejs" defer></script>
    <div class="page-title-box">
        <div class="row align-items-center">
            <div class="col-sm-6">
                <h4 class="page-title">EDITAR PRESUPUESTO {{ $nPresupuesto }}</span></h4>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-right">
                    <li class="breadcrumb-item"><a href="javascript:void(0);">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="javascript:void(0);">Presupuestos</a></li>
                    <li class="breadcrumb-item active">Editar Presupuesto</li>
                </ol>
            </div>
        </div> <!-- end row -->
    </div>
    <!-- end page-title -->
    <div class="row">
        <div class="col-md-9">
            <div class="card m-b-30">
                <div class="card-body">
                    <div class="form-row mb-4 justify-content-center">
                        <div class="form-group col-md-12">
                            <h5 class="ms-3"
                                style="border-bottom: 1px gray solid !important; padding-bottom: 10px !important;">Datos
                                básicos del presupuesto</h5>
                        </div>
                        <div class="form-group col-md-4">
                            <label for="nPresupuesto">Presupuesto Nº</label>
                            <input type="text" wire:model="nPresupuesto" class="form-control" name="nPresupuesto"
                                placeholder="X" disabled>
                        </div>
                        <div class="form-group col-md-3">
                            <label for="fechaEmision">Fecha de emisión</label>
                            <input type="date" wire:model.defer="fechaEmision" class="form-control"
                                wire:change='cambiarPresupuesto' name="fechaEmision" id="fechaEmision" placeholder="X">
                        </div>
                        <div class="form-group col-md-3">
                            <label for="fechaVencimiento">Fecha de vencimiento</label>
                            <input type="date" wire:model.defer="fechaVencimiento" class="form-control"
                                min="{{ $fechaEmision }}" name="fechaVencimiento" id="fechaVencimiento" placeholder="X">
                        </div>
                    </div>
                    <div class="form-row mb-4 justify-content-center">
                        <div class="form-group col-md-4" wire:ignore>
                            <div x-data="" x-init="$('#select2-estado').select2();
                            $('#select2-estado').on('change', function(e) {
                                var data = $('#select2-estado').select2('val');
                                @this.set('estado', data);
                            });">
                                <label for="fechaVencimiento">Estado</label>
                                <select class="form-control" name="estado" id="select2-estado"
                                    value="{{ $estado }}">
                                    <option value="Pendiente">Pendiente</option>
                                    <option value="Cancelado">Cancelado</option>
                                    <option value="Aceptado">Aceptado</option>
                                    <option value="Completado">Completado</option>
                                    <option value="Facturado">Facturado</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group col-md-3" wire:ignore>
                            <div x-data="" x-init="$('#select2-cat').select2();
                            $('#select2-cat').on('change', function(e) {
                                var data = $('#select2-cat').select2('val');
                                @this.set('categoria_evento_id', data);
                            });">
                                <label for="fechaVencimiento">Categoría</label>
                                <select class="form-control" name="estado" id="select2-cat"
                                    wire:model="categoria_evento_id">
                                    @foreach ($categorias_evento as $categoria)
                                        <option value="{{ $categoria->id }}">{{ $categoria->nombre }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="form-group col-md-3">
                            <label for="nPresupuesto">Gestor</label>
                            <input type="text" class="form-control" wire:model="nombreGestor" disabled>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card m-b-30">
                <div class="card-body">
                    <div class="form-row mt-3">
                        <div class="form-group col-md-12">
                            <h5 class="ms-3"
                                style="border-bottom: 1px gray solid !important; padding-bottom: 10px !important;">Datos
                                del solicitante</h5>
                        </div>
                        <div class="form-group col-md-12">
                            <div class="input-group mb-1" wire:ignore>
                                <br>
                                <span class="col-md-2">Selecciona un cliente existente</span>
                                <div class="col-md-8" x-data="" x-init="$('#select2-cliente').select2();
                                $('#select2-cliente').on('change', function(e) {
                                    var data = $('#select2-cliente').select2('val');
                                    @this.set('id_cliente', data);
                                });">
                                    <select class="form-control" name="id_cliente" id="select2-cliente">
                                        <option value="0">-- ELIGE UN CLIENTE --</option>
                                        @foreach ($clientes as $cliente)
                                            <option value="{{ $cliente->id }}"
                                                @if ($this->id_cliente == $cliente->id) selected @endif>>
                                                {{ $cliente->nombre }} {{ $cliente->apellidos }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <a href="{{ route('clientes.create') }}" class="btn btn-success w-100"
                                        target="_blank">
                                        &nbsp;Cliente nuevo</a>
                                    {{-- <button type="button" class="btn btn-success waves-effect waves-light w-100" data-toggle="modal" data-target="#myModal">Standard Modal</button> --}}

                                    <div id="myModal" class="modal fade w-100 h-100" tabindex="-1" role="dialog"
                                        aria-labelledby="myModalLabel" aria-hidden="true">
                                        <div class="modal-dialog w-100 my-0 "
                                            style="height: 100vh !important;max-width: 100% !important">
                                            <div class="modal-content" style="height: 100vh">
                                                <div class="modal-header">
                                                    <h5 class="modal-title mt-0" id="myModalLabel">Modal Heading
                                                    </h5>
                                                    <button type="button" class="close" data-dismiss="modal"
                                                        aria-label="Close">
                                                        <span aria-hidden="true">&times;</span>
                                                    </button>
                                                </div>
                                                <div class="modal-body">
                                                    <livewire:clientes.create-component :wire:key="$currentStep" />
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary waves-effect"
                                                        data-dismiss="modal">Close</button>
                                                    <button type="button"
                                                        class="btn btn-primary waves-effect waves-light">Save
                                                        changes</button>
                                                </div>
                                            </div><!-- /.modal-content -->
                                        </div><!-- /.modal-dialog -->
                                    </div><!-- /.modal -->
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="form-row">
                        @if ($id_cliente != 0 || $id_cliente != null)
                            <div class="form-row">
                                <!-- Tratamiento -->
                                <div class="form-group col-md-4">
                                    <label for="example-text-input"
                                        class="col-sm-12 col-form-label">Tratamiento</label>
                                    <div class="col-sm-10">
                                        <select class="input-group-text" name="trato" required disabled>
                                            <option class="dropdown-item" value="" disabled>Trato
                                            </option>
                                            <option class="dropdown-item" value="M">M</option>
                                            <option class="dropdown-item" value="Melle">Melle</option>
                                            <option class="dropdown-item" value="Mme">Mme</option>
                                        </select>
                                        @error('trato')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>

                                <!-- Nombre -->
                                <div class="form-group col-md-4">
                                    <label for="example-text-input" class="col-sm-12 col-form-label"
                                        disabled>Nombre</label>
                                    <div class="col-sm-10">
                                        {{-- <input class="form-control" type="text" value="Artisanal kale" id="example-text-input"> --}}
                                        <input type="text" value="{{ $clienteSeleccionado->nombre }}"
                                            class="form-control" name="nombre" aria-label="Nombre"
                                            placeholder="Nombre" disabled>
                                        @error('nombre')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>

                                <!-- Apellido -->
                                <div class="form-group col-md-4">
                                    <label for="example-text-input" class="col-sm-12 col-form-label">Apellido</label>
                                    <div class="col-sm-10">
                                        <input type="text" value="{{ $clienteSeleccionado->apellido }}"
                                            class="form-control" name="apellido" placeholder="Apellido" disabled>
                                        @error('apellido')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="form-row">
                                <!-- NIF/DNI -->
                                <div class="form-group col-md-4">
                                    <label for="example-text-input" class="col-sm-12 col-form-label"
                                        disabled>NIF/DNI</label>
                                    <div class="col-sm-10">
                                        <input type="text" value="{{ $clienteSeleccionado->nif }}"
                                            class="form-control" name="nif" placeholder="Nif" disabled>
                                        @error('nif')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>

                                <!-- Tipo de Calle -->
                                <div class="form-group col-md-4">
                                    <label for="example-text-input" class="col-sm-12 col-form-label" disabled>Tipo de
                                        Calle</label>
                                    <div class="col-sm-10">
                                        <input type="text" value="{{ $clienteSeleccionado->tipoCalle }}"
                                            class="form-control" name="tipoCalle"
                                            placeholder="Avenida/Plaza/Calle..." disabled>
                                        @error('tipoCalle')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                                <!-- Via -->
                                <div class="form-group col-md-4">
                                    <label for="example-text-input" class="col-sm-12 col-form-label"
                                        disabled>Via</label>
                                    <div class="col-sm-10">
                                        <input type="text" value="{{ $clienteSeleccionado->calle }}"
                                            class="form-control" name="calle" placeholder="Calle" disabled>
                                        @error('calle')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                                <!-- Nº -->
                                <div class="form-group col-md-4">
                                    <label for="example-text-input" class="col-sm-12 col-form-label"
                                        disabled>Nº</label>
                                    <div class="col-sm-10">
                                        <input type="number" value="{{ $clienteSeleccionado->numero }}"
                                            class="form-control" name="numero" placeholder="1" disabled>
                                        @error('numero')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>

                                <!-- Dir Adi 1 -->
                                <div class="form-group col-md-4">
                                    <label for="example-text-input" class="col-sm-12 col-form-label" disabled>Dir Adi
                                        1</label>
                                    <div class="col-sm-10">
                                        <input type="text" value="{{ $clienteSeleccionado->direccionAdicional1 }}"
                                            class="form-control" name="direccionAdicional1"
                                            placeholder="Bloque/Letra..." disabled>
                                    </div>
                                </div>

                                <!-- Dir Adi 2 -->
                                <div class="form-group col-md-4">
                                    <label for="example-text-input" class="col-sm-12 col-form-label" disabled>Dir Adi
                                        2</label>
                                    <div class="col-sm-10">
                                        <input type="text" value="{{ $clienteSeleccionado->direccionAdicional2 }}"
                                            class="form-control" name="direccionAdicional2"
                                            placeholder="Bloque/Letra..." disabled>
                                    </div>
                                </div>

                                <!-- Dir Adi 3 -->
                                <div class="form-group col-md-4">
                                    <label for="example-text-input" class="col-sm-12 col-form-label" disabled>Dir Adi
                                        3</label>
                                    <div class="col-sm-10">
                                        <input type="text" value="{{ $clienteSeleccionado->direccionAdicional3 }}"
                                            class="form-control" name="direccionAdicional3"
                                            placeholder="Bloque/Letra..." disabled>
                                    </div>
                                </div>

                                <!-- CP -->
                                <div class="form-group col-md-4">
                                    <label for="example-text-input" class="col-sm-12 col-form-label"
                                        disabled>CP</label>
                                    <div class="col-sm-10">
                                        <input type="number" value="{{ $clienteSeleccionado->codigoPostal }}"
                                            class="form-control" name="codigoPostal" placeholder="XXXXX" disabled>
                                        @error('codigoPostal')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>

                                <!-- Ciudad -->
                                <div class="form-group col-md-4">
                                    <label for="example-text-input" class="col-sm-12 col-form-label"
                                        disabled>Ciudad</label>
                                    <div class="col-sm-10">
                                        <input type="text" value="{{ $clienteSeleccionado->ciudad }}"
                                            class="form-control" name="ciudad" placeholder="Ciudad" disabled>
                                        @error('ciudad')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>

                                <!-- Confirmacion Postal -->
                                <div class="form-group col-md-4">
                                    <label for="confPostal" class="col-sm-12 col-form-label" disabled>Confirmacion
                                        Postal</label>
                                    <div class="col-sm-10">
                                        <input class="form-check-input mt-0"
                                            @if ($clienteSeleccionado->confPostal == 1) checked @endif type="checkbox"
                                            value="" name="confPostal"
                                            aria-label="Checkbox for following text input" disabled>
                                        {{-- <span class="input-group-text">Confirmacion Postal</span> --}}
                                    </div>
                                </div>

                                <!-- Telefono -->
                                <div class="form-group col-md-4">
                                    <label for="tlf1" class="col-sm-12 col-form-label" disabled>Telefono</label>
                                    <div class="col-sm-10">
                                        <input type="number" value="{{ $clienteSeleccionado->tlf1 }}"
                                            class="form-control" name="tlf1" placeholder="XXXXXXXXX" disabled>
                                        @error('tlf1')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>

                                <!-- Telefono Secundario -->
                                <div class="form-group col-md-4">
                                    <label for="tlf2" class="col-sm-12 col-form-label" disabled>Telefono
                                        Secundario</label>
                                    <div class="col-sm-10">
                                        <input type="number" value="{{ $clienteSeleccionado->tlf2 }}"
                                            class="form-control" name="tlf2" placeholder="Opcional" disabled>
                                    </div>
                                </div>

                                <!-- Telefono Adicional -->
                                <div class="form-group col-md-4">
                                    <label for="tlf3" class="col-sm-12 col-form-label" disabled>Telefono
                                        Adicional</label>
                                    <div class="col-sm-10">
                                        <input type="number" value="{{ $clienteSeleccionado->tlf3 }}"
                                            class="form-control" name="tlf3" placeholder="Opcional" disabled>
                                    </div>
                                </div>

                                <!-- Confirmacion SMS -->
                                <div class="form-group col-md-4">
                                    <label for="confSms" class="col-sm-12 col-form-label" disabled>Confirmacion
                                        SMS</label>
                                    <div class="col-sm-10">
                                        <input class="form-check-input mt-0"
                                            @if ($clienteSeleccionado->confSms == 1) checked @endif type="checkbox"
                                            value="" aria-label="Checkbox for following text input" disabled>
                                    </div>
                                </div>

                                <!-- Email -->
                                <div class="form-group col-md-4">
                                    <label for="email1" class="col-sm-12 col-form-label" disabled>Email</label>
                                    <div class="col-sm-10">
                                        <input type="text" value="{{ $clienteSeleccionado->email1 }}"
                                            class="form-control" name="email1" placeholder="Email@email.com"
                                            disabled>
                                        @error('email1')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>

                                <!-- Email Secundario -->
                                <div class="form-group col-md-4">
                                    <label for="email1" class="col-sm-12 col-form-label" disabled>Email
                                        Secundario</label>
                                    <div class="col-sm-10">
                                        <input type="text" value="{{ $clienteSeleccionado->email2 }}"
                                            class="form-control" name="email2" placeholder="email@email.com"
                                            disabled>
                                    </div>
                                </div>

                                <!-- Email Adicional -->
                                <div class="form-group col-md-4">
                                    <label for="email1" class="col-sm-12 col-form-label" disabled>Email
                                        Adicional</label>
                                    <div class="col-sm-10">
                                        <input type="text" value="{{ $clienteSeleccionado->email3 }}"
                                            class="form-control" name="email3" placeholder="Email@email.com"
                                            disabled>
                                    </div>
                                </div>

                                <!-- Confirmacion Email -->
                                <div class="form-group col-md-4">
                                    <label for="confEmail" class="col-sm-12 col-form-label" disabled>Confirmacion
                                        Email</label>
                                    <div class="col-sm-10">
                                        <input class="form-check-input mt-0"
                                            @if ($clienteSeleccionado->confEmail == 1) checked @endif type="checkbox"
                                            value="" aria-label="Checkbox for following text input" disabled>
                                    </div>
                                </div>
                            </div>
                            {{-- {{var_dump($clienteSeleccionado)}} --}}
                        @endif
                    </div>
                </div>
            </div>
            <div class="card m-b-30">
                <div class="card-body">
                    <div class="form-row">
                        <div class="form-group col-md-12">
                            <h5 class="ms-3"
                                style="border-bottom: 1px gray solid !important; padding-bottom: 10px !important;">
                                Datos del evento</h5>
                        </div>
                        <div class="form-group col-md-12">
                            <div class="input-group mb-1">
                                <br>
                                <span class="col-md-2">Selecciona un tipo de evento</span>
                                <div class="col-md-8" x-data="" x-init="$('#select2-evento').select2();
                                $('#select2-evento').on('change', function(e) {
                                    var data = $('#select2-evento').select2('val');
                                    @this.set('eventoNombre', data);
                                });" wire:ignore>
                                    <select class="form-control" name="id_cliente" id="select2-evento">
                                        <option value="0">-- ELIGE UN TIPO DE EVENTO --</option>
                                        @foreach ($tipos_evento as $tipo)
                                            <option value="{{ $tipo->id }}">
                                                {{ $tipo->nombre }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                @if ($eventoNombre == 0)
                                    <div class="col-md-2">
                                        <a href="{{ route('eventos.create') }}" class="btn btn-success w-100"
                                            target="_blank"> &nbsp;Tipo de evento nuevo</a>
                                        {{-- <button type="button" class="btn btn-success waves-effect waves-light w-100" data-toggle="modal" data-target="#myModal">Standard Modal</button> --}}
                                    </div>
                                @elseif($eventoNombre != 0)
                                    <div class="col-md-2">
                                        <a href="{{ route('eventos.edit', $eventoNombre) }}"
                                            class="btn btn-success w-100" target="_blank"> &nbsp;Editar tipo de
                                            evento</a>
                                        {{-- <button type="button" class="btn btn-success waves-effect waves-light w-100" data-toggle="modal" data-target="#myModal">Standard Modal</button> --}}
                                    </div>
                                @endif
                            </div>
                        </div>
                        @if ($eventoNombre != 0)
                            <div class="form-group col-md-4">
                                <label for="diaEvento" class="col-sm-12 col-form-label">Dia del evento</label>
                                <div class="col-sm-10">
                                    <input type="date" wire:model.lazy="diaEvento" class="form-control"
                                        name="diaEvento" id="diaEvento" placeholder="X">
                                </div>
                            </div>
                            <div class="form-group col-md-4">
                                <label for="diaFinal" class="col-sm-12 col-form-label">Dia final del
                                    evento</label>
                                <div class="col-sm-10">
                                    <input type="date" wire:model.lazy="diaFinal" class="form-control"
                                        name="diaFinal" id="diaFinal" placeholder="X">
                                </div>
                            </div>
                            <div class="form-group col-md-4">
                                <label for="eventoProtagonista" class="col-sm-12 col-form-label">Protagonistas</label>
                                <div class="col-sm-10">
                                    <input type="text" wire:model.lazy="eventoProtagonista" class="form-control"
                                        name="eventoProtagonista" id="eventoProtagonista"
                                        placeholder="Protagonistas">
                                </div>
                            </div>
                            <div class="form-group col-md-4">
                                <label for="eventoNiños" class="col-sm-12 col-form-label">Nº Niños</label>
                                <div class="col-sm-10">
                                    <input type="number" wire:model.lazy="eventoNiños" class="form-control"
                                        name="eventoNiños" id="eventoNiños" placeholder="0">
                                </div>
                            </div>

                            <div class="form-group col-md-4">
                                <label for="eventoAdulto" class="col-sm-12 col-form-label">Nº Adultos</label>
                                <div class="col-sm-10">
                                    <input type="number" wire:model.lazy="eventoAdulto" class="form-control"
                                        name="eventoAdulto" id="eventoAdulto" placeholder="0">
                                </div>
                            </div>
                            <div class="form-group col-md-4">
                                <label for="eventoContacto" class="col-sm-12 col-form-label">Contacto</label>
                                <div class="col-sm-10">
                                    <input type="text" wire:model.lazy="eventoContacto" class="form-control"
                                        name="eventoContacto" id="eventoContacto" placeholder="Contacto">
                                </div>
                            </div>
                            <div class="form-group col-md-4">
                                <label for="eventoParentesco" class="col-sm-12 col-form-label">Parentesco</label>
                                <div class="col-sm-10">
                                    <input type="text" wire:model.lazy="eventoParentesco" class="form-control"
                                        name="eventoParentesco" id="eventoParentesco" placeholder="Parentesco">
                                </div>
                            </div>
                            <div class="form-group col-md-4">
                                <label for="eventoTelefono" class="col-sm-12 col-form-label">Telefono</label>
                                <div class="col-sm-10">
                                    <input type="text" wire:model.lazy="eventoTelefono" class="form-control"
                                        name="eventoTelefono" id="eventoTelefono" placeholder="Telefono">
                                </div>
                            </div>
                            <div class="form-group col-md-4">
                                <label for="eventoLugar" class="col-sm-12 col-form-label">Lugar</label>
                                <div class="col-sm-10">
                                    <input type="text" wire:model.lazy="eventoLugar" class="form-control"
                                        name="eventoLugar" id="eventoLugar" placeholder="Lugar">
                                </div>
                            </div>
                            <div class="form-group col-md-4">
                                <label for="eventoLocalidad" class="col-sm-12 col-form-label">Localidad</label>
                                <div class="col-sm-10">
                                    <input type="text" wire:model.lazy="eventoLocalidad" class="form-control"
                                        name="eventoLocalidad" id="eventoLocalidad" placeholder="Localidad">
                                </div>
                            </div>
                            <!-- Confirmacion Email -->
                            <div class="form-group col-md-4">
                                <label for="eventoMontaje" class="col-sm-12 col-form-label">Posibilidad
                                    de Montaje</label>
                                <div class="col-sm-10">
                                    <textarea wire:model.lazy="eventoMontaje" id="eventoMontaje"></textarea>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
            <div class="card m-b-30">
                <div class="card-body">
                    <div class="form-row">
                        <div class="form-group col-md-12">
                            <h5 class="ms-3"
                                style="border-bottom: 1px gray solid !important; padding-bottom: 10px !important;">Tipo
                                de servicio a contratar</h5>
                        </div>
                        <div class="col-md-12 ms-1" style="margin-top: -10px !important;">
                            <fieldset class="row ms-1 scheduler-border justify-content-center">
                                <div class="col-sm-6 d-inline-flex align-items-center ms-1"
                                    style="margin-top: 20px !important;">
                                    <input class="form-check-input mt-0" wire:model="tipo_seleccionado"
                                        type="radio" value="pack" id="check1">
                                    <label for="check1" class="col-form-label">Pack de servicio</label>
                                </div>
                                <div class="col-sm-2 d-inline-flex align-items-center ms-auto"
                                    style="margin-top: 20px !important;">
                                    <input class="form-check-input mt-0" wire:model="tipo_seleccionado"
                                        type="radio" value="individual" id="check2">
                                    <label for="check2" class="col-form-label">Servicio individual</label>
                                </div>
                            </fieldset>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-12">
                            <h5 class="ms-3"
                                style="border-bottom: 1px gray solid !important; padding-bottom: 10px !important;">
                                Datos
                                del servicio a contratar</h5>
                        </div>
                        @if ($tipo_seleccionado == 'individual')
                            <div class="form-group col-md-6">
                                <label for="diaEvento" class="col-sm-12 col-form-label">Servicios</label>
                                <div class="col-md-12">
                                    <Select wire:model="servicio_seleccionado" class="form-control"
                                        wire:change='cambioPrecioServicio()' name="servicio_seleccionado"
                                        id="servicios">
                                        <option value="0">Selecciona un servicio.</option>
                                        @foreach ($servicios as $keys => $servicio)
                                            <option class="dropdown-item" value="{{ $servicio->id }}">
                                                {{ $servicio->nombre }}
                                            </option>
                                        @endforeach
                                    </Select>
                                </div>
                            </div>
                            <div class="form-group col-md-2">
                                <label for="precioFinalServicio" class="col-sm-12 col-form-label">Precio</label>
                                <div class="col-md-12">
                                    <input type="text" wire:model.lazy="precioFinalServicio"
                                        wire:change="cambioTiempoServicio()" class="form-control"
                                        name="precioFinalServicio" id="precioFinalServicio"
                                        placeholder="Precio final">
                                </div>
                            </div>
                            <div class="form-group col-md-2">
                                <label for="numero_monitores" class="col-sm-12 col-form-label">Monitores</label>
                                <div class="col-md-12">
                                    <input type="number" wire:model.lazy="numero_monitores"
                                        min="{{ $servicio->minMonitor }}" wire:change="cambioPrecioServicio()"
                                        class="form-control" name="numero_monitores" id="numero_monitores"
                                        placeholder="Número de monitores">
                                </div>
                            </div>
                            <div class="form-group col-md-2 text-center">
                                <label for="precioServicio" class="col-sm-12 col-form-label">&nbsp;</label>
                                <button class="btn btn-primary w-100" wire:click.prevent="addServicio">Añadir</button>
                            </div>
                            <div class="form-group col-md-2">
                                <label for="precioServicio" class="col-sm-12 col-form-label">Tiempo</label>
                                <div class="col-md-12">
                                    <input type="time" wire:model="tiempo" wire:change="cambioTiempoServicio()"
                                        class="form-control" name="tiempo" placeholder="00:00:00">
                                </div>
                            </div>
                            <div class="form-group col-md-2">
                                <label for="tiempo_montaje" class="col-sm-12 col-form-label">Tiempo
                                    montaje</label>
                                <div class="col-md-12">
                                    <input type="time" wire:model="tiempoMontaje"
                                        wire:change="cambioTiempoServicio()" class="form-control" name="tiempo"
                                        placeholder="00:00:00">
                                </div>
                            </div>
                            <div class="form-group col-md-2">
                                <label for="hora_finalizacion" class="col-sm-12 col-form-label">Tiempo
                                    desmontaje</label>
                                <div class="col-md-12">
                                    <input type="time" wire:model="tiempoDesmontaje"
                                        wire:change="cambioTiempoServicio()" class="form-control"
                                        name="hora_finalizacion" placeholder="00:00:00">
                                </div>
                            </div>
                            <div class="form-group col-md-2">
                                <label for="hora_montaje" class="col-sm-12 col-form-label">Hora
                                    montaje</label>
                                <div class="col-md-12">
                                    <input type="time" wire:model="horaMontaje"
                                        wire:change="cambioTiempoServicio()" class="form-control" name="hora_inicio"
                                        placeholder="00:00:00">
                                </div>
                            </div>
                            <div class="form-group col-md-2">
                                <label for="hora_inicio" class="col-sm-12 col-form-label">Hora
                                    inicio</label>
                                <div class="col-md-12">
                                    <input type="time" wire:model="hora_inicio"
                                        wire:change="cambioTiempoServicio()" class="form-control" name="hora_inicio"
                                        placeholder="00:00:00">
                                </div>
                            </div>

                            <div class="form-group col-md-2">
                                <label for="precioServicio" class="col-sm-12 col-form-label">Hora
                                    finalización</label>
                                <div class="col-md-12">
                                    <input type="time" wire:model="hora_finalizacion"
                                        wire:change="cambioTiempoServicio()" class="form-control"
                                        name="hora_finalizacion" placeholder="00:00:00">
                                </div>
                            </div>
                        @elseif($tipo_seleccionado == 'pack')
                            <div class="form-group col-md-10">
                                <label for="diaEvento" class="col-sm-12 col-form-label">Packs de
                                    servicios</label>
                                <div class="col-md-12">
                                    <Select wire:model="pack_seleccionado" class="form-control"
                                        name="pack_seleccionado" id="pack_seleccionado">
                                        <option value="0">Selecciona un paquete de servicios.</option>
                                        @foreach ($packs as $keys => $pack)
                                            <option class="dropdown-item" value="{{ $pack->id }}" selected>
                                                {{ $pack->nombre }}
                                            </option>
                                        @endforeach
                                    </Select>
                                </div>
                            </div>
                            <div class="form-group col-md-2 text-center">
                                <label for="precioServicio" class="col-sm-12 col-form-label">&nbsp;</label>

                                <button class="btn btn-primary w-100" wire:click.prevent="addPack()">Añadir</button>
                            </div>
                            @if ($pack_seleccionado != null)
                                @foreach ($packs->where('id', $pack_seleccionado)->first()->servicios()->get() as $keyPack => $servicio)
                                    <div class="form-group col-md-1">
                                        &nbsp;
                                    </div>
                                    <div class="form-group col-md-5">
                                        <label for="precioServicio" class="col-sm-12 col-form-label">Servicio</label>
                                        <div class="col-md-12">
                                            <input type="text" value="{{ $servicio->nombre }}"
                                                class="form-control" name="precioServicio" placeholder="Evento">
                                        </div>
                                    </div>
                                    <div class="form-group col-md-2">
                                        <label for="precioServicio" class="col-sm-12 col-form-label">Monitores</label>
                                        <div class="col-md-12">
                                            <input type="number" wire:model="preciosMonitores.{{ $keyPack }}"
                                                min="{{ $servicio->minMonitor }}"
                                                wire:init="asignarValorInicial('{{ $keyPack }}', '{{ $servicio->minMonitor }}')"
                                                wire:change='cambioPrecioPack' class="form-control"
                                                name="preciosMonitores.{{ $keyPack }}"
                                                placeholder="Número de monitores" required>
                                        </div>
                                    </div>
                                    <div class="form-group col-md-2">
                                        <label for="precioServicio" class="col-sm-12 col-form-label">Precio
                                            base</label>
                                        <div class="col-md-12">
                                            <input type="number" class="form-control" name="precioServicio"
                                                wire:change='cambioTiempoPack'
                                                wire:change='cambioPrecioPack("{{ $keyPack }}")'
                                                placeholder="Dias">
                                        </div>
                                    </div>
                                    <div class="form-group col-md-2">
                                        <label for="precioServicio" class="col-sm-12 col-form-label">Tiempo</label>
                                        <div class="col-md-12">
                                            <input type="time" wire:model="tiemposPack.{{ $keyPack }}"
                                                wire:change="cambioTiempoPack()" class="form-control" name="tiempo"
                                                placeholder="00:00:00">
                                        </div>
                                    </div>
                                    <div class="form-group col-md-1">
                                        <a href="{{ route('servicios.edit', $servicio->id) }}" type="button"
                                            class="btn btn-circle btn-primary"
                                            target="_blank">{{ $keyPack + 1 }}</a>
                                    </div>
                                    <div class="form-group col-md-3">
                                        <label for="precioServicio" class="col-sm-12 col-form-label">Tiempo
                                            montaje</label>
                                        <div class="col-md-12">
                                            <input type="time" wire:model="tiemposMontajePack.{{ $keyPack }}"
                                                wire:change="cambioTiempoPack()" class="form-control" name="tiempo"
                                                placeholder="00:00:00">
                                        </div>
                                    </div>
                                    <div class="form-group col-md-2">
                                        <label for="precioServicio" class="col-sm-12 col-form-label">Tiempo
                                            desmontaje</label>
                                        <div class="col-md-12">
                                            <input type="time"
                                                wire:model="tiemposDesmontajePack.{{ $keyPack }}"
                                                wire:change="cambioTiempoPack()" class="form-control"
                                                name="hora_finalizacion" placeholder="00:00:00">
                                        </div>
                                    </div>
                                    <div class="form-group col-md-2">
                                        <label for="precioServicio" class="col-sm-12 col-form-label">Hora
                                            montaje</label>
                                        <div class="col-md-12">
                                            <input type="time" wire:model="horasMontajePack.{{ $keyPack }}"
                                                wire:change="cambioTiempoPack()" class="form-control"
                                                name="hora_inicio" placeholder="00:00:00">
                                        </div>
                                    </div>
                                    <div class="form-group col-md-2">
                                        <label for="precioServicio" class="col-sm-12 col-form-label">Hora
                                            inicio</label>
                                        <div class="col-md-12">
                                            <input type="time" wire:model="horasInicioPack.{{ $keyPack }}"
                                                wire:change="cambioTiempoPack()" class="form-control"
                                                name="hora_inicio" placeholder="00:00:00">
                                        </div>
                                    </div>
                                    <div class="form-group col-md-2">
                                        <label for="precioServicio" class="col-sm-12 col-form-label">Hora
                                            finalización</label>
                                        <div class="col-md-12">
                                            <input type="time"
                                                wire:model="horasFinalizacionPack.{{ $keyPack }}"
                                                wire:change="cambioTiempoPack()" class="form-control"
                                                name="hora_finalizacion" placeholder="00:00:00">
                                        </div>
                                    </div>
                                @endforeach
                                <div class="form-group col-md-12">
                                    <label for="precioServicio" class="col-sm-12 col-form-label">Precio final
                                        del pack</label>
                                    <div class="col-md-12">
                                        <input type="number" class="form-control" wire:model="precioFinalPack"
                                            placeholder="Evento">
                                    </div>
                                </div>
                            @endif
                        @else
                        @endif
                    </div>
                </div>
            </div>
            <div class="card m-b-30">
                <div class="card-body">
                    <div class="form-group col-md-12">
                        <h5 class="ms-3"
                            style="border-bottom: 1px gray solid !important; padding-bottom: 10px !important;">
                            Servicios contratados</h5>
                    </div>
                    <div class="form-group col-md-12">
                        <h6 class="ms-3"
                            style="border-bottom: 1px lightgray solid !important; padding-bottom: 10px !important;">
                            Packs de servicio</h6>
                        @if ($eventoMontaje != 1)
                            <table class="table table-striped table-bordered nowrap">
                                @foreach ($listaPacks as $packIndex => $pack)
                                    @if ($packIndex == 0)
                                        <tr>
                                            <th>Pack de servicio</th>
                                            <th>Precio final</th>
                                            <th>Monitores contratados</th>
                                            <th>Tiempo total</th>
                                            <th>Eliminar</th>
                                        </tr>
                                    @else
                                        <tr>
                                            <th class="header">Pack de servicio</th>
                                            <th class="header">Precio final</th>
                                            <th class="header">Monitores contratados</th>
                                            <th class="header">Tiempo total</th>
                                            <th class="header">Eliminar</th>
                                        </tr>
                                    @endif
                                    <tr>
                                        <td class="izquierda">{{ $packs->where('id', $pack['id'])->first()->nombre }}
                                        </td>
                                        <td>{{ $pack['precioFinal'] }} € </td>
                                        <td>{{ array_sum($pack['numero_monitores']) }} monitores</td>
                                        <td> {{ $this->sumarTiempos($packIndex) }} h </td>
                                        <td class="derecha"><button type="button" class="btn btn-sm btn-danger"
                                                wire:click.prevent="deletePack('{{ $packIndex }}')">X</button></td>
                                    </tr>
                                    <tr>
                                        <th class="header">Servicio contratado</th>
                                        <th class="header">Monitores contratados</th>
                                        <th class="header">Duración</th>
                                        <th class="header">Hora de inicio</th>
                                        <th class="header">Hora de finalización</th>
                                    </tr>
                                    @foreach ($packs->where('id', $pack['id'])->first()->servicios()->get() as $keyPack => $servicioPack)
                                        @if (
                                            $keyPack + 1 !=
                                                $packs->where('id', $pack['id'])->first()->servicios()->count())
                                            <tr>
                                                <td class="izquierda"> {{ $servicioPack->nombre }} </td>
                                                <td>{{ $pack['numero_monitores'][$keyPack] }} monitores </td>
                                                <td> {{ $pack['tiempos'][$keyPack] }} h </td>
                                                <td>({{ $pack['horas_inicio'][$keyPack] }} </td>
                                                <td class="derecha">{{ $pack['horas_finalizacion'][$keyPack] }}) </td>
                                            </tr>
                                        @else
                                            <tr>
                                                <td class="izquierda"
                                                    style="border-bottom: 1px solid black !important;">
                                                    {{ $servicioPack->nombre }} </td>
                                                <td style="border-bottom: 1px solid black !important;">
                                                    {{ $pack['numero_monitores'][$keyPack] }} monitores </td>
                                                <td style="border-bottom: 1px solid black !important;">
                                                    {{ $pack['tiempos'][$keyPack] }} h </td>
                                                <td style="border-bottom: 1px solid black !important;">
                                                    ({{ $pack['horas_inicio'][$keyPack] }} </td>
                                                <td class="derecha"
                                                    style="border-bottom: 1px solid black !important;">
                                                    {{ $pack['horas_finalizacion'][$keyPack] }})
                                                </td>
                                            </tr>
                                        @endif
                                    @endforeach
                                @endforeach
                            </table>
                            <h6 class="ms-3"
                                style="border-bottom: 1px lightgray solid !important; padding-bottom: 10px !important;">
                                Servicios individuales</h6>
                            @if (count($listaServicios) > 0)
                                <table class="table table-striped table-bordered nowrap">
                                    <tr>
                                        <th class="header">Servicio contratado</th>
                                        <th class="header">Monitores contratados</th>
                                        <th class="header">Precio</th>
                                        <th class="header">Duración</th>
                                        <th class="header">Hora de inicio</th>
                                        <th class="header">Hora de finalización</th>
                                        <th class="header">Eliminar</th>
                                    </tr>
                                    @foreach ($listaServicios as $servicioIndex => $itemServicio)
                                        @if ($servicioIndex + 1 == count($listaServicios))
                                            <tr>
                                                <td class="izquierda">
                                                    {{ $servicios->where('id', $itemServicio['id'])->first()->nombre }}
                                                </td>
                                                <td>{{ $itemServicio['numero_monitores'] }}</td>
                                                <td> {{ $itemServicio['precioFinal'] }} €</td>
                                                <td> {{ $itemServicio['tiempo'] }} h</td>
                                                <td> {{ $itemServicio['hora_inicio'] }} h</td>
                                                <td> {{ $itemServicio['hora_finalizacion'] }} h</td>
                                                <td class="derecha"><button type="button"
                                                        class="btn btn-sm btn-danger"
                                                        wire:click.prevent="deleteServicio('{{ $servicioIndex }}')">X</button>
                                                </td>
                                            </tr>
                                        @else
                                            <tr>
                                                <td class="izquierda"
                                                    style="border-bottom: 1px solid black !important;">
                                                    {{ $servicios->where('id', $itemServicio['id'])->first()->nombre }}
                                                </td>
                                                <td style="border-bottom: 1px solid black !important;">
                                                    {{ $itemServicio['numero_monitores'] }}</td>
                                                <td style="border-bottom: 1px solid black !important;">
                                                    {{ $itemServicio['precioFinal'] }} €</td>
                                                <td style="border-bottom: 1px solid black !important;">
                                                    {{ $itemServicio['tiempo'] }} h</td>
                                                <td style="border-bottom: 1px solid black !important;">
                                                    {{ $itemServicio['hora_inicio'] }} h</td>
                                                <td style="border-bottom: 1px solid black !important;">
                                                    {{ $itemServicio['hora_finalizacion'] }} h</td>
                                                <td class="derecha"
                                                    style="border-bottom: 1px solid black !important;">
                                                    <button type="button" class="btn btn-sm btn-danger"
                                                        wire:click.prevent="deleteServicio('{{ $servicioIndex }}')">X</button>
                                                </td>
                                            </tr>
                                        @endif
                                    @endforeach
                                </table>
                            @endif
                        @else
                            <table class="table table-striped table-bordered nowrap">
                                @foreach ($listaPacks as $packIndex => $pack)
                                    @if ($packIndex == 0)
                                        <tr>
                                            <th colspan="3" class="header">Pack de servicio</th>
                                            <th class="header">Precio final</th>
                                            <th class="header">Monitores contratados</th>
                                            <th colspan="2" class="header">Tiempo total</th>
                                            <th class="header">Eliminar</th>
                                        </tr>
                                    @else
                                        <tr>
                                            <th colspan="3" class="header">Pack de servicio</th>
                                            <th class="header">Precio final</th>
                                            <th class="header">Monitores contratados</th>
                                            <th colspan="2" class="header">Tiempo total</th>
                                            <th class="header">Eliminar</th>
                                        </tr>
                                    @endif
                                    <tr>
                                        <td class="izquierda" colspan="3">
                                            {{ $packs->where('id', $pack['id'])->first()->nombre }}
                                        </td>
                                        <td>{{ $pack['precioFinal'] }} € </td>
                                        <td>{{ array_sum($pack['numero_monitores']) }} monitores</td>
                                        <td colspan="2"> {{ $this->sumarTiempos($packIndex) }} h </td>
                                        <td class="derecha"><button type="button" class="btn btn-sm btn-danger"
                                                wire:click.prevent="deletePack('{{ $packIndex }}')">X</button>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th class="header">Servicio contratado</th>
                                        <th class="header">Monitores contratados</th>
                                        <th class="header">Duración</th>
                                        <th class="header">Duración del montaje</th>
                                        <th class="header">Duración del desmontaje</th>
                                        <th class="header">Hora de montaje</th>
                                        <th class="header">Hora de inicio</th>
                                        <th class="header">Hora de finalización</th>
                                    </tr>
                                    @foreach ($packs->where('id', $pack['id'])->first()->servicios()->get() as $keyPack => $servicioPack)
                                        @if (
                                            $keyPack + 1 !=
                                                $packs->where('id', $pack['id'])->first()->servicios()->count())
                                            <tr>
                                                <td class="izquierda"> {{ $servicioPack->nombre }} </td>
                                                <td>{{ $pack['numero_monitores'][$keyPack] }} monitores </td>
                                                <td> {{ $pack['tiempos'][$keyPack] }} h </td>
                                                <td> {{ $pack['tiempos_montaje'][$keyPack] }} h </td>
                                                <td> {{ $pack['tiempos_desmontaje'][$keyPack] }} h </td>
                                                <td> {{ $pack['horas_montaje'][$keyPack] }} h </td>
                                                <td>({{ $pack['horas_inicio'][$keyPack] }} </td>
                                                <td class="derecha">{{ $pack['horas_finalizacion'][$keyPack] }})
                                                </td>
                                            </tr>
                                        @else
                                            <tr>
                                                <td class="izquierda"
                                                    style="border-bottom: 1px solid black !important;">
                                                    {{ $servicioPack->nombre }} </td>
                                                <td style="border-bottom: 1px solid black !important;">
                                                    {{ $pack['numero_monitores'][$keyPack] }} monitores </td>
                                                <td style="border-bottom: 1px solid black !important;">
                                                    {{ $pack['tiempos'][$keyPack] }} h </td>
                                                <td style="border-bottom: 1px solid black !important;">
                                                    ({{ $pack['horas_inicio'][$keyPack] }} </td>
                                                <td class="derecha"
                                                    style="border-bottom: 1px solid black !important;">
                                                    {{ $pack['horas_finalizacion'][$keyPack] }})
                                                </td>
                                            </tr>
                                        @endif
                                    @endforeach
                                @endforeach
                            </table>
                            <h6 class="ms-3"
                                style="border-bottom: 1px lightgray solid !important; padding-bottom: 10px !important;">
                                Servicios individuales</h6>
                            @if (count($listaServicios) > 0)
                                <table class="table table-striped table-bordered nowrap">
                                    <tr>
                                        <th class="header">Servicio contratado</th>
                                        <th class="header">Monitores contratados</th>
                                        <th class="header">Precio</th>
                                        <th class="header">Duración</th>
                                        <th class="header">Duración del montaje</th>
                                        <th class="header">Duración del desmontaje</th>
                                        <th class="header">Hora de montaje</th>
                                        <th class="header">Hora de inicio</th>
                                        <th class="header">Hora de finalización</th>
                                        <th class="header">Eliminar</th>
                                    </tr>
                                    @foreach ($listaServicios as $servicioIndex => $itemServicio)
                                        @if ($servicioIndex + 1 == count($listaServicios))
                                            <tr>
                                                <td class="izquierda">
                                                    {{ $servicios->where('id', $itemServicio['id'])->first()->nombre }}
                                                </td>
                                                <td>{{ $itemServicio['numero_monitores'] }}</td>
                                                <td> {{ $itemServicio['precioFinal'] }} €</td>
                                                <td> {{ $itemServicio['tiempo'] }} h</td>
                                                <td> {{ $itemServicio['tiempo_montaje'] }} h</td>
                                                <td> {{ $itemServicio['tiempo_desmontaje'] }} h</td>
                                                <td> {{ $itemServicio['hora_montaje'] }}</td>
                                                <td> {{ $itemServicio['hora_inicio'] }}</td>
                                                <td> {{ $itemServicio['hora_finalizacion'] }}</td>
                                                <td class="derecha"><button type="button"
                                                        class="btn btn-sm btn-danger"
                                                        wire:click.prevent="deleteServicio('{{ $servicioIndex }}')">X</button>
                                                </td>
                                            </tr>
                                        @else
                                            <tr>
                                                <td class="izquierda"
                                                    style="border-bottom: 1px solid black !important;">
                                                    {{ $servicios->where('id', $itemServicio['id'])->first()->nombre }}
                                                </td>
                                                <td style="border-bottom: 1px solid black !important;">
                                                    {{ $itemServicio['numero_monitores'] }}</td>
                                                <td style="border-bottom: 1px solid black !important;">
                                                    {{ $itemServicio['precioFinal'] }} €</td>
                                                <td style="border-bottom: 1px solid black !important;">
                                                    {{ $itemServicio['tiempo'] }} h</td>
                                                <td style="border-bottom: 1px solid black !important;">
                                                    {{ $itemServicio['tiempo_montaje'] }} h</td>
                                                <td style="border-bottom: 1px solid black !important;">
                                                    {{ $itemServicio['tiempo_desmontaje'] }} h</td>
                                                <td style="border-bottom: 1px solid black !important;">
                                                    {{ $itemServicio['hora_montaje'] }}</td>
                                                <td style="border-bottom: 1px solid black !important;">
                                                    {{ $itemServicio['hora_inicio'] }} h</td>
                                                <td style="border-bottom: 1px solid black !important;">
                                                    {{ $itemServicio['hora_finalizacion'] }} h</td>
                                                <td class="derecha"
                                                    style="border-bottom: 1px solid black !important;">
                                                    <button type="button" class="btn btn-sm btn-danger"
                                                        wire:click.prevent="deleteServicio('{{ $servicioIndex }}')">X</button>
                                                </td>
                                            </tr>
                                        @endif
                                    @endforeach
                                </table>
                            @endif
                        @endif
                    </div>
                    <div class="form-row justify-content-center">
                        <div class="form-group col-md-3">
                            <label for="precioServicio" class="col-sm-12 col-form-label">Subtotal</label>
                            <div class="col-md-12">
                                <input type="text" wire:model.lazy="precioFinal" class="form-control"
                                    name="precioFinal" id="precioFinal" disabled placeholder="Precio final">
                            </div>
                        </div>
                        <div class="form-group col-md-3">
                            <label for="precioServicio" class="col-sm-12 col-form-label">Descuento</label>
                            <div class="col-md-12">
                                <input type="number" wire:model.lazy="descuento" class="form-control"
                                    name="descuento" id="descuento" max="{{ $this->precioFinal }}"
                                    placeholder="Precio final">
                            </div>
                        </div>
                        <div class="form-group col-md-3">
                            <label for="precioServicio" class="col-sm-12 col-form-label">Adelanto</label>
                            <div class="col-md-12">
                                <input type="number" wire:model.lazy="adelanto" class="form-control"
                                    name="adelanto" id="adelanto"
                                    max="{{ $this->precioFinal - $this->descuento }}" placeholder="Precio final">
                            </div>
                        </div>
                    </div>
                    <div class="form-group col-md-12">
                        <label for="precioServicio" class="col-sm-12 col-form-label">&nbsp;</label>
                        <h4>Total: {{ $this->precioFinal - $this->descuento }} € @if ($adelanto > 0 || $adelanto != null)
                                ( {{ $this->adelanto }} € pagado por adelantado. )
                            @endif
                        </h4>
                    </div>
                </div>
            </div>

            <div class="card m-b-30">
                <div class="card-body">
                    <div class="form-row">
                        <div class="form-group col-md-12">
                            <h5 class="ms-3"
                                style="border-bottom: 1px gray solid !important; padding-bottom: 10px !important;">
                                Sueldos de monitores y gasoil</h5>
                        </div>
                        <div class="form-group col-md-12">
                            <div class="row">
                                @foreach ($listaPacks as $packIndex => $pack)
                                    @foreach ($packs->where('id', $pack['id'])->first()->servicios()->get() as $keyPack => $servicioPack)
                                        <div class="row text-center">
                                            <div class="col-md-12">
                                                <label for="adelantoResumen"
                                                    class="col-sm-12 col-form-label">Servicio</label>
                                                <input type="text" id="adelantoResumen"
                                                    class="form-control text-center"
                                                    value="{{ $servicioPack->nombre }}" disabled>
                                            </div>
                                            @for ($i = 0; $i < $pack['numero_monitores'][$keyPack]; $i++)
                                                <div class="col-md-6">
                                                    <label for="adelantoResumen"
                                                        class="col-sm-12 col-form-label">Monitor</label>
                                                    <select class="form-control text-center"
                                                        wire:model="listaPacks.{{ $packIndex }}.id_monitores.{{ $keyPack }}.{{ $i }}"
                                                        name="servicio_seleccionado" id="monitores">
                                                        <option value="0">Selecciona un monitor.</option>
                                                        @foreach ($monitores as $keys => $monitor)
                                                            <option class="dropdown-item"
                                                                value="{{ $monitor->id }}">
                                                                {{ $monitor->nombre }}
                                                            </option>
                                                        @endforeach
                                                    </Select>
                                                </div>
                                                <div class="col-md-3">
                                                    <label for="adelantoResumen"
                                                        class="col-sm-12 col-form-label">Sueldo</label>
                                                    <input type="number" id="adelantoResumen"
                                                        wire:model="listaPacks.{{ $packIndex }}.sueldos_monitores.{{ $keyPack }}.{{ $i }}"
                                                        class="form-control text-center">
                                                </div>
                                                <div class="col-md-3">
                                                    <label for="adelantoResumen"
                                                        class="col-sm-12 col-form-label">Pendiente</label>
                                                    <input type="number"
                                                        wire:model="listaPacks.{{ $packIndex }}.pagos_pendientes.{{ $keyPack }}.{{ $i }}"
                                                        class="form-control text-center">
                                                </div>
                                            @endfor
                                        </div>
                                    @endforeach
                                @endforeach
                                @foreach ($listaServicios as $servicioIndex => $itemServicio)
                                    <div class="row text-center">
                                        <div class="col-md-12">
                                            <label for="adelantoResumen"
                                                class="col-sm-12 col-form-label">Servicio</label>
                                            <input type="text" id="adelantoResumen"
                                                class="form-control text-center"
                                                value="{{ $servicios->find($itemServicio['id'])->nombre }}" disabled>
                                        </div>
                                        @for ($i = 0; $i < $itemServicio['numero_monitores']; $i++)
                                            <div class="col-md-6">
                                                <label for="adelantoResumen"
                                                    class="col-sm-12 col-form-label">Monitor</label>
                                                <select class="form-control text-center"
                                                    wire:model="listaServicios.{{ $servicioIndex }}.id_monitores.{{ $i }}"
                                                    name="servicio_seleccionado" id="monitores">
                                                    <option value="0">Selecciona un monitor.</option>
                                                    @foreach ($monitores as $keys => $monitor)
                                                        <option class="dropdown-item" value="{{ $monitor->id }}">
                                                            {{ $monitor->nombre }}
                                                        </option>
                                                    @endforeach
                                                </Select>
                                            </div>
                                            <div class="col-md-3">
                                                <label for="adelantoResumen"
                                                    class="col-sm-12 col-form-label">Sueldo</label>
                                                <input type="number" id="adelantoResumen"
                                                    wire:model='listaServicios.{{ $servicioIndex }}.sueldo_monitores.{{ $i }}'
                                                    value="{{ $servicios->find($itemServicio['id'])->precioMonitor }}"
                                                    class="form-control text-center">
                                            </div>
                                            <div class="col-md-3">
                                                <label for="adelantoResumen"
                                                    class="col-sm-12 col-form-label">Pendiente de
                                                    pago</label>
                                                <input type="number" id="adelantoResumen"
                                                    wire:model='listaServicios.{{ $servicioIndex }}.pago_pendiente.{{ $i }}'
                                                    class="form-control text-center">
                                            </div>
                                        @endfor
                                    </div>
                                @endforeach
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card m-b-30">
                <div class="card-body">
                    <div class="form-row">
                        <div class="form-group col-md-12">
                            <h5 class="ms-3"
                                style="border-bottom: 1px gray solid !important; padding-bottom: 10px !important;">
                                Datos para la creación del contrato</h5>
                        </div>
                        <div class="col-sm-11 align-items-center ms-5">
                            <label for="observaciones" class="col-form-label">Observaciones</label>
                            <textarea class="form-control" wire:model="observaciones" id="observaciones"></textarea>
                        </div>
                        <div class="col-sm-5 align-items-center ms-5">
                            <label for="metodoPago" class="col-form-label">Método de pago</label>
                            <select class="form-control text-center" wire:model="metodoPago" name="metodoPago"
                                id="metodoPago">
                                <option value="Efectivo">Efectivo</option>
                                <option value="Transferencia">Transferencia</option>
                                <option value="Bizum">Bizum</option>
                            </select>
                        </div>
                        <div class="col-sm-5 align-items-center ms-5">
                            @if ($metodoPago == 'Transferencia')
                                <label for="cuentaTransferencia" class="col-form-label">Cuenta para la
                                    transferencia</label>
                                <select class="form-control text-center" wire:model="cuentaTransferencia"
                                    name="cuentaTransferencia" id="cuentaTransferencia">
                                    <option value="Deutsche Bank">Deutsche Bank</option>
                                    <option value="Caixabank">Caixabank</option>
                                </select>
                            @endif
                        </div>
                        <div class="form-group col-12">
                            <div class="col-sm-10 d-inline-flex align-items-center ms-5">
                                <input class="form-check-input mt-0" wire:model="authImagen" type="checkbox"
                                    id="authImagen">
                                <label for="confEmail" class=" col-form-label">Autorizo la captación y difusión de
                                    imágenes en medios propios.</label>
                            </div>
                        </div>

                        <div class="form-group col-12">
                            <div class="col-sm-10 d-inline-flex align-items-center ms-5">
                                <input class="form-check-input mt-0" wire:model="authMenores" type="checkbox"
                                    id="authMenores">
                                <label for="confEmail" class=" col-form-label">En caso afirmativo, deseo que se
                                    muestren los rostros de los menores. </label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 justify-content-center">
            <div class="position-fixed">
                <div class="card m-b-30">
                    <div class="card-body">
                        <h5>Opciones de guardado</h5>
                        <div class="row">
                            <div class="col-12">
                                <button class="w-100 btn btn-success mb-2" wire:click.prevent="alertaGuardar">Guardar
                                    presupuesto</button>
                                <button class="w-100 btn btn-danger mb-2" wire:click.prevent="alertaEliminar">Eliminar
                                    presupuesto</button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card m-b-30">
                    <div class="card-body">
                        <h5>Opciones de impresión</h5>
                        <div class="row">
                            <div class="col-12">
                                <button class="w-100 btn btn-info mb-2" wire:click.prevent="alertaAceptar">Imprimir
                                    datos de
                                    presupuesto</button>
                                <button class="w-100 btn btn-primary mb-2" wire:click.prevent="alertaCancelar">@if($contrato_id != null)Ver @else Generar @endif
                                    contrato</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <style>
            fieldset.scheduler-border {
                border: 1px groove #ddd !important;
                padding: 0 1.4em 1.4em 1.4em !important;
                margin: 0 0 1.5em 0 !important;
                -webkit-box-shadow: 0px 0px 0px 0px #000;
                box-shadow: 0px 0px 0px 0px #000;
            }

            table {
                border: 1px black solid !important;
            }

            th {
                border-bottom: 1px black solid !important;
                border: 1px black solid !important;
                border-top: 1px black solid !important;
            }

            th.header {
                border-bottom: 1px black solid !important;
                border: 1px black solid !important;
                border-top: 2px black solid !important;
            }

            td.izquierda {
                border-left: 1px black solid !important;

            }

            td.derecha {
                border-right: 1px black solid !important;

            }

            td.suelo {}
        </style>
    </div>
</div>


@section('scripts')
    <script>
        $("#alertaAceptar").on("click", () => {
            Swal.fire({
                title: '¿Estás seguro? Comprueba que todo está en orden.',
                icon: 'warning',
                text: 'Si estás seguro, pulsa el botón de "Confirmar" para imprimir el presupuesto.',
                showConfirmButton: true,
                showCancelButton: true
            }).then((result) => {
                if (result.isConfirmed) {
                    window.livewire.emit('imprimirPresupuesto');
                }
            });
        });

        $("#alertaCancelar").on("click", () => {
            Swal.fire({
                title: '¿Estás seguro?',
                icon: 'error',
                text: 'Si estás seguro, pulsa el botón de "Confirmar" para imprimir contrato.',
                showConfirmButton: true,
                showCancelButton: true
            }).then((result) => {
                if (result.isConfirmed) {
                    window.livewire.emit('confirmedImprimir');
                }
            });
        });

        $("#alertaEliminar").on("click", () => {
            Swal.fire({
                title: '¿Estás seguro? No se podrá revertir la acción.',
                icon: 'error',
                text: 'Si estás seguro, pulsa el botón de "Confirmar" para eliminar el presupuesto.',
                showConfirmButton: true,
                showCancelButton: true
            }).then((result) => {
                if (result.isConfirmed) {
                    window.livewire.emit('destroy');
                }
            });
        });


        $("#alertaGuardar").on("click", () => {
            Swal.fire({
                title: '¿Estás seguro? Comprueba que todo está en orden.',
                icon: 'warning',
                text: 'Si estás seguro, pulsa el botón de "Confirmar" para guardar el presupuesto.',
                showConfirmButton: true,
                showCancelButton: true
            }).then((result) => {
                if (result.isConfirmed) {
                    window.livewire.emit('updateEvento');
                }
            });
        });

        $("#alertaImprimir").on("click", () => {
            Swal.fire({
                title: '¿Estás seguro? Comprueba que todo está en orden.',
                icon: 'warning',
                text: 'Si estás seguro, pulsa el botón de "Confirmar" para descargar el PDF del presupuesto.',
                showConfirmButton: true,
                showCancelButton: true
            }).then((result) => {
                if (result.isConfirmed) {
                    window.livewire.emit('imprimirPresupuesto');
                }
            });
        });

        $("#alertaFacturar").on("click", () => {
            Swal.fire({
                title: '¿Estás seguro? Comprueba que todo está en orden.',
                icon: 'info',
                text: 'Si estás seguro, pulsa el botón de "Confirmar" para crear una factura para este presupuesto.',
                showConfirmButton: true,
                showCancelButton: true
            }).then((result) => {
                if (result.isConfirmed) {
                    window.livewire.emit('facturarPresupuesto');
                }
            });
        });
        $.datepicker.regional['es'] = {
            closeText: 'Cerrar',
            prevText: '< Ant',
            nextText: 'Sig >',
            currentText: 'Hoy',
            monthNames: ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre',
                'Octubre', 'Noviembre', 'Diciembre'
            ],
            monthNamesShort: ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'],
            dayNames: ['Domingo', 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado'],
            dayNamesShort: ['Dom', 'Lun', 'Mar', 'Mié', 'Juv', 'Vie', 'Sáb'],
            dayNamesMin: ['Do', 'Lu', 'Ma', 'Mi', 'Ju', 'Vi', 'Sá'],
            weekHeader: 'Sm',
            dateFormat: 'yy-mm-dd',
            firstDay: 1,
            isRTL: false,
            showMonthAfterYear: false,
            yearSuffix: ''
        };
        $.datepicker.setDefaults($.datepicker.regional['es']);
        // document.addEventListener('livewire:load', function() {


        // })
        document.addEventListener("livewire:load", () => {
            Livewire.hook('message.processed', (message, component) => {
                $('.js-example-basic-single').select2();
            });

            // $('#id_cliente').on('change', function (e) {
            // console.log('change')
            // console.log( e.target.value)
            // // var data = $('.js-example-basic-single').select2("val");
            // })
        });



        $(document).ready(function() {

            $('.js-example-basic-single').select2();
            // $('.js-example-basic-single').on('change', function (e) {
            // console.log('change')
            // console.log( e.target.value)
            // var data = $('.js-example-basic-single').select2("val");

            // @this.set('foo', data);
            //     livewire.emit('selectedCompanyItem', e.target.value)
            // });
            // $('#tableServicios').DataTable({
            //     responsive: true,
            //     dom: 'Bfrtip',
            //     buttons: [
            //         'copy', 'csv', 'excel', 'pdf', 'print'
            //     ],
            //     buttons: [{
            //         extend: 'collection',
            //         text: 'Export',
            //         buttons: [{
            //                 extend: 'pdf',
            //                 className: 'btn-export'
            //             },
            //             {
            //                 extend: 'excel',
            //                 className: 'btn-export'
            //             }
            //         ],
            //         className: 'btn btn-info text-white'
            //     }],
            //     "language": {
            //         "lengthMenu": "Mostrando _MENU_ registros por página",
            //         "zeroRecords": "Nothing found - sorry",
            //         "info": "Mostrando página _PAGE_ of _PAGES_",
            //         "infoEmpty": "No hay registros disponibles",
            //         "infoFiltered": "(filtrado de _MAX_ total registros)",
            //         "search": "Buscar:",
            //         "paginate": {
            //             "first": "Primero",
            //             "last": "Ultimo",
            //             "next": "Siguiente",
            //             "previous": "Anterior"
            //         },
            //         "zeroRecords": "No se encontraron registros coincidentes",
            //     }

        });



        // $("#fechaEmision").datepicker();


        // $("#fechaEmision").on('change', function(e) {
        //     @this.set('fechaEmision', $('#fechaEmision').val());
        // });



        function togglePasswordVisibility() {
            var passwordInput = document.getElementById("password");
            var eyeIcon = document.getElementById("eye-icon");
            if (passwordInput.type === "password") {
                passwordInput.type = "text";
                eyeIcon.className = "fas fa-eye-slash";
            } else {
                passwordInput.type = "password";
                eyeIcon.className = "fas fa-eye";
            }
        }

        //observer para aplicar el datepicker de evento
        // const observer = new MutationObserver((mutations, observer) => {
        //     console.log(mutations, observer);
        // });
        // observer.observe(document, {
        //     subtree: true,
        //     attributes: true
        // });



        document.addEventListener('DOMSubtreeModified', (e) => {
            $("#diaEvento").datepicker();

            // $("#diaEvento").on('focus', function(e) {
            //     document.getElementById("guardar-evento").style.visibility = "hidden";
            // })
            // $("#diaEvento").on('focusout', function(e) {
            //     if ($('#diaEvento').val() != "") {
            //         document.getElementById("guardar-evento").style.visibility = "visible";
            //     }

            // })
            // $("#diaFinal").on('focus', function(e) {
            //     document.getElementById("guardar-evento").style.visibility = "hidden";
            // })
            // $("#diaFinal").on('focusout', function(e) {
            //     if ($('#diaFinal').val() != "") {
            //         document.getElementById("guardar-evento").style.visibility = "visible";
            //     }

            // })

            $("#diaFinal").datepicker();

            $("#diaFinal").on('change', function(e) {
                @this.set('diaFinal', $('#diaFinal').val());

            });

            $("#diaEvento").on('change', function(e) {
                @this.set('diaEvento', $('#diaEvento').val());
                @this.set('diaFinal', $('#diaEvento').val());

            });

            $('#id_cliente').on('change', function(e) {
                console.log('change')
                console.log(e.target.value)
                var data = $('#id_cliente').select2("val");
                @this.set('id_cliente', data);
                Livewire.emit('selectCliente')

                // livewire.emit('selectedCompanyItem', data)
            })
        })

        function OpenSecondPage() {
            var id = @this.id_cliente
            window.open(`/admin/clientes-edit/` + id, '_blank'); // default page
        };
    </script>
@endsection
