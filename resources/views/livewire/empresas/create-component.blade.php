
@section('head')
    @vite(['resources/sass/productos.scss'])
    @vite(['resources/sass/alumnos.scss'])
@endsection



    <div class="container mx-auto">
        <h1>Empresa</h1>
        <h2>Crear empresa</h2>
        <br>


                <form wire:submit.prevent="submit">
                    <input type="hidden" name="csrf-token" value="{{ csrf_token() }}">

                    <div class="mb-3 row d-flex align-items-center">
                        <label for="nombre" class="col-sm-2 col-form-label">Nombre </label>
                        <div class="col-sm-10">
                          <input type="text" wire:model="nombre" class="form-control" name="nombre" id="nombre" placeholder="Zenotech SL...">
                          @error('nombre') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div class="mb-3 row d-flex align-items-center">
                        <label for="telefono" class="col-sm-2 col-form-label">Teléfono </label>
                        <div class="col-sm-10">
                          <input type="text" wire:model="telefono" class="form-control" name="telefono" id="telefono" placeholder="956124591...">
                          @error('telefono') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>
                    </div>


                    <div class="mb-3 row d-flex align-items-center">
                        <label for="direccion" class="col-sm-2 col-form-label">Dirección </label>
                        <div class="col-sm-10">
                          <input type="text" wire:model="direccion" class="form-control" name="direccion" id="direccion" placeholder="Calle arboleda nº...">
                          @error('direccion') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div class="mb-3 row d-flex align-items-center">
                        <label for="cif" class="col-sm-2 col-form-label">CIF </label>
                        <div class="col-sm-10">
                          <input type="text" wire:model="cif" class="form-control" name="cif" id="cif" placeholder="Z2826000G">
                          @error('cif') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div class="mb-3 row d-flex align-items-center">
                        <label for="email" class="col-sm-2 col-form-label">Email </label>
                        <div class="col-sm-10">
                          <input type="text" wire:model="email" class="form-control" name="email" id="email" placeholder="zenotech@gmail.com...">
                          @error('email') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div class="mb-3 row d-flex align-items-center">
                        <label for="cod_postal" class="col-sm-2 col-form-label">Cod. Postal </label>
                        <div class="col-sm-10">
                          <input type="text" wire:model="cod_postal" class="form-control" name="cod_postal" id="cod_postal" placeholder="11574...">
                          @error('cod_postal') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div class="mb-3 row d-flex align-items-center">
                        <label for="localidad" class="col-sm-2 col-form-label">Localidad </label>
                        <div class="col-sm-10">
                          <input type="text" wire:model="localidad" class="form-control" name="localidad" id="localidad" placeholder="Algeciras...">
                          @error('localidad') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div class="mb-3 row d-flex align-items-center">
                        <label for="pais" class="col-sm-2 col-form-label">País </label>
                        <div class="col-sm-10">
                          <input type="text" wire:model="pais" class="form-control" name="pais" id="pais" placeholder="España...">
                          @error('pais') <span class="text-danger">{{ $message }}</span> @enderror
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
            monthNames: ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'],
            monthNamesShort: ['Ene','Feb','Mar','Abr', 'May','Jun','Jul','Ago','Sep', 'Oct','Nov','Dic'],
            dayNames: ['Domingo', 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado'],
            dayNamesShort: ['Dom','Lun','Mar','Mié','Juv','Vie','Sáb'],
            dayNamesMin: ['Do','Lu','Ma','Mi','Ju','Vi','Sá'],
            weekHeader: 'Sm',
            dateFormat: 'dd/mm/yy',
            firstDay: 1,
            isRTL: false,
            showMonthAfterYear: false,
            yearSuffix: ''
        };
        $.datepicker.setDefaults($.datepicker.regional['es']);
        document.addEventListener('livewire:load', function () {


        })
        $(document).ready(function() {
            console.log('select2')
            $("#datepicker").datepicker();

            $("#datepicker").on('change', function(e){
                @this.set('fecha_nac', $('#datepicker').val());
                });

        });
    </script>

    @endsection

