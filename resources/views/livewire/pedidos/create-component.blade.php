@php
$mostrarElemento = Auth::user()->role == 2;
$EsAdmin = Auth::user()->isAdmin();
$canEdit = $EsAdmin; //|| $estado == 1;
@endphp
<div class="container-fluid">
    <div class="page-title-box">
        <div class="row align-items-center">
            <div class="col-sm-6">
                <h4 class="page-title">TRAMITAR PEDIDO</span></h4>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-right">
                    <li class="breadcrumb-item"><a href="javascript:void(0);">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="javascript:void(0);">Pedidos</a></li>
                    <li class="breadcrumb-item active">Tramitar pedido</li>
                </ol>
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
    <div class="row" style="align-items: start !important">
        <div class="col-md-9">
            <div class="card m-b-30">
                <div class="card-body">
                    <div class="form-row justify-content-center">
                        <div class="form-group col-md-12">
                            <h5 class="ms-3"
                                style="border-bottom: 1px gray solid !important; padding-bottom: 10px !important;">Datos
                                básicos del pedido</h5>
                        </div>
                        <div class="form-group col-md-4" wire:ignore>
                            <div x-data="" x-init="$('#select2-cliente').select2({placeholder: '-- Selecciona un cliente --'});
                            $('#select2-cliente').on('change', function(e) {
                                var data = $('#select2-cliente').select2('val');
                                @this.set('cliente_id', data);
                                @this.call('selectCliente');
                            });">
                                <label for="Cliente">Cliente</label>
                                <select class="form-control" name="cliente_id" id="select2-cliente"
                                    wire:model="cliente_id" wire:change="onChangeCliente()" >
                                    <option value=""></option>
                                    @foreach ($clientes as $cliente)
                                        <option value="{{ $cliente->id }}">{{ $cliente->nombre }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="form-group col-md-3">
                            <label for="fecha">Fecha</label>
                            <input type="text" value="{{ $fecha }}" class="form-control" disabled>
                        </div>
                        {{--<div class="form-group col-md-3" wire:ignore>
                            <div x-data="" x-init="$('#select2-estado').select2();
                            $('#select2-estado').on('change', function(e) {
                                var data = $('#select2-estado').select2('val');
                                @this.set('estado', data);
                            });">
                                <label for="estado">Estado</label>
                                <select class="form-control" name="estado" id="select2-estado"
                                    value="{{ $estado }}">
                                    <option value="Pendiente">Pendiente</option>
                                    <option value="Cancelado">Cancelado</option>
                                    <option value="Aceptado">Aceptado</option>
                                    <option value="Completado">Completado</option>
                                    <option value="Facturado">Facturado</option>
                                </select>
                            </div>
                        </div>--}}
                        <div class="form-group col-md-4" wire:ignore>
                            <div x-data="" x-init="$('#select2-tipo').select2();
                            $('#select2-tipo').on('change', function(e) {
                                var data = $('#select2-tipo').select2('val');
                                @this.set('tipo_pedido_id', data);
                            });">
                                <label for="fechaVencimiento">Tipo de pedido</label>
                                <select class="form-control" name="estado" id="select2-tipo"
                                    value="{{ $tipo_pedido_id }}">
                                    <option value="0">Albarán y factura</option>
                                    <option value="1">Albarán sin factura</option>
                                </select>
                            </div>
                        </div>
                        @if($mostrarElemento)
                            <!-- Si la condición es verdadera, muestra esto -->
                            <div class="form-group col-md-6" wire:ignore>

                                <div x-data="" x-init="$('#select2-almacen').select2();
                                    $('#select2-almacen').on('change', function(e) {
                                    var data = $('#select2-almacen').select2('val');
                                    @this.set('almacen_id', data);
                                    });">
                                    <label for="fechaVencimiento">Almacen</label>
                                    <select name="almacen" id="select2-almacen" wire:model="almacen_id" style="width: 100% !important">
                                        <option value="{{ null }}">-- Selecciona un almacén --</option>
                                        @foreach ($almacenes as $presup)
                                            <option value="{{ $presup->id }}">{{ $presup->almacen }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        @endif
                    </div>
                    <div class="form-row justify-content-center">
                        <div class="form-group col-md-12">
                            <h5 class="ms-3"
                                style="border-bottom: 1px gray solid !important; padding-bottom: 10px !important;">Datos
                                de envío</h5>
                        </div>
                        <div class="form-group col-md-5">
                            <label for="localidad_entrega">Dirección</label>
                            <input type="text" wire:model="direccion_entrega" class="form-control" readonly>
                        </div>
                        <div class="form-group col-md-1">
                            &nbsp;
                        </div>
                        <div class="form-group col-md-5">
                            <label for="localidad_entrega">Localidad</label>
                            <input type="text" wire:model="localidad_entrega" class="form-control" readonly>
                        </div>
                    </div>
                    <div class="form-row justify-content-center">
                        <div class="form-group col-md-5">
                            <label for="provincia_entrega">Provincia</label>
                            <input type="text" wire:model="provincia_entrega" class="form-control" readonly>
                        </div>
                        <div class="form-group col-md-1">
                            &nbsp;
                        </div>
                        <div class="form-group col-md-5">
                            <label for="cod_postal_entrega">Código postal</label>
                            <input type="text" wire:model="cod_postal_entrega" class="form-control" readonly>
                        </div>
                    </div>
                    <div class="form-row mb-4 justify-content-center">
                       {{-- <div class="form-group col-md-5">
                            <label for="fecha">Órden de entrega</label>
                            <input type="text" wire:model="orden_entrega" class="form-control">
                        </div>
                        <div class="form-group col-md-1">
                            &nbsp;
                        </div>--}}
                        <div class="form-group col-md-11">
                            <label for="fecha">Observaciones</label>
                            <textarea wire:model="observaciones" class="form-control"></textarea>
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
                                    style="align-self: end !important;" data-target="#addProductModal" wire:click="isClienteSeleccionado()">Añadir</button>
                            </h5>
                            <div class="form-group col-md-12">
                                @if (count($productos_pedido) > 0)
                                    <table class="table ms-3 table-striped table-bordered dt-responsive nowrap">
                                        <thead>
                                            <tr>
                                                <th>Producto</th>
                                                <th>Cantidad</th>
                                                <th>Precio unidad</th>
                                                <th>Precio total</th>
                                                <th>Eliminar</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($productos_pedido as $productoIndex => $producto)
                                                <tr>
                                                    <td>{{ $this->getNombreTabla($producto['producto_pedido_id']) }}</td>
                                                    <td>{{ $this->getUnidadesTabla($productoIndex) }}</td>
                                                    <td><input type="number" wire:model.lazy="productos_pedido.{{ $productoIndex }}.precio_ud" wire:change="actualizarPrecioTotal({{$productoIndex}})" class="form-control" style="width:70%; display:inline-block">€</td>
                                                    <td>{{ $producto['precio_total']}} €</td>
                                                    <td><button type="button" class="btn btn-danger" wire:click="deleteArticulo('{{$productoIndex}}')">X</button></td>
                                                </tr>
                                            @endforeach
                                            <tr>
                                                <th colspan="3">Precio estimado</th>
                                                <th>{{ $precioSinDescuento }} €</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                @endif
                            </div>
                        </div>
                        <div class="form-group col-md-5 d-flex align-items-center">
                            <div class="form-group col-md-6 d-flex align-items-center">
                            <label for="descuento">Descuento</label>
                            <input type="checkbox" id="descuento" wire:model="descuento" class="form-checkbox" wire:change='setPrecioEstimado()' style="margin-left: 10px; width: 20px; height: 20px;">
                        </div>
                            @if ($descuento)
                            <div class="form-group col-md-6 d-flex flex-column justify-content-center">
                            <label>Porcentaje descuento</label>
                            <input class="form-control"  type="number" wire:model="porcentaje_descuento"  wire:change='setPrecioEstimado()' placeholder="Ingrese el valor del descuento">
                            </div>
                         @endif
                        </div>
                        <div class="form-group col-md-1">
                           &nbsp;
                        </div>
                        
                    </div>
                    @if (count($productos_pedido) > 0)
                    <div class="d-flex col-12">
                        <div class="form-group col-md-4">
                            <label for="subtotal">Subtotal</label>
                            <input type="text" wire:model="subtotal" class="form-control" readonly>
                        </div>

                        <div class="form-group col-md-4">
                            <label for="subtotal">Descuento total</label>
                            <input type="text" wire:model="descuento_total" class="form-control" readonly>
                        </div>
                        <div class="form-group col-md-4">
                            <label for="fecha">Precio final</label>
                            <input type="text" wire:model="precio" class="form-control" readonly>
                        </div>
                    </div>
                    @endif
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
                                @if ($producto_seleccionado != null)
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
                                                        {{ $this->getProductoNombre() }}</h2>
                                                    <img class="mx-auto" src="{{ $this->getProductoImg() }}"
                                                        style="max-width: 30%; text-align:center;"
                                                        alt="Card image cap">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                                <div class="row justify-content-center">
                                    <div class="col-md-10" style="text-align: center !important;">
                                        <label for="fechaVencimiento">Producto seleccionado</label>
                                    </div>
                                    <div class="col-md-10" wire:ignore>
                                        <div x-data="" x-init="$('#select2-producto').select2();
                                        $('#select2-producto').on('change', function(e) {
                                            var data = $('#select2-producto').select2('val');
                                            @this.set('producto_seleccionado', data);
                                            @this.set('unidades_pallet_producto', 0);
                                            @this.set('unidades_caja_producto', 0);
                                            @this.set('unidades_producto', 0);
                                            console.log('data');
                                        });">
                                            <select name="producto" id="select2-producto"
                                                wire:model="producto_seleccionado" style="width: 100% !important">
                                                <option value="{{ null }}">-- Selecciona un producto --
                                                </option>
                                                @foreach ($productos as $presup)
                                                    <option value="{{ $presup->id }}">{{ $presup->nombre }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                @if ($producto_seleccionado != null)
                                    <div class="row justify-content-center mt-1">
                                        <div class="col-md-3" style="text-align: center !important;">
                                            <label for="fechaVencimiento">Pallets</label>
                                        </div>
                                        <div class="col-md-3" style="text-align: center !important;">
                                            <label for="fechaVencimiento">Cajas</label>
                                        </div>
                                        <div class="col-md-3" style="text-align: center !important;">
                                            <label for="unidades">Uds.</label>
                                        </div>
                                        <div class="col-md-3" style="text-align: center !important;">
                                            <label for="unidades">&nbsp; </label>
                                        </div>
                                    </div>
                                    <div class="row justify-content-center mt-1">
                                        <div class="col-md-3">
                                            <input type="number" class="form-control" wire:model="unidades_pallet_producto" wire:change='updatePallet()'>
                                        </div>
                                        <div class="col-md-3">
                                            <input type="number" class="form-control" wire:model="unidades_caja_producto" wire:change='updateCaja()'>
                                        </div>
                                        <div class="col-md-3">
                                            <input type="number" class="form-control" wire:model="unidades_producto"  wire:change='updateUnidad()'>
                                        </div>
                                        <div class="col-md-3" style="justify-content: start !important"
                                            style="display: flex;flex-direction: column;align-content: center;justify-content: center;align-items: center;">
                                            <button type="button" class="btn btn-primary w-100"
                                                wire:click.prevent="addProductos('{{ $producto_seleccionado }}')"
                                                data-dismiss="modal" aria-label="Close">+</a>
                                        </div>
                                    </div>
                                    <div class="row justify-content-center">
                                        <div class="col-11 mt-3">

                                            <input name="sinCargo" class="form-check-input" type="checkbox" id="sinCargo" wire:model="sinCargo">
                                            <label for="sinCargo" style="cursor:pointer"> Producto sin cargos.</label>
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
        <div class="col-md-3">
            <div class="card m-b-30" >
                <div class="card-body">
                    <h5>Opciones de guardado</h5>
                    <div class="row">
                        <div class="col-12">
                            <button class="w-100 btn btn-success mb-2" wire:click.prevent="alertaGuardar">Guardar
                                presupuesto</button>
                        </div>
                    </div>
                    <div class="row">
                        @if(count($anotacionesProximoPedido) > 0 )
                            <div class="col-12">
                                <button class="w-100 btn btn-info mb-2" id="verAnotaciones" data-toggle="modal" data-target="#viewModal">Ver
                                    Anotaciones</button>
                            </div>
                        @endif
                        
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
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>
        {{-- <script src="https://cdn.datatables.net/buttons/2.3.4/js/buttons.html5.min.js"></script> --}}
        {{-- <script src="https://cdn.datatables.net/buttons/2.3.4/js/buttons.print.min.js"></script> --}}
        <script>
            // In your Javascript (external .js resource or <script> tag)
                Livewire.on('closeModal', () => {
                    setTimeout(() => {
                        $('#addProductModal').modal('hide');
                    }, 2000);
                    console.log('cerrar modal')
                   
                });
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
