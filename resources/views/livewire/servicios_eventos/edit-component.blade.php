@section('head')
    @vite(['resources/sass/productos.scss'])
@endsection
<div class="container mx-auto">
    <h1>Servicios</h1>
    <h2>Editar Servicio</h2>
    <br>


    <form wire:submit.prevent="update">
        <input type="hidden" name="csrf-token" value="{{ csrf_token() }}">

        <div class="mb-3 row d-flex align-items-center">
            <label for="nombre" class="col-sm-2 col-form-label">Nombre </label>
            <div class="col-sm-10">
                <input type="text" wire:model="nombre" class="form-control" name="nombre" id="nombre"
                    placeholder="Servicio">
                @error('nombre')
                    <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>
        </div>

        <div class="mb-3 row d-flex align-items-center">
            <label for="id_categoria" class="col-sm-2 col-form-label">Categoria </label>
            <div class="col-sm-10">
                <select class="form-control" name="id_categoria" required id="id_categoria" wire:model="id_categoria">
                    <option value="">Categorias</option>
                    @foreach ($servicioCategorias as $categoria)
                        <option value="{{ $categoria->id }}">
                            {{ $categoria->nombre }}</option>
                    @endforeach
                    @error('id_categoria')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </select>
            </div>
        </div>

        <div class="mb-3 row d-flex align-items-center">
            <label for="id_pack" class="col-sm-2 col-form-label">Pack </label>
            <div class="col-sm-10">
                <select class="form-control" name="id_pack" required id="id_pack"
                    wire:model="id_pack">
                    <option value="">Pack</option>
                    @foreach ($servicioPacks as $pack)
                        <option value="{{ $pack->id }}">{{ $pack->nombre }}
                        </option>
                    @endforeach
                    @error('id_pack')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </select>
            </div>
        </div>

        <div class="mb-3 row d-flex align-items-center">
            <label for="precioBase" class="col-sm-2 col-form-label">Precio Base </label>
            <div class="col-sm-10">
                <input type="text" wire:model="precioBase" class="form-control" name="precioBase" id="precioBase"
                    placeholder="0">
                @error('precioBase')
                    <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>
        </div>

        <div class="mb-3 row d-flex align-items-center">
            <label for="minEmpleados" class="col-sm-2 col-form-label">Empleados mínimos </label>
            <div class="col-sm-10">
                <input type="number" wire:model="minEmpleados" class="form-control" name="minEmpleados" id="minEmpleados"
                    placeholder="1">
                @error('minEmpleados')
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
