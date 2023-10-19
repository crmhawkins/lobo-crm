@section('head')
    @vite(['resources/sass/productos.scss'])
@endsection
<div class="container mx-auto">
    <h1>Programa</h1>
    <h2>Editar Programa</h2>
    <br>


    <form wire:submit.prevent="update">
        <input type="hidden" name="csrf-token" value="{{ csrf_token() }}">

        <div class="mb-3 row d-flex align-items-center">
            <label for="dia" class="col-sm-2 col-form-label">Dia del programa</label>
            <div class="col-sm-10">
                <input type="date-local" wire:model.defer="dia" class="form-control" placeholder="15/02/2023"
                    id="datepicker">
                @error('dia')
                    <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>
        </div>

        <div class="mb-3 row d-flex align-items-center">
            <label for="evento" class="col-sm-2 col-form-label">Evento </label>
            <div class="col-sm-10" wire:ignore.self>
                <select id="id_evento" class="form-control js-example-responsive" wire:model="id_evento">
                    <option value="">-- Seleccione un evento --</option>
                    @foreach ($eventos as $evento)
                        <option value="{{ $evento->id }}">{{ $evento->nombre }}</option>
                    @endforeach
                </select>
                @error('id_evento')
                    <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>
        </div>

        <div class="mb-3 row d-flex align-items-center">
            <label for="id_servicio" class="col-sm-2 col-form-label">Servicio </label>
            <div class="col-sm-10">
                <select class="form-control" name="id_servicio" required id="id_servicio"
                    wire:model="id_servicio">
                    <option value="">Servicio</option>
                    @foreach ($servicios as $servicio)
                        <option value="{{ $servicio->id }}">{{ $servicio->nombre }}</option>
                    @endforeach
                    <option value="" wire:click.prevetn="crearServicio">Crear Servicio</option>
                    @error('id_servicio')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </select>
            </div>
        </div>

        <div class="mb-3 row d-flex align-items-center">
            <label for="id_monitor" class="col-sm-2 col-form-label">Monitor </label>
            <div class="col-sm-10">
                <select class="form-control" name="id_monitor" required id="id_monitor"
                    wire:model="id_monitor">
                    <option value="">Monitor</option>
                    @foreach ($monitores as $monitor)
                        <option value="{{ $monitor->id }}">{{ $this->nombreMonitor($monitor->id)}}
                        </option>
                    @endforeach
                    <option value="" wire:click.prevetn="crearMonitor">Insertar Monitor</option>
                    @error('id_monitor')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </select>
            </div>
        </div>


        <div class="mb-3 row d-flex align-items-center">
            <label for="comienzoMontaje" class="col-sm-2 col-form-label">Hora de comienzo del montaje </label>
            <div class="col-sm-10">
                <input type="text" wire:model="comienzoMontaje" class="form-control" name="comienzoMontaje" id="comienzoMontaje"
                    placeholder="0">
                @error('comienzoMontaje')
                    <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>
        </div>

        <div class="mb-3 row d-flex align-items-center">
            <label for="comienzoEvento" class="col-sm-2 col-form-label">Hora de comienzo del servicio </label>
            <div class="col-sm-10">
                <input type="text" wire:model="comienzoEvento" class="form-control" name="comienzoEvento"
                    id="comienzoEvento" placeholder="00:00">
                @error('comienzoEvento')
                    <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>
        </div>

        <div class="mb-3 row d-flex align-items-center">
            <label for="horas" class="col-sm-2 col-form-label">Horas de duración </label>
            <div class="col-sm-10">
                <input type="number" wire:model="horas" class="form-control" name="horas"
                    id="horas" placeholder="0 para indefinidas">
                @error('horas')
                    <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>
        </div>

        <div class="mb-3 row d-flex align-items-center">
            <label for="tiempoDesmontaje" class="col-sm-2 col-form-label">Tiempo de desmontaje (Minutos) </label>
            <div class="col-sm-10">
                <input type="number" wire:model="tiempoDesmontaje" class="form-control" name="tiempoDesmontaje"
                    id="tiempoDesmontaje" placeholder="30">
                @error('tiempoDesmontaje')
                    <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>
        </div>

        <div class="mb-3 row d-flex align-items-center">
            <button type="submit" class="btn btn-outline-info">Guardar</button>
        </div>



    </form>
    <div class="mb-3 row d-flex align-items-center">
        <button wire:click="destroy" class="btn btn-outline-danger">Eliminar</button>
    </div>
</div>

</div>


@section('scripts')
    <script>
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
