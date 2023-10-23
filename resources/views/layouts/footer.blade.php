<footer class="row" style="align-items: center; padding: 0 1rem; justify-content: space-between;z-index: 15000;">
    <div class="exit-button-col" style="text-align:right;">
        <button class="footer-button" onclick="location.href='{{ URL::previous() }}'"><svg
                xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-arrow-back-up" width="44"
                height="44" viewBox="0 0 24 24" stroke-width="1.5" stroke="#35a8e0" fill="none"
                stroke-linecap="round" stroke-linejoin="round">
                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                <path d="M9 13l-4 -4l4 -4m-4 4h11a4 4 0 0 1 0 8h-1" />
            </svg>
            <span>VOLVER</span>
        </button>
    </div>
    <div class="menu_footer_new">
        @if ($user_rol != 4)
            <div class="exit-button-col">
                <a class="footer-button" href="{{ route('productos.index') }}">
                    <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-brand-codesandbox"
                        width="44" height="44" viewBox="0 0 24 24" stroke-width="1.5" stroke="#35a8e0"
                        fill="none" stroke-linecap="round" stroke-linejoin="round">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                        <path d="M20 7.5v9l-4 2.25l-4 2.25l-4 -2.25l-4 -2.25v-9l4 -2.25l4 -2.25l4 2.25z" />
                        <path d="M12 12l4 -2.25l4 -2.25" />
                        <line x1="12" y1="12" x2="12" y2="21" />
                        <path d="M12 12l-4 -2.25l-4 -2.25" />
                        <path d="M20 12l-4 2v4.75" />
                        <path d="M4 12l4 2l0 4.75" />
                        <path d="M8 5.25l4 2.25l4 -2.25" />
                    </svg>
                    <span>PRODUCTOS</span>
                </a>
            </div>
            <div class="exit-button-col">
                <a class="footer-button" href="{{ route('pedidos.create') }}">
                    <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-circle-plus"
                        width="44" height="44" viewBox="0 0 24 24" stroke-width="1.5" stroke="#35a8e0"
                        fill="none" stroke-linecap="round" stroke-linejoin="round">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                        <path d="M3 12a9 9 0 1 0 18 0a9 9 0 0 0 -18 0"></path>
                        <path d="M9 12h6"></path>
                        <path d="M12 9v6"></path>
                    </svg>
                    <span>NUEVO PEDIDO</span>
                </a>

            </div>
            <div class="exit-button-col">
                <a class="footer-button" href="{{ route('pedidos.index') }}">
                    <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-folder" width="44"
                        height="44" viewBox="0 0 24 24" stroke-width="1.5" stroke="#35a8e0" fill="none"
                        stroke-linecap="round" stroke-linejoin="round">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                        <path d="M5 4h4l3 3h7a2 2 0 0 1 2 2v8a2 2 0 0 1 -2 2h-14a2 2 0 0 1 -2 -2v-11a2 2 0 0 1 2 -2" />
                    </svg>
                    <span>PEDIDOS</span>
                </a>
            </div>
        @endif
        <div class="exit-button-col">
            <a class="footer-button" href="{{ route('clientes.index') }}">
                <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-users" width="44"
                    height="44" viewBox="0 0 24 24" stroke-width="1.5" stroke="#35a8e0" fill="none"
                    stroke-linecap="round" stroke-linejoin="round">
                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                    <circle cx="9" cy="7" r="4" />
                    <path d="M3 21v-2a4 4 0 0 1 4 -4h4a4 4 0 0 1 4 4v2" />
                    <path d="M16 3.13a4 4 0 0 1 0 7.75" />
                    <path d="M21 21v-2a4 4 0 0 0 -3 -3.85" />
                </svg>
                <span>CLIENTES</span>
            </a>
        </div>
        @if ($user_rol != 4)
            <div class="exit-button-col">
                <a class="footer-button" href="{{ route('facturas.index') }}">
                    <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-file-invoice"
                        width="44" height="44" viewBox="0 0 24 24" stroke-width="1.5" stroke="#35a8e0"
                        fill="none" stroke-linecap="round" stroke-linejoin="round">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                        <path d="M14 3v4a1 1 0 0 0 1 1h4"></path>
                        <path d="M17 21h-10a2 2 0 0 1 -2 -2v-14a2 2 0 0 1 2 -2h7l5 5v11a2 2 0 0 1 -2 2z"></path>
                        <path d="M9 7l1 0"></path>
                        <path d="M9 13l6 0"></path>
                        <path d="M13 17l2 0"></path>
                    </svg>
                    <span>FACTURAS</span>
                </a>
            </div>
        @endif
        @if ($user_rol == 1 || $user_rol == 4)
            <div class="exit-button-col">
                <a class="footer-button" href="{{ route('almacen.index') }}">
                    <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-file-3d"
                        width="44" height="44" viewBox="0 0 24 24" stroke-width="1.5" stroke="#35a8e0"
                        fill="none" stroke-linecap="round" stroke-linejoin="round">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                        <path d="M14 3v4a1 1 0 0 0 1 1h4"></path>
                        <path d="M17 21h-10a2 2 0 0 1 -2 -2v-14a2 2 0 0 1 2 -2h7l5 5v11a2 2 0 0 1 -2 2z"></path>
                        <path d="M12 13.5l4 -1.5"></path>
                        <path d="M8 11.846l4 1.654v4.5l4 -1.846v-4.308l-4 -1.846z"></path>
                        <path d="M8 12v4.2l4 1.8"></path>
                    </svg>
                    <span>ALMACÉN</span>
                </a>
            </div>
            <div class="exit-button-col">
                <a class="footer-button" href="{{ route('stock.index') }}">
                    <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-packages"
                        width="44" height="44" viewBox="0 0 24 24" stroke-width="1.5" stroke="#35a8e0"
                        fill="none" stroke-linecap="round" stroke-linejoin="round">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                        <path d="M7 16.5l-5 -3l5 -3l5 3v5.5l-5 3z"></path>
                        <path d="M2 13.5v5.5l5 3"></path>
                        <path d="M7 16.545l5 -3.03"></path>
                        <path d="M17 16.5l-5 -3l5 -3l5 3v5.5l-5 3z"></path>
                        <path d="M12 19l5 3"></path>
                        <path d="M17 16.5l5 -3"></path>
                        <path d="M12 13.5v-5.5l-5 -3l5 -3l5 3v5.5"></path>
                        <path d="M7 5.03v5.455"></path>
                        <path d="M12 8l5 -3"></path>
                    </svg>
                    <span>STOCK</span>
                </a>
            </div>
        @endif
        @if ($user_rol == 1)
            <div class="exit-button-col">
                <a class="footer-button" href="{{ route('usuarios.index') }}">
                    <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-user-cog"
                        width="44" height="44" viewBox="0 0 24 24" stroke-width="1.5" stroke="#35a8e0"
                        fill="none" stroke-linecap="round" stroke-linejoin="round">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                        <path d="M8 7a4 4 0 1 0 8 0a4 4 0 0 0 -8 0"></path>
                        <path d="M6 21v-2a4 4 0 0 1 4 -4h2.5"></path>
                        <path d="M19.001 19m-2 0a2 2 0 1 0 4 0a2 2 0 1 0 -4 0"></path>
                        <path d="M19.001 15.5v1.5"></path>
                        <path d="M19.001 21v1.5"></path>
                        <path d="M22.032 17.25l-1.299 .75"></path>
                        <path d="M17.27 20l-1.3 .75"></path>
                        <path d="M15.97 17.25l1.3 .75"></path>
                        <path d="M20.733 20l1.3 .75"></path>
                    </svg>
                    <span>ADMIN. USUARIOS</span>
                </a>
            </div>
        @endif
    </div>

    <div class="col-2 col-md-2 col-lg-1 exit-button-col">
        <button class="footer-button" href="{{ route('logout') }}"
            onclick="event.preventDefault();document.getElementById('cPnEf0Yn21GWvOwPEAvTtEmZ1IuHPGSMwogz4WnK').submit();">
            <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-logout" width="44"
                height="44" viewBox="0 0 24 24" stroke-width="1.5" stroke="#35a8e0" fill="none"
                stroke-linecap="round" stroke-linejoin="round">
                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                <path d="M14 8v-2a2 2 0 0 0 -2 -2h-7a2 2 0 0 0 -2 2v12a2 2 0 0 0 2 2h7a2 2 0 0 0 2 -2v-2" />
                <path d="M7 12h14l-3 -3m0 6l3 -3" />
            </svg><span>SALIR</span>
        </button>
        <form id="cPnEf0Yn21GWvOwPEAvTtEmZ1IuHPGSMwogz4WnK"
            action="{{ route('logout') }}" method="POST" style="display: none;">
            <input type="hidden" name="_token" value="{{ ['_token' => csrf_token()]['_token'] }}">
        </form>
    </div>
    <script src="https://code.jquery.com/jquery-3.7.0.slim.js"
        integrity="sha256-7GO+jepT9gJe9LB4XFf8snVOjX3iYNb0FHYr5LI1N5c=" crossorigin="anonymous"></script>
    <link rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/@sweetalert2/themes@5.0.15/bootstrap-4/bootstrap-4.css" />
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.js"></script>
    @if (isset($alertas))
        <script>
            $(document).ready(function() {
                console.log('hola')
                $('#eliminarCarrito').on('click', function(e) {
                    e.preventDefault()
                    var id = $(this).attr('data-id')
                    console.log(id)
                    peticionEliminarCarrito(id)
                })
            })

            function peticionEliminarCarrito(id) {

                Swal.fire({
                    title: '¿Estas segunro de Eliminar el pedido?',
                    text: "Esta es una acción irreversible",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Si'
                }).then((result) => {


                    if (result.isConfirmed) {

                        $.when(eliminarPedido(id)).then(function(data, textStatus, jqXHR) {
                            // alert( jqXHR.status ); // Alerts 200
                            if (jqXHR.status == 200) {
                                Swal.fire({
                                    position: 'top-end',
                                    icon: 'success',
                                    title: 'El Pedido a sido cancelado',
                                    showConfirmButton: false,
                                    timer: 1500
                                })
                                window.location.href = 'https://ventamayorista.crmhawkins.com/admin/dashboard';
                            } else {
                                Swal.fire({
                                    position: 'top-end',
                                    icon: 'error',
                                    title: 'El proceso ha fallado, intentelo de nuevo mas tarde.',
                                    showConfirmButton: false,
                                    timer: 1500
                                })
                            }
                        });
                    }
                })
            }

            function eliminarPedido(id) {
                return $.ajax({
                    type: "POST",
                    url: '/admin/pedidos/borrarPedido',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                    },
                    data: {
                        'id_pedido': id
                    },
                    dataType: "json"
                });
            }

            function confirmarAlerta(alerta) {
                return $.ajax({
                    type: "POST",
                    url: '/admin/almacen/confirmarAlerta',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                    },
                    data: {
                        'alerta': alerta
                    },
                    dataType: "json"
                });
            }

            let alertas = @json($alertas);
            const steps = [];
            let alertasMostrar = [];
            let contador = 1;
            console.log(alertas);
            let alertasEnviar = (alertas) => {
                for (let index = 0; index < alertas.length; index++) {
                    if (alertas[index].stage_id == 40) {
                        steps.push(contador.toString());
                        alertasMostrar.push(alertas[index]);
                        contador++;
                    }

                }
            }
            alertasEnviar(alertas);
            async function mostrarAlerta(alertasMostrar) {
                console.log(alertasMostrar)
                const Queue = Swal.mixin({
                    confirmButtonText: 'Siguiente >',
                    // optional classes to avoid backdrop blinking between steps
                    showClass: {
                        backdrop: 'swal2-noanimation'
                    },
                    hideClass: {
                        backdrop: 'swal2-noanimation'
                    }
                })
                for (let i = 0; i < alertasMostrar.length; i++) {
                    await Queue.fire({
                        title: "Modificación en stock de pedido",
                        text: alertasMostrar[i].description,
                        icon: 'warning',
                        showClass: {
                            backdrop: 'swal2-noanimation'
                        },
                    })
                    confirmarAlerta(alertasMostrar[i]);
                }

            }
            mostrarAlerta(alertasMostrar);
        </script>
    @endif
    <script>
        function peticionEliminarCarrito(id) {

            Swal.fire({
                title: '¿Estas segunro de Eliminar el pedido?',
                text: "Esta es una acción irreversible",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Si'
            }).then((result) => {


                if (result.isConfirmed) {

                    $.when(eliminarPedido(id)).then(function(data, textStatus, jqXHR) {
                        // alert( jqXHR.status ); // Alerts 200
                        if (jqXHR.status == 200) {
                            Swal.fire({
                                position: 'top-end',
                                icon: 'success',
                                title: 'El Pedido a sido cancelado',
                                showConfirmButton: false,
                                timer: 1500
                            })
                            window.location.href = 'https://ventamayorista.crmhawkins.com/admin/dashboard';
                        } else {
                            Swal.fire({
                                position: 'top-end',
                                icon: 'error',
                                title: 'El proceso ha fallado, intentelo de nuevo mas tarde.',
                                showConfirmButton: false,
                                timer: 1500
                            })
                        }
                    });
                }
            })
        }

        function eliminarPedido(id) {
            return $.ajax({
                type: "POST",
                url: '/admin/pedidos/borrarPedido',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                },
                data: {
                    'id_pedido': id
                },
                dataType: "json"
            });
        }
    </script>
</footer>
