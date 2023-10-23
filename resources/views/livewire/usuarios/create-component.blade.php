    <div class="container-fluid">
        <script src="//unpkg.com/alpinejs" defer></script>
        <div class="page-title-box">
            <div class="row align-items-center">
                <div class="col-sm-6">
                    <h4 class="page-title">CREAR USUARIO</span></h4>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-right">
                        <li class="breadcrumb-item"><a href="javascript:void(0);">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="javascript:void(0);">Usuarios</a></li>
                        <li class="breadcrumb-item active">Crear usuario</li>
                    </ol>
                </div>
            </div> <!-- end row -->
        </div>
        <!-- end page-title -->
        <div class="row" style="align-items: start !important;">
            <div class="col-md-9">
                <div class="card m-b-30">
                    <div class="card-body">
                        <form wire:submit.prevent="submit">
                            <input type="hidden" name="csrf-token" value="{{ csrf_token() }}">

                            <div class="form-group row">
                                <div class="col-sm-6">
                                    <label for="name" class="col-sm-12 col-form-label">NOMBRE</label>
                                    <div class="col-sm-10">
                                        <input type="text" wire:model="name" class="form-control" name="name"
                                            id="name" placeholder="José Carlos...">
                                        @error('name')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-sm-6">
                                    <label for="surname" class="col-sm-12 col-form-label">APELLIDOS</label>
                                    <div class="col-sm-10">
                                        <input type="text" wire:model="surname" class="form-control" name="surname"
                                            id="surname" placeholder="Pérez...">
                                        @error('surname')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="form-group row">
                                <div class="col-sm-6">
                                    <label for="email" class="col-sm-12 col-form-label">CORREO ELECTRÓNICO</label>
                                    <div class="col-sm-10">
                                        <input type="text" wire:model="email" class="form-control" name="email"
                                            id="email" placeholder="jose85@hotmail.com ...">
                                        @error('email')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <label for="username" class="col-sm-12 col-form-label">NOMBRE DE USUARIO (ALIAS)</label>
                                    <div class="col-sm-10">
                                        <input type="text" wire:model="username" class="form-control" name="username"
                                            id="username" placeholder="jose85">
                                        @error('username')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                            <div class="form-group row">
                                <div class="col-md-11" wire:ignore>
                                    <div x-data="" x-init="$('#select2-rol').select2();
                                    $('#select2-rol').on('change', function(e) {
                                        var data = $('#select2-rol').select2('val');
                                        console.log(data)
                                        @this.set('role', data);
                                    });">
                                        <label for="fechaVencimiento" class="col-sm-12 col-form-label">ROL (PUESTO DE TRABAJO)</label>
                                        <div class="col-sm-12">
                                            <select class="form-control" name="rol" id="select2-rol"
                                                wire:model="role">
                                                @foreach ($this->roles as $rol)
                                                    <option value="{{ $rol->id }}">{{ $rol->nombre }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group row">
                                <div class="col-11">
                                    <label for="password" class="col-12 col-form-label">Contraseña</label>
                                    <div class="col-12">
                                        <input type="password" wire:model="password" class="form-control"
                                            name="password" id="password" placeholder="123456...">
                                        @error('password')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-1" style="padding: 0 !important">
                                    <label for="password" class="col-12 col-form-label">&nbsp;</label>
                                    <button type="button" class="me-auto btn btn-primary"
                                        onclick="togglePasswordVisibility()">
                                        <i class="fas fa-eye" id="eye-icon"></i>
                                    </button>
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
                                <button class="w-100 btn btn-success btn-lg mb-2" id="alertaGuardar">CREAR USUARIO</button>
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
                    text: 'Pulsa el botón de confirmar para crear el nuevo usuario.',
                    icon: 'warning',
                    showConfirmButton: true,
                    showCancelButton: true
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.livewire.emit('submit');
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
                dateFormat: 'dd/mm/yy',
                firstDay: 1,
                isRTL: false,
                showMonthAfterYear: false,
                yearSuffix: ''
            };
            $.datepicker.setDefaults($.datepicker.regional['es']);
            document.addEventListener('livewire:load', function() {


            })
            $(document).ready(function() {
                console.log('select2')
                $("#datepicker").datepicker();

                $("#datepicker").on('change', function(e) {
                    @this.set('fecha_nac', $('#datepicker').val());
                });

            });

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
        </script>
    @endsection
