
@section('head')
    @vite(['resources/sass/cursos.scss'])

@endsection

    <div class="container mx-auto">
        <h1>Cursos</h1>
        <h2 class="crearCurso">Crear curso</h2>
        <br>


                <form wire:submit.prevent="submit">
                    <input type="hidden" name="csrf-token" value="{{ csrf_token() }}">

                    <div class="mb-3 row d-flex align-items-center">
                        <label for="nombre" class="col-sm-2 col-form-label">Nombre </label>
                        <div class="col-sm-10">
                          <input type="text" wire:model="nombre" class="form-control" name="nombre" id="nombre" placeholder="Irata nivel...">
                          @error('nombre') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div class="mb-3 row d-flex align-items-center">
                        <label for="denominacion" class="col-sm-2 col-form-label">Denominación</label>
                        <div class="col-sm-10" wire:ignore.self>
                            <select id="denominacion" class="form-control js-example-responsive" wire:model="denominacion_id">
                                <option value="">-- Sin denominación --</option>
                                @foreach ($denominaciones as $dem)
                                    <option value="{{ $dem->id }}">{{ $dem->nombre }} </option>
                                @endforeach
                            </select>
                            @error('denominacion')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <div class="mb-3 row d-flex align-items-center">
                        <label for="celebracion" class="col-sm-2 col-form-label">Celebración</label>
                        <div class="col-sm-10" wire:ignore.self>
                            <select id="celebracion" class="form-control js-example-responsive" wire:model="celebracion_id">
                                <option value="">-- Sin celebración --</option>
                                @foreach ($celebraciones as $cel)
                                    <option value="{{ $cel->id }}">{{ $cel->nombre }} </option>
                                @endforeach
                            </select>
                            @error('denominacion')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <div class="mb-3 row d-flex align-items-center">
                        <label for="precio" class="col-sm-2 col-form-label">Precio </label>
                        <div class="col-sm-10">
                          <input type="number" wire:model="precio" class="form-control" name="precio" id="precio" placeholder="75.12...">
                          @error('precio') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div class="mb-3 row d-flex align-items-center">
                        <label for="duracion" class="col-sm-2 col-form-label">Duración (Horas) </label>
                        <div class="col-sm-10">
                          <input type="number" wire:model="duracion" class="form-control" name="duracion" id="duracion" placeholder="8...">
                          @error('duracion') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div class="mb-3 row d-flex align-items-center">
                        <label for="fecha_inicio" class="col-sm-2 col-form-label">Fecha inicio</label>
                        <div class="col-sm-10">
                            <input type="text" wire:model.defer="fecha_inicio" class="form-control" placeholder="18/02/2023"
                                id="datepicker">
                            @error('fecha_inicio')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <div class="mb-3 row d-flex align-items-center">
                        <label for="fecha_fin" class="col-sm-2 col-form-label">Fecha Fin.</label>
                        <div class="col-sm-10">
                            <input type="text" wire:model.defer="fecha_fin" class="form-control" placeholder="24/11/2023"
                                id="datepicker2">
                            @error('fecha_fin')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <div class="mb-3 row d-flex align-items-center" wire:ignore>
                        <label for="descripcion" class="col-sm-2 col-form-label">Descripción </label>
                        <div class="col-sm-10">
                            <textarea name="descripcion" wire:model="descripcion" id="editor" cols="30" rows="10"></textarea>
                          @error('descripcion') <span class="text-danger">{{ $message }}</span> @enderror
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
                @this.set('fecha_inicio', $('#datepicker').val());
                });
            $("#datepicker2").datepicker();

            $("#datepicker2").on('change', function(e){
                @this.set('fecha_fin', $('#datepicker2').val());
                });


        });
    </script>

<script src="https://cdn.ckeditor.com/ckeditor5/36.0.1/classic/ckeditor.js"></script>


<script>
    // El then es para que no se pierdan el CkEditor cuando se validan los campos
    ClassicEditor
            .create( document.querySelector( '#editor' ) )
            .then(function(editor){
                    editor.model.document.on('change:data', ()=>{
                        @this.set('descripcion', editor.getData())
                    })
            } )
            .catch( error => {
                    console.error( error );
            } );
</script>

    @endsection

