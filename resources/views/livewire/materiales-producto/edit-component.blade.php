<div class="container-fluid">
    <script src="//unpkg.com/alpinejs" defer></script>
    <div class="page-title-box">
        <div class="row align-items-center">
            <div class="col-sm-6">
                <h4 class="page-title">EDITAR ASIGNACIÓN DE MATERIALES A PRODUCTOS POR CAJA</span></h4>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-right">
                    <li class="breadcrumb-item"><a href="javascript:void(0);">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="javascript:void(0);">Productos</a></li>
                    <li class="breadcrumb-item active">Asignar materiales a producto por caja</li>
                </ol>
            </div>
        </div>
    </div>
    <div class="row" style="align-items: start !important">
        <div class="col-md-9">
            <div class="card m-b-30">
                <div class="card-body">
                    <div class="form-row justify-content-center">
                        <div class="form-group col-md-12">
                            <h5 class="ms-3"
                                style="border-bottom: 1px gray solid !important; padding-bottom: 10px !important;">Datos
                                básicos del producto</h5>
                        </div>
                        <div class="form-group col-md-2">
                            <label for="fecha">Imagen</label>
                            <img src="{{ asset('storage/photos/' . $imagen_producto) }}"
                                style="max-width: 100% !important; text-align: center">
                        </div>
                        <div class="form-group col-md-6">
                            <label for="nombre_producto">Nombre</label>
                            <input type="text" wire:model="nombre_producto" class="form-control" disabled>
                            <br><br>
                            <label for="fecha">Descripción</label>
                            <textarea wire:model="descripcion_producto" class="form-control"></textarea>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card m-b-30">
                <div class="card-body">
                    <div class="form-row justify-content-center">
                        <div class="form-group col-md-12">
                            <h5 class="ms-3"
                                style="border-bottom: 1px gray solid !important;padding-bottom: 10px !important;display: flex !important;flex-direction: row;justify-content: space-between;">
                                Lista de productos <button type="button" class="btn btn-primary" data-toggle="modal"
                                    style="align-self: end !important;" data-target="#addProductModal">Añadir</button>
                            </h5>
                            <div class="form-group col-md-12">
                                @if (count($mercaderias_ordenadas) > 0)
                                    <table class="table ms-3 table-striped table-bordered dt-responsive nowrap">
                                        <thead>
                                            <tr>
                                                <th>Producto</th>
                                                <th>Cantidad por caja</th>
                                                <th>Eliminar</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($mercaderias_ordenadas as $productoIndex => $producto)
                                                <tr>
                                                    <td width="40%">
                                                        {{ $this->getNombreTabla($producto['mercaderia_id']) }}
                                                    </td>
                                                    <td width="40%">
                                                        <div class="row align-items-center">
                                                            <div class="col-6 text-end"><input type="number"
                                                                    class="form-control"
                                                                    wire:model="mercaderias_ordenadas.{{ $productoIndex }}.cantidad"
                                                                    wire:change='setPrecioEstimado'>
                                                            </div>
                                                            <div class="col-6 text-start">
                                                                <p class="my-auto">unidades</p>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td width="20%"><button type="button" class="btn btn-danger"
                                                            wire:click="deleteArticulo('{{ $productoIndex }}')">X</button>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <div wire:ignore.self class="modal fade" id="addProductModal" tabindex="-1" role="dialog">
                    <div class="modal-dialog"
                        style="min-width: 25vw !important; align-self: center !important; margin-top: 0 !important;">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">Añadir Producto</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                @if ($mercaderia_seleccionada != null)
                                    <div class="row justify-content-center">
                                        <div class="col-md-12">
                                            <div class="card border border-dark border-1"
                                                style="margin-bottom: 5px !important">
                                                <div class="card-body"
                                                    style="
                                                display: flex;
                                                flex-direction: column;
                                                flex-wrap: wrap;
                                                align-items: center;
                                                justify-content: center;
                                            ">
                                                    <h2 class="card-title mt-0 font-32"
                                                        style="text-align: center; margin-bottom: -0.25rem !important;">
                                                        {{ $this->getMercaderiaNombre() }}</h2>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                                <div class="row justify-content-center">
                                    <div class="col-md-10" style="text-align: center !important;">
                                        <label for="fechaVencimiento">Producto de mercadería seleccionado</label>
                                    </div>
                                    <div class="col-md-10" wire:ignore>
                                        <div x-data="" x-init="$('#select2-mercaderia').select2();
                                        $('#select2-mercaderia').on('change', function(e) {
                                            var data = $('#select2-mercaderia').select2('val');
                                            @this.set('mercaderia_seleccionada', data);
                                            console.log('data');
                                        });">
                                            <select name="mercaderia" id="select2-mercaderia"
                                                wire:model="mercaderia_seleccionada" style="width: 100% !important">
                                                <option value="{{ null }}">-- Selecciona un producto de
                                                    mercadería --
                                                </option>
                                                @foreach ($mercaderias as $presup)
                                                    <option value="{{ $presup->id }}">{{ $presup->nombre }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                @if ($mercaderia_seleccionada != null)
                                    <div class="row justify-content-center mt-1">
                                        <div class="col-md-7" style="text-align: center !important;">
                                            <label for="unidades">Unidades</label>
                                        </div>
                                        <div class="col-md-3" style="text-align: center !important;">
                                            <label for="unidades">&nbsp; </label>
                                        </div>
                                    </div>
                                    <div class="row justify-content-center mt-1">
                                        <div class="col-md-7">
                                            <input type="number" class="form-control"
                                                wire:model="unidades_mercaderia">
                                        </div>
                                        <div class="col-md-3" style="justify-content: start !important"
                                            style="display: flex;flex-direction: column;align-content: center;justify-content: center;align-items: center;">
                                            <button type="button" class="btn btn-primary w-100"
                                                wire:click.prevent="addMercaderia('{{ $mercaderia_seleccionada }}')"
                                                data-dismiss="modal" aria-label="Close">+</a>
                                        </div>
                                    </div>
                                @endif
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3" style="width: 23vw !important;">
            <div class="card m-b-30 position-fixed" style="width: -webkit-fill-available">
                <div class="card-body">
                    <h5>Opciones de guardado</h5>
                    <div class="row">
                        <div class="col-12">
                            <button class="w-100 btn btn-success mb-2" wire:click.prevent="alertaGuardar">Guardar
                                </button>
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
        <script>
            window.addEventListener('initializeMapKit', () => {
                fetch('/admin/service/jwt')
                    .then(response => response.json())
                    .then(data => {
                        mapkit.init({
                            authorizationCallback: function(done) {
                                done(data.token);
                            }
                        });
                        // Aquí puedes inicializar tu mapa u otras funcionalidades relacionadas
                    });
            });
        </script>
    </div>

    @section('scripts')
        {{-- <script src="https://cdn.datatables.net/responsive/2.4.0/js/dataTables.responsive.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.2/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.3.4/js/dataTables.buttons.min.js"></script> --}}
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
        {{-- <script src="https://cdn.datatables.net/buttons/2.3.4/js/buttons.html5.min.js"></script> --}}
        {{-- <script src="https://cdn.datatables.net/buttons/2.3.4/js/buttons.print.min.js"></script> --}}
        <script>
            // In your Javascript (external .js resource or <script> tag)

            $("#alertaGuardar").on("click", () => {
                Swal.fire({
                    title: '¿Estás seguro?',
                    icon: 'warning',
                    showConfirmButton: true,
                    showCancelButton: true
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.livewire.emit('submitEvento');
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
