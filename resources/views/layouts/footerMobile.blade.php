<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Footer con Menú Burger</title>
    <style>
        body, html {
            margin: 0;
            padding: 0;
            height: 100%;
        }

        footer {
            display: flex;
            justify-content: space-between;
            padding: 10px 20px;
            background-color: #fff;
            color: #35a8e0;
            z-index: 1000;
            box-shadow: 0 -2px 5px rgba(0,0,0,0.1);
        }

        button {
            background-color: #fff;
            color: #35a8e0;
            border: none;
            padding: 10px 20px;
            cursor: pointer;
            transition: background-color 0.3s;
            font-size: 16px;
        }

        button:hover {
            background-color: #777;
        }

        #fullScreenMenu {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            overflow-y: auto;
            background-color: rgba(255, 255, 255, 1);
            color: #35a8e0;
            z-index: 1000;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: flex-start;
            transform: translateY(100%);
            transition: transform 0.4s ease-in-out;
            padding-top: 60px; /* Añade espacio en la parte superior para evitar solapamientos */
        }

        #fullScreenMenu a {
            color: #35a8e0;
            text-decoration: none;
            padding: 15px;
            font-size: 20px;
        }

        #closeMenuBtn {
            position: fixed; /* Cambiado a fixed para asegurar su posición relativa a la ventana */
            top: 10px;
            right: 10px;
            font-size: 30px;
            padding: 10px;
            background-color: transparent;
            border: none;
            cursor: pointer;
            z-index: 1100;
        }

        #fullScreenMenu.menu-shown {
            transform: translateY(0);
        }
    </style>
</head>

