@section('head')
    @vite(['resources/sass/productos.scss'])
    @vite(['resources/sass/alumnos.scss'])
@endsection



<div class="container mx-auto">
    <h1>Alumnos</h1>
    <h2>Crear alumno</h2>
    <br>


    <form wire:submit.prevent="submit">
        <input type="hidden" name="csrf-token" value="{{ csrf_token() }}">

        <div class="mb-3 row d-flex align-items-center">
            <label for="nombre" class="col-sm-2 col-form-label">Nombre </label>
            <div class="col-sm-10">
                <input type="text" wire:model="nombre" class="form-control" name="nombre" id="nombre"
                    placeholder="Carlos">
                @error('nombre')
                    <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>
        </div>

        <div class="mb-3 row d-flex align-items-center">
            <label for="empresa" class="col-sm-2 col-form-label">Empresa</label>
            <div class="col-sm-10" wire:ignore.self>
                <select id="empresa" class="form-control js-example-responsive" wire:model="empresa_id">
                    <option value="">-- Cliente por cuenta propia --</option>
                    @foreach ($empresas as $emp)
                        <option value="{{ $emp->id }}">{{ $emp->nombre }} </option>
                    @endforeach
                </select>
                @error('empresa')
                    <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>
        </div>

        <div class="mb-3 row d-flex align-items-center">
            <label for="apellidos" class="col-sm-2 col-form-label">Apellidos </label>
            <div class="col-sm-10">
                <input type="text" wire:model="apellidos" class="form-control" name="apellidos" id="apellidos"
                    placeholder="Pérez">
                @error('apellidos')
                    <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>
        </div>


        <div class="mb-3 row d-flex align-items-center">
            <label for="dni" class="col-sm-2 col-form-label">DNI </label>
            <div class="col-sm-10">
                <input type="text" wire:model="dni" class="form-control" name="dni" id="dni"
                    placeholder="7515763200P">
                @error('dni')
                    <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>
        </div>

        <div class="mb-3 row d-flex align-items-center">
            <label for="fecha_nac" class="col-sm-2 col-form-label">Fecha Nac.</label>
            <div class="col-sm-10">
                <input type="text" wire:model.defer="fecha_nac" class="form-control" placeholder="18/02/2002"
                    id="datepicker">
                @error('fecha_nac')
                    <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>
        </div>

        <div class="mb-3 row d-flex align-items-center">
            <label for="direccion" class="col-sm-2 col-form-label">Dirección </label>
            <div class="col-sm-10">
                <input type="text" wire:model="direccion" class="form-control" name="direccion" id="direccion"
                    placeholder="Calle Baldomero nº 12">
                @error('direccion')
                    <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>
        </div>

        <div class="mb-3 row d-flex align-items-center">
            <label for="localidad" class="col-sm-2 col-form-label">Localidad </label>
            <div class="col-sm-10">
                <input type="text" wire:model="localidad" class="form-control" name="localidad" id="localidad"
                    placeholder="Algeciras">
                @error('localidad')
                    <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>
        </div>

        <div class="mb-3 row d-flex align-items-center">
            <label for="provincia" class="col-sm-2 col-form-label">Provincia </label>
            <div class="col-sm-10">
                <input type="text" wire:model="provincia" class="form-control" name="provincia" id="provincia"
                    placeholder="Cádiz">
                @error('provincia')
                    <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>
        </div>

        <div class="mb-3 row d-flex align-items-center">
            <label for="cod_postal" class="col-sm-2 col-form-label">Cod. Postal </label>
            <div class="col-sm-10">
                <input type="text" wire:model="cod_postal" class="form-control" name="cod_postal" id="cod_postal"
                    placeholder="11749">
                @error('cod_posta')
                    <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>
        </div>

        <div class="mb-3 row d-flex align-items-center">
            <label for="pais" class="col-sm-2 col-form-label">País </label>
            <div class="col-sm-10">
                <input type="text" wire:model="pais" class="form-control" name="pais" id="pais"
                    placeholder="España">
                @error('pais')
                    <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>
        </div>

        <div class="mb-3 row d-flex align-items-center">
            <label for="telefono" class="col-sm-2 col-form-label">Teléfono</label>
            <div class="col-sm-10">
                <input type="text" wire:model="telefono" class="form-control" name="telefono" id="telefono"
                    placeholder="956812502">
                @error('telefono')
                    <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>
        </div>

        <div class="mb-3 row d-flex align-items-center">
            <label for="movil" class="col-sm-2 col-form-label">Móvil</label>
            <div class="col-sm-10">
                <input type="text" wire:model="movil" class="form-control" name="movil" id="movil"
                    placeholder="654138955">
                @error('movil')
                    <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>
        </div>

        <div class="mb-3 row d-flex align-items-center">
            <label for="email" class="col-sm-2 col-form-label">Email</label>
            <div class="col-sm-10">
                <input type="text" wire:model="email" class="form-control" name="email" id="email"
                    placeholder="ejemplo@gmail.com">
                @error('email')
                    <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>
        </div>

        <div class="mb-3 row d-flex align-items-center">
            <button type="submit" class="btn btn-outline-info">Guardar</button>
        </div>


    </form>
</div>





</div>

</tbody>
</table>
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
    </script>
@endsection
