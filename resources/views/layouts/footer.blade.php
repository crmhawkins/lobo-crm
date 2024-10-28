    <style>
    footer {
    display: grid;
    justify-content: center;
    align-content: start;
    grid-gap: 10px;
    padding: 2%;
    position: relative;
    height: fit-content;
    min-height: 100vh;
}

.menu_footer_new {
    display: grid; /* Oculta el menú inicialmente */

    grid-template-columns: repeat(1, minmax(0, 1fr));
    justify-items: center;
    gap: 10px;
}

.exit-button-col {
    display: grid;
    grid-template-columns: auto 1fr;
    justify-content: center;
    align-items: center;
    width: 100%;
    padding: 10px 0;
    border-bottom: 1px solid transparent; /* Preparar para el gradiente */
    background-image: linear-gradient(to right, #009ffd, #2a2a72); /* Cambia a los colores que prefieras */
    background-repeat: no-repeat;
    background-size: 100% 1px; /* Tamaño del "borde" creado con el gradiente */
    background-position: bottom;
}
.exit-button-col:last-child {
    background-image: none; /* Eliminar el gradiente del último elemento */
}
.footer-button {
    display: grid;
    grid-template-columns: auto 1fr; /* Icono y texto */
    align-items: center;
    gap: 10px;
    width: 100%;
    text-align: center;
}

.footer-button > svg, .footer-button > i {
    justify-self: center; /* Centra el icono */
    width: 30px; /* Tamaño consistente para los iconos */
    font-size: 30px !important; /* Tamaño consistente para los iconos tipo i */
}
        /* Estilo para el botón de alternar */
.toggle-button {
    display: block;
    margin: 10px auto; /* Centra el botón horizontalmente */
    padding: 10px 20px;
    cursor: pointer;
}
    </style>
@switch($user_rol)
    @case(1)
        <footer class="" style="">
            {{-- <div class="exit-button-col" style="text-align:right;">
                <button class="footer-button" onclick="location.href='{{ URL::previous() }}'"><svg
                        xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-arrow-back-up" width="44"
                        height="44" viewBox="0 0 24 24" stroke-width="1.5" stroke="#35a8e0" fill="none"
                        stroke-linecap="round" stroke-linejoin="round">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                        <path d="M9 13l-4 -4l4 -4m-4 4h11a4 4 0 0 1 0 8h-1" />
                    </svg>
                    <span>VOLVER</span>
                </button>
            </div> --}}
            <div class="menu_footer_new">
                <div class="exit-button-col">
                    <a class="footer-button" href="{{ route('productos.index') }}">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-bottle-filled"
                            width="44" height="44" viewBox="0 0 24 24" stroke-width="1.5" stroke="#35a8e0" fill="none"
                            stroke-linecap="round" stroke-linejoin="round">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                            <path
                                d="M13 1a2 2 0 0 1 1.995 1.85l.005 .15v.5c0 1.317 .381 2.604 1.094 3.705l.17 .25l.05 .072a9.093 9.093 0 0 1 1.68 4.92l.006 .354v6.199a3 3 0 0 1 -2.824 2.995l-.176 .005h-6a3 3 0 0 1 -2.995 -2.824l-.005 -.176v-6.2a9.1 9.1 0 0 1 1.486 -4.982l.2 -.292l.05 -.069a6.823 6.823 0 0 0 1.264 -3.957v-.5a2 2 0 0 1 1.85 -1.995l.15 -.005h2zm.362 5h-2.724a8.827 8.827 0 0 1 -1.08 2.334l-.194 .284l-.05 .069a7.091 7.091 0 0 0 -1.307 3.798l-.003 .125a3.33 3.33 0 0 1 1.975 -.61a3.4 3.4 0 0 1 2.833 1.417c.27 .375 .706 .593 1.209 .583a1.4 1.4 0 0 0 1.166 -.583a3.4 3.4 0 0 1 .81 -.8l.003 .183c0 -1.37 -.396 -2.707 -1.137 -3.852l-.228 -.332a8.827 8.827 0 0 1 -1.273 -2.616z"
                                stroke-width="0" fill="currentColor" />
                        </svg>
                        <span>PRODUCTOS</span>
                    </a>
                </div>
                <div class="exit-button-col">
                    <a class="footer-button" href="{{ route('pedidos.create') }}">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-circle-plus" width="44"
                            height="44" viewBox="0 0 24 24" stroke-width="1.5" stroke="#35a8e0" fill="none"
                            stroke-linecap="round" stroke-linejoin="round">
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
                <div class="exit-button-col">
                    <a class="footer-button" href="{{ route('comercial.clientes') }}">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 512" width="44"
                        height="44" troke-width="1.5" stroke="#35a8e0" fill="#35A8E0"
                        stroke-linecap="round" stroke-linejoin="round">
                            <!--!Font Awesome Free 6.6.0 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2024 Fonticons, Inc.-->
                            <path d="M160 64c0-35.3 28.7-64 64-64L576 0c35.3 0 64 28.7 64 64l0 288c0 35.3-28.7 64-64 64l-239.2 0c-11.8-25.5-29.9-47.5-52.4-64l99.6 0 0-32c0-17.7 14.3-32 32-32l64 0c17.7 0 32 14.3 32 32l0 32 64 0 0-288L224 64l0 49.1C205.2 102.2 183.3 96 160 96l0-32zm0 64a96 96 0 1 1 0 192 96 96 0 1 1 0-192zM133.3 352l53.3 0C260.3 352 320 411.7 320 485.3c0 14.7-11.9 26.7-26.7 26.7L26.7 512C11.9 512 0 500.1 0 485.3C0 411.7 59.7 352 133.3 352z"/>
                        </svg>
                        <span>COMERCIAL</span>
                    </a>
                </div>
                <div class="exit-button-col">
                    <a class="footer-button" href="{{ route('facturas.index') }}">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-file-invoice" width="44"
                            height="44" viewBox="0 0 24 24" stroke-width="1.5" stroke="#35a8e0" fill="none"
                            stroke-linecap="round" stroke-linejoin="round">
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
                <div class="exit-button-col">
                    <a class="footer-button" href="{{ route('almacen.index') }}">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-file-3d" width="44"
                            height="44" viewBox="0 0 24 24" stroke-width="1.5" stroke="#35a8e0" fill="none"
                            stroke-linecap="round" stroke-linejoin="round">
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
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-packages" width="44"
                            height="44" viewBox="0 0 24 24" stroke-width="1.5" stroke="#35a8e0" fill="none"
                            stroke-linecap="round" stroke-linejoin="round">
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
                <div class="exit-button-col">
                    <a class="footer-button" href="{{ route('mercaderia.index') }}">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-sticker" width="44"
                            height="44" viewBox="0 0 24 24" stroke-width="1.5" stroke="#35a8e0" fill="none"
                            stroke-linecap="round" stroke-linejoin="round">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                            <path d="M20 12l-2 .5a6 6 0 0 1 -6.5 -6.5l.5 -2l8 8" />
                            <path d="M20 12a8 8 0 1 1 -8 -8" />
                        </svg>
                        <span>MATERIALES</span>
                    </a>
                </div>
                <div class="exit-button-col">
                    <a class="footer-button" href="{{ route('produccion.index') }}">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-building-factory-2"
                            width="44" height="44" viewBox="0 0 24 24" stroke-width="1.5" stroke="#35a8e0"
                            fill="none" stroke-linecap="round" stroke-linejoin="round">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                            <path d="M3 21h18" />
                            <path d="M5 21v-12l5 4v-4l5 4h4" />
                            <path d="M19 21v-8l-1.436 -9.574a.5 .5 0 0 0 -.495 -.426h-1.145a.5 .5 0 0 0 -.494 .418l-1.43 8.582" />
                            <path d="M9 17h1" />
                            <path d="M14 17h1" />
                        </svg>
                        <span>PRODUCCIÓN</span>
                    </a>
                </div>
                <div class="exit-button-col">
                    <a class="footer-button" href="{{ route('caja.index') }}">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-safe" width="44" height="44" viewBox="0 0 24 24" stroke-width="1.5" stroke="#35a8e0" fill="none" stroke-linecap="round" stroke-linejoin="round">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                            <rect x="3" y="7" width="18" height="14" rx="2" />
                            <path d="M7 7v-1a2 2 0 0 1 2 -2h6a2 2 0 0 1 2 2v1" />
                            <path d="M12 10v4" />
                            <circle cx="12" cy="14" r="2" />
                            <path d="M10 14h4" />
                          </svg>
                        <span>CAJA</span>
                    </a>
                </div>
                <div class="exit-button-col">
                    <a class="footer-button" href="{{ route('contabilidad.index') }}">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 576 512" width="44"
                        height="44" stroke-width="40" stroke="#35a8e0" fill="none">
                            <path d="M249.6 471.5c10.8 3.8 22.4-4.1 22.4-15.5l0-377.4c0-4.2-1.6-8.4-5-11C247.4 52 202.4 32 144 32C93.5 32 46.3 45.3 18.1 56.1C6.8 60.5 0 71.7 0 83.8L0 454.1c0 11.9 12.8 20.2 24.1 16.5C55.6 460.1 105.5 448 144 448c33.9 0 79 14 105.6 23.5zm76.8 0C353 462 398.1 448 432 448c38.5 0 88.4 12.1 119.9 22.6c11.3 3.8 24.1-4.6 24.1-16.5l0-370.3c0-12.1-6.8-23.3-18.1-27.6C529.7 45.3 482.5 32 432 32c-58.4 0-103.4 20-123 35.6c-3.3 2.6-5 6.8-5 11L304 456c0 11.4 11.7 19.3 22.4 15.5z"/></svg>
                         <span>CONTABILIDAD</span>
                    </a>
                </div>
                <div class="exit-button-col">
                    <a class="footer-button" href="{{ route('admin.incidencias.index') }}">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 384 512" width="44"
                        height="44" fill="#35a8e0"><!--!Font Awesome Free 6.6.0 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2024 Fonticons, Inc.--><path d="M112.5 301.4c0-73.8 105.1-122.5 105.1-203 0-47.1-34-88-39.1-90.4 .4 3.3 .6 6.7 .6 10C179.1 110.1 32 171.9 32 286.6c0 49.8 32.2 79.2 66.5 108.3 65.1 46.7 78.1 71.4 78.1 86.6 0 10.1-4.8 17-4.8 22.3 13.1-16.7 17.4-31.9 17.5-46.4 0-29.6-21.7-56.3-44.2-86.5-16-22.3-32.6-42.6-32.6-69.5zm205.3-39c-12.1-66.8-78-124.4-94.7-130.9l4 7.2c2.4 5.1 3.4 10.9 3.4 17.1 0 44.7-54.2 111.2-56.6 116.7-2.2 5.1-3.2 10.5-3.2 15.8 0 20.1 15.2 42.1 17.9 42.1 2.4 0 56.6-55.4 58.1-87.7 6.4 11.7 9.1 22.6 9.1 33.4 0 41.2-41.8 96.9-41.8 96.9 0 11.6 31.9 53.2 35.5 53.2 1 0 2.2-1.4 3.2-2.4 37.9-39.3 67.3-85 67.3-136.8 0-8-.7-16.2-2.2-24.6z"/></svg>
                        <span>INCIDENCIAS </span>
                    </a>
                </div>
                <div class="exit-button-col">
                    <a class="footer-button" href="{{ route('proveedores.index') }}">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-users" width="44"
                            height="44" viewBox="0 0 24 24" stroke-width="1.5" stroke="#35a8e0" fill="none"
                            stroke-linecap="round" stroke-linejoin="round">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                            <circle cx="9" cy="7" r="4" />
                            <path d="M3 21v-2a4 4 0 0 1 4 -4h4a4 4 0 0 1 4 4v2" />
                            <path d="M16 3.13a4 4 0 0 1 0 7.75" />
                            <path d="M21 21v-2a4 4 0 0 0 -3 -3.85" />
                        </svg>
                        <span>PROVEEDORES</span>
                    </a>
                </div>
                <div class="exit-button-col">
                    <a class="footer-button" href="{{ route('usuarios.index') }}">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-user-cog" width="44"
                            height="44" viewBox="0 0 24 24" stroke-width="1.5" stroke="#35a8e0" fill="none"
                            stroke-linecap="round" stroke-linejoin="round">
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
                        <span>ADMIN. USUARIOS </span>
                    </a>
                </div>
                <div class="exit-button-col">
                    <a class="footer-button" href="{{ route('calendario.index') }}">
                        <i class="fa-regular fa-calendar" style="font-size: 44px"></i>
                        <span>EVENTOS</span>
                    </a>
                </div>
                <div class="exit-button-col">
                    <a class="footer-button" href="{{ route('alertas.popup') }}">
                        <i class="fa-regular fa-bell" style="font-size: 44px;"></i>
                        <span>ALERTAS</span>
                    </a>
                </div>
                <div class="exit-button-col">
                    <a class="footer-button" href="{{ route('configuracion.edit') }}">
                        <i class="fa-solid fa-gears" style="font-size: 44px"></i>
                        <span>OPCIONES </span>
                    </a>
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
                    <form id="cPnEf0Yn21GWvOwPEAvTtEmZ1IuHPGSMwogz4WnK" action="{{ route('logout') }}" method="POST"
                        style="display: none;">
                        <input type="hidden" name="_token" value="{{ ['_token' => csrf_token()]['_token'] }}">
                    </form>
                </div>
                
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
    @break

    @case(2)
        <footer >
            {{-- <div class="exit-button-col" style="text-align:right;">
                <button class="footer-button" onclick="location.href='{{ URL::previous() }}'"><svg
                        xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-arrow-back-up" width="44"
                        height="44" viewBox="0 0 24 24" stroke-width="1.5" stroke="#35a8e0" fill="none"
                        stroke-linecap="round" stroke-linejoin="round">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                        <path d="M9 13l-4 -4l4 -4m-4 4h11a4 4 0 0 1 0 8h-1" />
                    </svg>
                    <span>VOLVER</span>
                </button>
            </div> --}}
            <div class="menu_footer_new">
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
                <div class="exit-button-col">
                    <a class="footer-button" href="{{ route('comercial.clientes') }}">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 512" width="44"
                        height="44" troke-width="1.5" stroke="#35a8e0" fill="#35A8E0"
                        stroke-linecap="round" stroke-linejoin="round">
                            <!--!Font Awesome Free 6.6.0 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2024 Fonticons, Inc.-->
                            <path d="M160 64c0-35.3 28.7-64 64-64L576 0c35.3 0 64 28.7 64 64l0 288c0 35.3-28.7 64-64 64l-239.2 0c-11.8-25.5-29.9-47.5-52.4-64l99.6 0 0-32c0-17.7 14.3-32 32-32l64 0c17.7 0 32 14.3 32 32l0 32 64 0 0-288L224 64l0 49.1C205.2 102.2 183.3 96 160 96l0-32zm0 64a96 96 0 1 1 0 192 96 96 0 1 1 0-192zM133.3 352l53.3 0C260.3 352 320 411.7 320 485.3c0 14.7-11.9 26.7-26.7 26.7L26.7 512C11.9 512 0 500.1 0 485.3C0 411.7 59.7 352 133.3 352z"/>
                        </svg>
                        <span>COMERCIAL</span>
                    </a>
                </div>
                <div class="exit-button-col">
                    <a class="footer-button" href="{{ route('stock.index') }}">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-packages" width="44"
                            height="44" viewBox="0 0 24 24" stroke-width="1.5" stroke="#35a8e0" fill="none"
                            stroke-linecap="round" stroke-linejoin="round">
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
                <div class="exit-button-col">
                    <a class="footer-button" href="{{ route('admin.incidencias.index') }}">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 384 512" width="44"
                        height="44" fill="#35a8e0"><!--!Font Awesome Free 6.6.0 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2024 Fonticons, Inc.--><path d="M112.5 301.4c0-73.8 105.1-122.5 105.1-203 0-47.1-34-88-39.1-90.4 .4 3.3 .6 6.7 .6 10C179.1 110.1 32 171.9 32 286.6c0 49.8 32.2 79.2 66.5 108.3 65.1 46.7 78.1 71.4 78.1 86.6 0 10.1-4.8 17-4.8 22.3 13.1-16.7 17.4-31.9 17.5-46.4 0-29.6-21.7-56.3-44.2-86.5-16-22.3-32.6-42.6-32.6-69.5zm205.3-39c-12.1-66.8-78-124.4-94.7-130.9l4 7.2c2.4 5.1 3.4 10.9 3.4 17.1 0 44.7-54.2 111.2-56.6 116.7-2.2 5.1-3.2 10.5-3.2 15.8 0 20.1 15.2 42.1 17.9 42.1 2.4 0 56.6-55.4 58.1-87.7 6.4 11.7 9.1 22.6 9.1 33.4 0 41.2-41.8 96.9-41.8 96.9 0 11.6 31.9 53.2 35.5 53.2 1 0 2.2-1.4 3.2-2.4 37.9-39.3 67.3-85 67.3-136.8 0-8-.7-16.2-2.2-24.6z"/></svg>
                        <span>INCIDENCIAS </span>
                    </a>
                </div>
                <div class="exit-button-col">
                    <a class="footer-button" href="{{ route('alertas.popup') }}">
                        <i class="fa-regular fa-bell" style="font-size: 44px;"></i>
                        <span>ALERTAS</span>
                    </a>
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
                    <form id="cPnEf0Yn21GWvOwPEAvTtEmZ1IuHPGSMwogz4WnK" action="{{ route('logout') }}" method="POST"
                        style="display: none;">
                        <input type="hidden" name="_token" value="{{ ['_token' => csrf_token()]['_token'] }}">
                    </form>
                </div>
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
    @break

    @case(3)
        <footer >
            {{-- <div class="exit-button-col" style="text-align:right;">
                <button class="footer-button" onclick="location.href='{{ URL::previous() }}'"><svg
                        xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-arrow-back-up" width="44"
                        height="44" viewBox="0 0 24 24" stroke-width="1.5" stroke="#35a8e0" fill="none"
                        stroke-linecap="round" stroke-linejoin="round">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                        <path d="M9 13l-4 -4l4 -4m-4 4h11a4 4 0 0 1 0 8h-1" />
                    </svg>
                    <span>VOLVER</span>
                </button>
            </div> --}}
            <div class="menu_footer_new">
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
                <div class="exit-button-col">
                    <a class="footer-button" href="{{ route('facturas.index') }}">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-file-invoice" width="44"
                            height="44" viewBox="0 0 24 24" stroke-width="1.5" stroke="#35a8e0" fill="none"
                            stroke-linecap="round" stroke-linejoin="round">
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
                <div class="exit-button-col">
                    <a class="footer-button" href="{{ route('comercial.clientes') }}">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 512" width="44"
                        height="44" troke-width="1.5" stroke="#35a8e0" fill="#35A8E0"
                        stroke-linecap="round" stroke-linejoin="round">
                            <!--!Font Awesome Free 6.6.0 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2024 Fonticons, Inc.-->
                            <path d="M160 64c0-35.3 28.7-64 64-64L576 0c35.3 0 64 28.7 64 64l0 288c0 35.3-28.7 64-64 64l-239.2 0c-11.8-25.5-29.9-47.5-52.4-64l99.6 0 0-32c0-17.7 14.3-32 32-32l64 0c17.7 0 32 14.3 32 32l0 32 64 0 0-288L224 64l0 49.1C205.2 102.2 183.3 96 160 96l0-32zm0 64a96 96 0 1 1 0 192 96 96 0 1 1 0-192zM133.3 352l53.3 0C260.3 352 320 411.7 320 485.3c0 14.7-11.9 26.7-26.7 26.7L26.7 512C11.9 512 0 500.1 0 485.3C0 411.7 59.7 352 133.3 352z"/>
                        </svg>
                        <span>COMERCIAL</span>
                    </a>
                </div>
                <div class="exit-button-col">
                    <a class="footer-button" href="{{ route('admin.incidencias.index') }}">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 384 512" width="44"
                        height="44" fill="#35a8e0"><!--!Font Awesome Free 6.6.0 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2024 Fonticons, Inc.--><path d="M112.5 301.4c0-73.8 105.1-122.5 105.1-203 0-47.1-34-88-39.1-90.4 .4 3.3 .6 6.7 .6 10C179.1 110.1 32 171.9 32 286.6c0 49.8 32.2 79.2 66.5 108.3 65.1 46.7 78.1 71.4 78.1 86.6 0 10.1-4.8 17-4.8 22.3 13.1-16.7 17.4-31.9 17.5-46.4 0-29.6-21.7-56.3-44.2-86.5-16-22.3-32.6-42.6-32.6-69.5zm205.3-39c-12.1-66.8-78-124.4-94.7-130.9l4 7.2c2.4 5.1 3.4 10.9 3.4 17.1 0 44.7-54.2 111.2-56.6 116.7-2.2 5.1-3.2 10.5-3.2 15.8 0 20.1 15.2 42.1 17.9 42.1 2.4 0 56.6-55.4 58.1-87.7 6.4 11.7 9.1 22.6 9.1 33.4 0 41.2-41.8 96.9-41.8 96.9 0 11.6 31.9 53.2 35.5 53.2 1 0 2.2-1.4 3.2-2.4 37.9-39.3 67.3-85 67.3-136.8 0-8-.7-16.2-2.2-24.6z"/></svg>
                        <span>INCIDENCIAS </span>
                    </a>
                </div>
                <div class="exit-button-col">
                    <a class="footer-button" href="{{ route('alertas.popup') }}">
                        <i class="fa-regular fa-bell" style="font-size: 44px;"></i>
                        <span>ALERTAS</span>
                    </a>
                </div>
                @if(Auth::user()->user_department_id == 2)
                <div class="exit-button-col">
                    <a class="footer-button" href="{{ route('almacen.index') }}">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-file-3d" width="44"
                            height="44" viewBox="0 0 24 24" stroke-width="1.5" stroke="#35a8e0" fill="none"
                            stroke-linecap="round" stroke-linejoin="round">
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
                    <a class="footer-button" href="{{ route('calendario.index') }}">
                        <i class="fa-regular fa-calendar" style="font-size: 44px"></i>
                        <span>EVENTOS</span>
                    </a>
                </div>
            @endif
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
                <form id="cPnEf0Yn21GWvOwPEAvTtEmZ1IuHPGSMwogz4WnK" action="{{ route('logout') }}" method="POST"
                    style="display: none;">
                    <input type="hidden" name="_token" value="{{ ['_token' => csrf_token()]['_token'] }}">
                </form>
            </div>
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
    @break

    @case(4)
        <footer >
            {{-- <div class="exit-button-col" style="text-align:right;">
                <button class="footer-button" onclick="location.href='{{ URL::previous() }}'"><svg
                        xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-arrow-back-up" width="44"
                        height="44" viewBox="0 0 24 24" stroke-width="1.5" stroke="#35a8e0" fill="none"
                        stroke-linecap="round" stroke-linejoin="round">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                        <path d="M9 13l-4 -4l4 -4m-4 4h11a4 4 0 0 1 0 8h-1" />
                    </svg>
                    <span>VOLVER</span>
                </button>
            </div> --}}
            <div class="menu_footer_new">
                <div class="exit-button-col">
                    <a class="footer-button" href="{{ route('almacen.index') }}">
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
                        <span>ALMACÉN</span>
                    </a>
                </div>
                <div class="exit-button-col">
                    <a class="footer-button" href="{{ route('stock.index') }}">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-circle-plus"
                            width="44" height="44" viewBox="0 0 24 24" stroke-width="1.5" stroke="#35a8e0"
                            fill="none" stroke-linecap="round" stroke-linejoin="round">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                            <path d="M3 12a9 9 0 1 0 18 0a9 9 0 0 0 -18 0"></path>
                            <path d="M9 12h6"></path>
                            <path d="M12 9v6"></path>
                        </svg>
                        <span>STOCK</span>
                    </a>

                </div>
                <div class="exit-button-col">
                    <a class="footer-button" href="{{ route('admin.incidencias.index') }}">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 384 512" width="44"
                        height="44" fill="#35a8e0"><!--!Font Awesome Free 6.6.0 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2024 Fonticons, Inc.--><path d="M112.5 301.4c0-73.8 105.1-122.5 105.1-203 0-47.1-34-88-39.1-90.4 .4 3.3 .6 6.7 .6 10C179.1 110.1 32 171.9 32 286.6c0 49.8 32.2 79.2 66.5 108.3 65.1 46.7 78.1 71.4 78.1 86.6 0 10.1-4.8 17-4.8 22.3 13.1-16.7 17.4-31.9 17.5-46.4 0-29.6-21.7-56.3-44.2-86.5-16-22.3-32.6-42.6-32.6-69.5zm205.3-39c-12.1-66.8-78-124.4-94.7-130.9l4 7.2c2.4 5.1 3.4 10.9 3.4 17.1 0 44.7-54.2 111.2-56.6 116.7-2.2 5.1-3.2 10.5-3.2 15.8 0 20.1 15.2 42.1 17.9 42.1 2.4 0 56.6-55.4 58.1-87.7 6.4 11.7 9.1 22.6 9.1 33.4 0 41.2-41.8 96.9-41.8 96.9 0 11.6 31.9 53.2 35.5 53.2 1 0 2.2-1.4 3.2-2.4 37.9-39.3 67.3-85 67.3-136.8 0-8-.7-16.2-2.2-24.6z"/></svg>
                        <span>INCIDENCIAS </span>
                    </a>
                </div>
                <div class="exit-button-col">
                    <a class="footer-button" href="{{ route('alertas.popup') }}">
                        <i class="fa-regular fa-bell" style="font-size: 44px;"></i>
                        <span>ALERTAS</span>
                    </a>
                </div>
              {{--  <div class="exit-button-col">
                    <a class="footer-button" href="{{ route('pedidos.index') }}">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-folder" width="44"
                            height="44" viewBox="0 0 24 24" stroke-width="1.5" stroke="#35a8e0" fill="none"
                            stroke-linecap="round" stroke-linejoin="round">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                            <path d="M5 4h4l3 3h7a2 2 0 0 1 2 2v8a2 2 0 0 1 -2 2h-14a2 2 0 0 1 -2 -2v-11a2 2 0 0 1 2 -2" />
                        </svg>
                        <span>PEDIDOS</span>
                    </a>
                </div>--}}
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
                    <form id="cPnEf0Yn21GWvOwPEAvTtEmZ1IuHPGSMwogz4WnK" action="{{ route('logout') }}" method="POST"
                        style="display: none;">
                        <input type="hidden" name="_token" value="{{ ['_token' => csrf_token()]['_token'] }}">
                    </form>
                </div>
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
    @break

    @case(5)
        <footer >
            {{-- <div class="exit-button-col" style="text-align:right;">
                <button class="footer-button" onclick="location.href='{{ URL::previous() }}'"><svg
                        xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-arrow-back-up" width="44"
                        height="44" viewBox="0 0 24 24" stroke-width="1.5" stroke="#35a8e0" fill="none"
                        stroke-linecap="round" stroke-linejoin="round">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                        <path d="M9 13l-4 -4l4 -4m-4 4h11a4 4 0 0 1 0 8h-1" />
                    </svg>
                    <span>VOLVER</span>
                </button>
            </div> --}}
            <div class="menu_footer_new">
                <div class="exit-button-col">
                    <a class="footer-button" href="{{ route('almacen.index') }}">
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
                        <span>ALMACÉN</span>
                    </a>
                </div>
                <div class="exit-button-col">
                    <a class="footer-button" href="{{ route('stock.index') }}">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-circle-plus"
                            width="44" height="44" viewBox="0 0 24 24" stroke-width="1.5" stroke="#35a8e0"
                            fill="none" stroke-linecap="round" stroke-linejoin="round">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                            <path d="M3 12a9 9 0 1 0 18 0a9 9 0 0 0 -18 0"></path>
                            <path d="M9 12h6"></path>
                            <path d="M12 9v6"></path>
                        </svg>
                        <span>STOCK</span>
                    </a>
                </div>
                <div class="exit-button-col">
                    <a class="footer-button" href="{{ route('produccion.index') }}">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-building-factory-2"
                        width="44" height="44" viewBox="0 0 24 24" stroke-width="1.5" stroke="#35a8e0"
                        fill="none" stroke-linecap="round" stroke-linejoin="round">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                        <path d="M3 21h18" />
                        <path d="M5 21v-12l5 4v-4l5 4h4" />
                        <path
                            d="M19 21v-8l-1.436 -9.574a.5 .5 0 0 0 -.495 -.426h-1.145a.5 .5 0 0 0 -.494 .418l-1.43 8.582" />
                        <path d="M9 17h1" />
                        <path d="M14 17h1" />
                        </svg>
                        <span>PRODUCCIÓN</span>
                    </a>
                </div>
                <div class="exit-button-col">
                    <a class="footer-button" href="{{ route('mercaderia.index') }}">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-sticker" width="44"
                        height="44" viewBox="0 0 24 24" stroke-width="1.5" stroke="#35a8e0" fill="none"
                        stroke-linecap="round" stroke-linejoin="round">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                        <path d="M20 12l-2 .5a6 6 0 0 1 -6.5 -6.5l.5 -2l8 8" />
                        <path d="M20 12a8 8 0 1 1 -8 -8" />
                    </svg>
                        <span>MATERIALES</span>
                    </a>
                </div>
                <div class="exit-button-col">
                    <a class="footer-button" href="{{ route('productos.index') }}">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-bottle-filled"
                            width="44" height="44" viewBox="0 0 24 24" stroke-width="1.5" stroke="#35a8e0" fill="none"
                            stroke-linecap="round" stroke-linejoin="round">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                            <path
                                d="M13 1a2 2 0 0 1 1.995 1.85l.005 .15v.5c0 1.317 .381 2.604 1.094 3.705l.17 .25l.05 .072a9.093 9.093 0 0 1 1.68 4.92l.006 .354v6.199a3 3 0 0 1 -2.824 2.995l-.176 .005h-6a3 3 0 0 1 -2.995 -2.824l-.005 -.176v-6.2a9.1 9.1 0 0 1 1.486 -4.982l.2 -.292l.05 -.069a6.823 6.823 0 0 0 1.264 -3.957v-.5a2 2 0 0 1 1.85 -1.995l.15 -.005h2zm.362 5h-2.724a8.827 8.827 0 0 1 -1.08 2.334l-.194 .284l-.05 .069a7.091 7.091 0 0 0 -1.307 3.798l-.003 .125a3.33 3.33 0 0 1 1.975 -.61a3.4 3.4 0 0 1 2.833 1.417c.27 .375 .706 .593 1.209 .583a1.4 1.4 0 0 0 1.166 -.583a3.4 3.4 0 0 1 .81 -.8l.003 .183c0 -1.37 -.396 -2.707 -1.137 -3.852l-.228 -.332a8.827 8.827 0 0 1 -1.273 -2.616z"
                                stroke-width="0" fill="currentColor" />
                        </svg>
                        <span>PRODUCTOS</span>
                    </a>
                </div>
                <div class="exit-button-col">
                    <a class="footer-button" href="{{ route('admin.incidencias.index') }}">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 384 512" width="44"
                        height="44" fill="#35a8e0"><!--!Font Awesome Free 6.6.0 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2024 Fonticons, Inc.--><path d="M112.5 301.4c0-73.8 105.1-122.5 105.1-203 0-47.1-34-88-39.1-90.4 .4 3.3 .6 6.7 .6 10C179.1 110.1 32 171.9 32 286.6c0 49.8 32.2 79.2 66.5 108.3 65.1 46.7 78.1 71.4 78.1 86.6 0 10.1-4.8 17-4.8 22.3 13.1-16.7 17.4-31.9 17.5-46.4 0-29.6-21.7-56.3-44.2-86.5-16-22.3-32.6-42.6-32.6-69.5zm205.3-39c-12.1-66.8-78-124.4-94.7-130.9l4 7.2c2.4 5.1 3.4 10.9 3.4 17.1 0 44.7-54.2 111.2-56.6 116.7-2.2 5.1-3.2 10.5-3.2 15.8 0 20.1 15.2 42.1 17.9 42.1 2.4 0 56.6-55.4 58.1-87.7 6.4 11.7 9.1 22.6 9.1 33.4 0 41.2-41.8 96.9-41.8 96.9 0 11.6 31.9 53.2 35.5 53.2 1 0 2.2-1.4 3.2-2.4 37.9-39.3 67.3-85 67.3-136.8 0-8-.7-16.2-2.2-24.6z"/></svg>
                        <span>INCIDENCIAS </span>
                    </a>
                </div>
                <div class="exit-button-col">
                    <a class="footer-button" href="{{ route('alertas.popup') }}">
                        <i class="fa-regular fa-bell" style="font-size: 44px;"></i>
                        <span>ALERTAS</span>
                    </a>
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
                    <form id="cPnEf0Yn21GWvOwPEAvTtEmZ1IuHPGSMwogz4WnK" action="{{ route('logout') }}" method="POST"
                        style="display: none;">
                        <input type="hidden" name="_token" value="{{ ['_token' => csrf_token()]['_token'] }}">
                    </form>
                </div>
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
    @break

    @case(6)
        <footer >
            {{-- <div class="exit-button-col" style="text-align:right;">
                <button class="footer-button" onclick="location.href='{{ URL::previous() }}'"><svg
                        xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-arrow-back-up" width="44"
                        height="44" viewBox="0 0 24 24" stroke-width="1.5" stroke="#35a8e0" fill="none"
                        stroke-linecap="round" stroke-linejoin="round">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                        <path d="M9 13l-4 -4l4 -4m-4 4h11a4 4 0 0 1 0 8h-1" />
                    </svg>
                    <span>VOLVER</span>
                </button>
            </div> --}}
            
            <div class="menu_footer_new">
                <div class="exit-button-col">
                    <a class="footer-button" href="{{ route('productos.index') }}">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-bottle-filled"
                            width="44" height="44" viewBox="0 0 24 24" stroke-width="1.5" stroke="#35a8e0" fill="none"
                            stroke-linecap="round" stroke-linejoin="round">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                            <path
                                d="M13 1a2 2 0 0 1 1.995 1.85l.005 .15v.5c0 1.317 .381 2.604 1.094 3.705l.17 .25l.05 .072a9.093 9.093 0 0 1 1.68 4.92l.006 .354v6.199a3 3 0 0 1 -2.824 2.995l-.176 .005h-6a3 3 0 0 1 -2.995 -2.824l-.005 -.176v-6.2a9.1 9.1 0 0 1 1.486 -4.982l.2 -.292l.05 -.069a6.823 6.823 0 0 0 1.264 -3.957v-.5a2 2 0 0 1 1.85 -1.995l.15 -.005h2zm.362 5h-2.724a8.827 8.827 0 0 1 -1.08 2.334l-.194 .284l-.05 .069a7.091 7.091 0 0 0 -1.307 3.798l-.003 .125a3.33 3.33 0 0 1 1.975 -.61a3.4 3.4 0 0 1 2.833 1.417c.27 .375 .706 .593 1.209 .583a1.4 1.4 0 0 0 1.166 -.583a3.4 3.4 0 0 1 .81 -.8l.003 .183c0 -1.37 -.396 -2.707 -1.137 -3.852l-.228 -.332a8.827 8.827 0 0 1 -1.273 -2.616z"
                                stroke-width="0" fill="currentColor" />
                        </svg>
                        <span>PRODUCTOS</span>
                    </a>
                </div>
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
                <div class="exit-button-col">
                    <a class="footer-button" href="{{ route('pedidos.index') }}">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-folder" width="44"
                            height="44" viewBox="0 0 24 24" stroke-width="1.5" stroke="#35a8e0" fill="none"
                            stroke-linecap="round" stroke-linejoin="round">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                            <path d="M5 4h4l3 3h7a2 2 0 0 1 2 2v8a2 2 0 0 1 -2 2h-14a2 2 0 0 1 -2 -2v-11a2 2 0 0 1 2 -2" />
                        </svg>
                        <span>PEDIDOS PRODUCTOS</span>
                    </a>
                </div>
                <div class="exit-button-col">
                    <a class="footer-button" href="{{ route('facturas.index') }}">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-file-invoice" width="44"
                            height="44" viewBox="0 0 24 24" stroke-width="1.5" stroke="#35a8e0" fill="none"
                            stroke-linecap="round" stroke-linejoin="round">
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
                <div class="exit-button-col">
                    <a class="footer-button" href="{{ route('almacen.index') }}">
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
                        <span>ALMACÉN</span>
                    </a>
                </div>
                <div class="exit-button-col">
                    <a class="footer-button" href="{{ route('caja.index') }}">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-safe" width="44" height="44" viewBox="0 0 24 24" stroke-width="1.5" stroke="#35a8e0" fill="none" stroke-linecap="round" stroke-linejoin="round">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                            <rect x="3" y="7" width="18" height="14" rx="2" />
                            <path d="M7 7v-1a2 2 0 0 1 2 -2h6a2 2 0 0 1 2 2v1" />
                            <path d="M12 10v4" />
                            <circle cx="12" cy="14" r="2" />
                            <path d="M10 14h4" />
                          </svg>
                        <span>CAJA</span>
                    </a>
                </div>
                <div class="exit-button-col">
                    <a class="footer-button" href="{{ route('proveedores.index') }}">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-users" width="44"
                            height="44" viewBox="0 0 24 24" stroke-width="1.5" stroke="#35a8e0" fill="none"
                            stroke-linecap="round" stroke-linejoin="round">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                            <circle cx="9" cy="7" r="4" />
                            <path d="M3 21v-2a4 4 0 0 1 4 -4h4a4 4 0 0 1 4 4v2" />
                            <path d="M16 3.13a4 4 0 0 1 0 7.75" />
                            <path d="M21 21v-2a4 4 0 0 0 -3 -3.85" />
                        </svg>
                        <span>PROVEEDORES</span>
                    </a>
                </div>
                <div class="exit-button-col">
                    <a class="footer-button" href="{{ route('admin.incidencias.index') }}">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 384 512" width="44"
                        height="44" fill="#35a8e0"><!--!Font Awesome Free 6.6.0 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2024 Fonticons, Inc.--><path d="M112.5 301.4c0-73.8 105.1-122.5 105.1-203 0-47.1-34-88-39.1-90.4 .4 3.3 .6 6.7 .6 10C179.1 110.1 32 171.9 32 286.6c0 49.8 32.2 79.2 66.5 108.3 65.1 46.7 78.1 71.4 78.1 86.6 0 10.1-4.8 17-4.8 22.3 13.1-16.7 17.4-31.9 17.5-46.4 0-29.6-21.7-56.3-44.2-86.5-16-22.3-32.6-42.6-32.6-69.5zm205.3-39c-12.1-66.8-78-124.4-94.7-130.9l4 7.2c2.4 5.1 3.4 10.9 3.4 17.1 0 44.7-54.2 111.2-56.6 116.7-2.2 5.1-3.2 10.5-3.2 15.8 0 20.1 15.2 42.1 17.9 42.1 2.4 0 56.6-55.4 58.1-87.7 6.4 11.7 9.1 22.6 9.1 33.4 0 41.2-41.8 96.9-41.8 96.9 0 11.6 31.9 53.2 35.5 53.2 1 0 2.2-1.4 3.2-2.4 37.9-39.3 67.3-85 67.3-136.8 0-8-.7-16.2-2.2-24.6z"/></svg>
                        <span>INCIDENCIAS </span>
                    </a>
                </div>
                <div class="exit-button-col">
                    <a class="footer-button" href="{{ route('alertas.popup') }}">
                        <i class="fa-regular fa-bell" style="font-size: 44px;"></i>
                        <span>ALERTAS</span>
                    </a>
                </div>
                <div class="exit-button-col">
                    <a class="footer-button" href="{{ route('contabilidad.index') }}">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 576 512" width="44"
                        height="44" stroke-width="40" stroke="#35a8e0" fill="none">
                            <path d="M249.6 471.5c10.8 3.8 22.4-4.1 22.4-15.5l0-377.4c0-4.2-1.6-8.4-5-11C247.4 52 202.4 32 144 32C93.5 32 46.3 45.3 18.1 56.1C6.8 60.5 0 71.7 0 83.8L0 454.1c0 11.9 12.8 20.2 24.1 16.5C55.6 460.1 105.5 448 144 448c33.9 0 79 14 105.6 23.5zm76.8 0C353 462 398.1 448 432 448c38.5 0 88.4 12.1 119.9 22.6c11.3 3.8 24.1-4.6 24.1-16.5l0-370.3c0-12.1-6.8-23.3-18.1-27.6C529.7 45.3 482.5 32 432 32c-58.4 0-103.4 20-123 35.6c-3.3 2.6-5 6.8-5 11L304 456c0 11.4 11.7 19.3 22.4 15.5z"/></svg>
                         <span>CONTABILIDAD</span>
                    </a>
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
                    <form id="cPnEf0Yn21GWvOwPEAvTtEmZ1IuHPGSMwogz4WnK" action="{{ route('logout') }}" method="POST"
                        style="display: none;">
                        <input type="hidden" name="_token" value="{{ ['_token' => csrf_token()]['_token'] }}">
                    </form>
                </div>
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
    @break

    @case(7)
    <footer >
        {{-- <div class="exit-button-col" style="text-align:right;">
            <button class="footer-button" onclick="location.href='{{ URL::previous() }}'"><svg
                    xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-arrow-back-up" width="44"
                    height="44" viewBox="0 0 24 24" stroke-width="1.5" stroke="#35a8e0" fill="none"
                    stroke-linecap="round" stroke-linejoin="round">
                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                    <path d="M9 13l-4 -4l4 -4m-4 4h11a4 4 0 0 1 0 8h-1" />
                </svg>
                <span>VOLVER</span>
            </button>
        </div> --}}
        <div class="menu_footer_new">
            <div class="exit-button-col">
                <a class="footer-button" href="{{ route('productos.index') }}">
                    <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-bottle-filled"
                        width="44" height="44" viewBox="0 0 24 24" stroke-width="1.5" stroke="#35a8e0" fill="none"
                        stroke-linecap="round" stroke-linejoin="round">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                        <path
                            d="M13 1a2 2 0 0 1 1.995 1.85l.005 .15v.5c0 1.317 .381 2.604 1.094 3.705l.17 .25l.05 .072a9.093 9.093 0 0 1 1.68 4.92l.006 .354v6.199a3 3 0 0 1 -2.824 2.995l-.176 .005h-6a3 3 0 0 1 -2.995 -2.824l-.005 -.176v-6.2a9.1 9.1 0 0 1 1.486 -4.982l.2 -.292l.05 -.069a6.823 6.823 0 0 0 1.264 -3.957v-.5a2 2 0 0 1 1.85 -1.995l.15 -.005h2zm.362 5h-2.724a8.827 8.827 0 0 1 -1.08 2.334l-.194 .284l-.05 .069a7.091 7.091 0 0 0 -1.307 3.798l-.003 .125a3.33 3.33 0 0 1 1.975 -.61a3.4 3.4 0 0 1 2.833 1.417c.27 .375 .706 .593 1.209 .583a1.4 1.4 0 0 0 1.166 -.583a3.4 3.4 0 0 1 .81 -.8l.003 .183c0 -1.37 -.396 -2.707 -1.137 -3.852l-.228 -.332a8.827 8.827 0 0 1 -1.273 -2.616z"
                            stroke-width="0" fill="currentColor" />
                    </svg>
                    <span>PRODUCTOS</span>
                </a>
            </div>
            <div class="exit-button-col">
                <a class="footer-button" href="{{ route('pedidos.create') }}">
                    <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-circle-plus" width="44"
                        height="44" viewBox="0 0 24 24" stroke-width="1.5" stroke="#35a8e0" fill="none"
                        stroke-linecap="round" stroke-linejoin="round">
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
            <div class="exit-button-col">
                <a class="footer-button" href="{{ route('facturas.index') }}">
                    <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-file-invoice" width="44"
                        height="44" viewBox="0 0 24 24" stroke-width="1.5" stroke="#35a8e0" fill="none"
                        stroke-linecap="round" stroke-linejoin="round">
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
            <div class="exit-button-col">
                <a class="footer-button" href="{{ route('almacen.index') }}">
                    <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-file-3d" width="44"
                        height="44" viewBox="0 0 24 24" stroke-width="1.5" stroke="#35a8e0" fill="none"
                        stroke-linecap="round" stroke-linejoin="round">
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
                    <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-packages" width="44"
                        height="44" viewBox="0 0 24 24" stroke-width="1.5" stroke="#35a8e0" fill="none"
                        stroke-linecap="round" stroke-linejoin="round">
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
            <div class="exit-button-col">
                <a class="footer-button" href="{{ route('mercaderia.index') }}">
                    <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-sticker" width="44"
                        height="44" viewBox="0 0 24 24" stroke-width="1.5" stroke="#35a8e0" fill="none"
                        stroke-linecap="round" stroke-linejoin="round">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                        <path d="M20 12l-2 .5a6 6 0 0 1 -6.5 -6.5l.5 -2l8 8" />
                        <path d="M20 12a8 8 0 1 1 -8 -8" />
                    </svg>
                    <span>MATERIALES</span>
                </a>
            </div>
            <div class="exit-button-col">
                <a class="footer-button" href="{{ route('produccion.index') }}">
                    <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-building-factory-2"
                        width="44" height="44" viewBox="0 0 24 24" stroke-width="1.5" stroke="#35a8e0"
                        fill="none" stroke-linecap="round" stroke-linejoin="round">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                        <path d="M3 21h18" />
                        <path d="M5 21v-12l5 4v-4l5 4h4" />
                        <path d="M19 21v-8l-1.436 -9.574a.5 .5 0 0 0 -.495 -.426h-1.145a.5 .5 0 0 0 -.494 .418l-1.43 8.582" />
                        <path d="M9 17h1" />
                        <path d="M14 17h1" />
                    </svg>
                    <span>PRODUCCIÓN</span>
                </a>
            </div>
            <div class="exit-button-col">
                <a class="footer-button" href="{{ route('alertas.popup') }}">
                    <i class="fa-regular fa-bell" style="font-size: 44px;"></i>
                    <span>ALERTAS</span>
                </a>
            </div>
            <div class="exit-button-col">
                <a class="footer-button" href="{{ route('admin.incidencias.index') }}">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 384 512" width="44"
                    height="44" fill="#35a8e0"><!--!Font Awesome Free 6.6.0 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2024 Fonticons, Inc.--><path d="M112.5 301.4c0-73.8 105.1-122.5 105.1-203 0-47.1-34-88-39.1-90.4 .4 3.3 .6 6.7 .6 10C179.1 110.1 32 171.9 32 286.6c0 49.8 32.2 79.2 66.5 108.3 65.1 46.7 78.1 71.4 78.1 86.6 0 10.1-4.8 17-4.8 22.3 13.1-16.7 17.4-31.9 17.5-46.4 0-29.6-21.7-56.3-44.2-86.5-16-22.3-32.6-42.6-32.6-69.5zm205.3-39c-12.1-66.8-78-124.4-94.7-130.9l4 7.2c2.4 5.1 3.4 10.9 3.4 17.1 0 44.7-54.2 111.2-56.6 116.7-2.2 5.1-3.2 10.5-3.2 15.8 0 20.1 15.2 42.1 17.9 42.1 2.4 0 56.6-55.4 58.1-87.7 6.4 11.7 9.1 22.6 9.1 33.4 0 41.2-41.8 96.9-41.8 96.9 0 11.6 31.9 53.2 35.5 53.2 1 0 2.2-1.4 3.2-2.4 37.9-39.3 67.3-85 67.3-136.8 0-8-.7-16.2-2.2-24.6z"/></svg>
                    <span>INCIDENCIAS </span>
                </a>
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
                <form id="cPnEf0Yn21GWvOwPEAvTtEmZ1IuHPGSMwogz4WnK" action="{{ route('logout') }}" method="POST"
                    style="display: none;">
                    <input type="hidden" name="_token" value="{{ ['_token' => csrf_token()]['_token'] }}">
                </form>
            </div>
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
    @break

    @default

@endswitch