<body>

    <footer>
        <button id="backBtn"><svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-arrow-back-up"
                width="44" height="44" viewBox="0 0 24 24" stroke-width="1.5" stroke="#35a8e0" fill="none"
                stroke-linecap="round" stroke-linejoin="round">
                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                <path d="M9 13l-4 -4l4 -4m-4 4h11a4 4 0 0 1 0 8h-1" />
            </svg><br>
            <span>VOLVER</span></button>
        <button id="menuBtn"><svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-menu-2"
                width="44" height="44" viewBox="0 0 24 24" stroke-width="1.5" stroke="#35a8e0" fill="none"
                stroke-linecap="round" stroke-linejoin="round">
                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                <path d="M4 6l16 0" />
                <path d="M4 12l16 0" />stock
                <path d="M4 18l16 0" />
            </svg><br><span>MENÚ</span></button>
    </footer>

    <div id="fullScreenMenu" class="menu-hidden">
        <button id="closeMenuBtn">&times;</button>
        <nav>
            <div class="row">
                @if ($user_rol != 4 && $user_rol != 3 )
                    <div class="col-6"><a class="footer-button" href="{{ route('productos.index') }}">
                            <svg xmlns="http://www.w3.org/2000/svg"
                                class="icon icon-tabler icon-tabler-brand-codesandbox" width="44" height="44"
                                viewBox="0 0 24 24" stroke-width="1.5" stroke="#35a8e0" fill="none"
                                stroke-linecap="round" stroke-linejoin="round">
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
                        </a></div>
                @endif
                @if ($user_rol != 4 && $user_rol != 5 && $user_rol != 6)
                    <div class="col-6"><a class="footer-button" href="{{ route('pedidos.create') }}">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-circle-plus"
                                width="44" height="44" viewBox="0 0 24 24" stroke-width="1.5" stroke="#35a8e0"
                                fill="none" stroke-linecap="round" stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                <path d="M3 12a9 9 0 1 0 18 0a9 9 0 0 0 -18 0"></path>
                                <path d="M9 12h6"></path>
                                <path d="M12 9v6"></path>
                            </svg>
                            <span>NUEVO PEDIDO</span>
                        </a></div>
                @endif
                @if ($user_rol != 4 && $user_rol != 5)
                    <div class="col-6"><a class="footer-button" href="{{ route('pedidos.index') }}">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-folder"
                                width="44" height="44" viewBox="0 0 24 24" stroke-width="1.5" stroke="#35a8e0"
                                fill="none" stroke-linecap="round" stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                <path
                                    d="M5 4h4l3 3h7a2 2 0 0 1 2 2v8a2 2 0 0 1 -2 2h-14a2 2 0 0 1 -2 -2v-11a2 2 0 0 1 2 -2" />
                            </svg>
                            <span>PEDIDOS</span>
                        </a></div>

                    <div class="col-6"><a class="footer-button" href="{{ route('clientes.index') }}">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-users"
                            width="44" height="44" viewBox="0 0 24 24" stroke-width="1.5" stroke="#35a8e0"
                            fill="none" stroke-linecap="round" stroke-linejoin="round">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                            <circle cx="9" cy="7" r="4" />
                            <path d="M3 21v-2a4 4 0 0 1 4 -4h4a4 4 0 0 1 4 4v2" />
                            <path d="M16 3.13a4 4 0 0 1 0 7.75" />
                            <path d="M21 21v-2a4 4 0 0 0 -3 -3.85" />
                        </svg>
                        <span>CLIENTES</span>
                    </a></div>
                @endif
                @if ($user_rol == 1 || $user_rol == 6 || $user_rol == 7)
                    <div class="col-6">
                        <a class="footer-button" href="{{ route('facturas.index') }}">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-file-invoice"
                                width="44" height="44" viewBox="0 0 24 24" stroke-width="1.5"
                                stroke="#35a8e0" fill="none" stroke-linecap="round" stroke-linejoin="round">
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
                @if ($user_rol == 1 || $user_rol == 6)
                    <div class="col-6">
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
                    <div class="col-6">
                        <a class="footer-button" href="{{ route('caja.index') }}">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-safe" width="44"
                                height="44" viewBox="0 0 24 24" stroke-width="1.5" stroke="#35a8e0" fill="none"
                                stroke-linecap="round" stroke-linejoin="round">
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
                @endif
                @if ($user_rol == 1 || $user_rol == 4 || $user_rol == 5|| $user_rol == 7 || $user_rol == 2)
                  @if($user_rol !=2)
                    <div class="col-6"><a class="footer-button" href="{{ route('almacen.index') }}">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-file-3d"
                                width="44" height="44" viewBox="0 0 24 24" stroke-width="1.5"
                                stroke="#35a8e0" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                <path d="M14 3v4a1 1 0 0 0 1 1h4"></path>
                                <path d="M17 21h-10a2 2 0 0 1 -2 -2v-14a2 2 0 0 1 2 -2h7l5 5v11a2 2 0 0 1 -2 2z"></path>
                                <path d="M12 13.5l4 -1.5"></path>
                                <path d="M8 11.846l4 1.654v4.5l4 -1.846v-4.308l-4 -1.846z"></path>
                                <path d="M8 12v4.2l4 1.8"></path>
                            </svg>
                            <span>ALMACÉN</span>
                        </a></div>
                  @endif
                    <div class="col-6"><a class="footer-button" href="{{ route('stock.index') }}">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-packages"
                                width="44" height="44" viewBox="0 0 24 24" stroke-width="1.5"
                                stroke="#35a8e0" fill="none" stroke-linecap="round" stroke-linejoin="round">
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
                        </a></div>
                @endif
                @if ($user_rol == 1 || $user_rol == 5 || $user_rol == 7)
                    <div class="col-6">
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
                    <div class="col-6">
                        <a class="footer-button" href="{{ route('usuarios.index') }}">
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
                @endif
                @if ($user_rol == 1)
                    <div class="col-6">
                        <a class="footer-button" href="{{ route('usuarios.index') }}">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-user-cog"
                                width="44" height="44" viewBox="0 0 24 24" stroke-width="1.5"
                                stroke="#35a8e0" fill="none" stroke-linecap="round" stroke-linejoin="round">
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
                <div @if ($user_rol == 1) class="col-12" @else class="col-6" @endif> <a class="footer-button" href="{{ route('logout') }}"
                        onclick="event.preventDefault();document.getElementById('cPnEf0Yn21GWvOwPEAvTtEmZ1IuHPGSMwogz4WnK').submit();">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-logout"
                            width="44" height="44" viewBox="0 0 24 24" stroke-width="1.5" stroke="#35a8e0"
                            fill="none" stroke-linecap="round" stroke-linejoin="round">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                            <path d="M14 8v-2a2 2 0 0 0 -2 -2h-7a2 2 0 0 0 -2 2v12a2 2 0 0 0 2 2h7a2 2 0 0 0 2 -2v-2" />
                            <path d="M7 12h14l-3 -3m0 6l3 -3" />
                        </svg><span>SALIR</span>
                    </a>
                    <form id="cPnEf0Yn21GWvOwPEAvTtEmZ1IuHPGSMwogz4WnK" action="{{ route('logout') }}" method="POST"
                        style="display: none;">
                        <input type="hidden" name="_token" value="{{ ['_token' => csrf_token()]['_token'] }}">
                    </form>
                </div>

        </nav>
    </div>

    <script>
        document.getElementById("menuBtn").addEventListener("click", function() {
            document.getElementById("fullScreenMenu").classList.add("menu-shown");
        });

        document.getElementById("backBtn").addEventListener("click", function() {
            window.history.back();
        });

        document.getElementById("closeMenuBtn").addEventListener("click", function() {
            document.getElementById("fullScreenMenu").classList.remove("menu-shown");
        });
    </script>
</body>

</html>
