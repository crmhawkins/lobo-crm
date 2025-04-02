<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>LOBO - @yield('title') </title>
    <style>
        .precioProductoClientes{
            display:grid;
            grid-template-columns: repeat(5, 1fr) !important;
        }
        @media(max-width: 768px) {
           .content-page .content{
                padding: 0px !important;
           }
            .precioProductoClientes{
                display:grid;
                grid-template-columns: repeat(2, 1fr) !important;
            }
            .tipoCliente{
                padding-left: 30px;

            }
            .invisible{
                display: none;
            }
        }
    </style>
    <link rel="stylesheet" href="https://uicdn.toast.com/calendar/latest/toastui-calendar.min.css">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.9.1/font/bootstrap-icons.css"
        integrity="sha512-CaTMQoJ49k4vw9XO0VpTBpmMz8XpCWP5JhGmBvuBqCOaOHWENWO1CrVl09u4yp8yBVSID6smD4+gpzDJVQOPwQ=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link href="https://fonts.bunny.net/css?family=Nunito" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css"
        integrity="sha512-xh6O/CkQoPOWDdYTDqeRdPCVd1SpvCA9XXcUnZS2FmJNp1coAFzvtCN9BmamE+4aHK8yyUHUSCcJHgXloTyT2A=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.2/dist/css/bootstrap.min.css"
        integrity="sha384-Zenh87qX5JnK2Jl0vWa8Ck2rdkQ2Bzep5IDxbcnCeuOxjzrPF/et3URy9Bv1WTRi" crossorigin="anonymous">
    <link href='https://unpkg.com/boxicons@2.0.7/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="//cdn.datatables.net/1.13.2/css/jquery.dataTables.min.css">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    @livewireStyles
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    {{-- <script src="//unpkg.com/alpinejs" defer></script> --}}
    <script src="https://cdnjs.cloudflare.com/ajax/libs/alpinejs/2.3.0/alpine.js" defer></script>
    <link rel="stylesheet" href="//code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css">

    <x-livewire-alert::scripts />
    <!-- Scripts -->
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])
    <!--Morris Chart CSS -->
    <link rel="stylesheet" href="/plugins/morris/morris.css">

    <link href="/assets/css/bootstrap.min.css" rel="stylesheet" type="text/css">
    <link href="/assets/css/metismenu.min.css" rel="stylesheet" type="text/css">
    <link href="/assets/css/icons.css" rel="stylesheet" type="text/css">
    <link href="/assets/css/style.css" rel="stylesheet" type="text/css">
    {{-- <link rel="stylesheet" href="../css/metismenu.min.css"> --}}
    @yield('head')


</head>

<body>
    @php
        $user = Auth::user();
        $user_rol = $user->role;
    @endphp
    <div id="app">
        <div id="loadingOverlay" style="display: block; position: fixed; width: 100%; height: 100%; top: 0; left: 0; right: 0; bottom: 0; background-color: rgba(255,255,255,0.5); z-index: 50000; cursor: pointer;">
            <div style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%);">
                <div class="spinner-border text-black" role="status">
                    <span class="sr-only">Cargando...</span>
                </div>
            </div>
        </div>
        <style>
           #footerSidebar {
                position: fixed;
                left: -250px; /* Inicia oculto */
                top: 0;
                bottom: 0;
                width: 250px;
                background-color: #f8f9fa;
                overflow-y: auto; /* Permite el scroll en el sidebar */
                transition: left 0.3s;
            }

            #toggleFooterSidebar {
                position: absolute;
                top: 10px;
                left: 0px;
                z-index: 1501;
                cursor: pointer;
                background: none;
                border: none;
                color: #007bff;
                padding: 10px;
                font-size: 30px;
            }

            .page-wrapper {
                transition: margin-left 0.3s; /* Suaviza la transición */
            }

            /* Asegurarse de que el page-wrapper se mueva con el sidebar */
            .page-wrapper.active {
                margin-left: 250px;
            }



            /* Media Query para dispositivos móviles */
            @media (max-width: 768px) {
                #footerSidebar {
                    width: 100%; /* Ocupa toda la anchura en móviles */
                }
            }
        </style>

        <button id="toggleFooterSidebar" style=" z-index: 1501; cursor: pointer; background: none; border: none; color: white;">
            <i class="fas fa-bars" id="openIcon"></i> <!-- Icono visible cuando el sidebar está cerrado -->
            <i class="fas fa-arrow-left" id="closeIcon" style="display: none;"></i> <!-- Icono visible cuando el sidebar está abierto -->
        </button>
        <div id="footerSidebar" class="footer-sidebar" style="position: fixed; left: -250px; top: 0; bottom: 0; width: 250px; background-color: #f8f9fa; transition: left 0.3s;">
            @include('layouts.footer') <!-- Tu footer actual -->
        </div>

        <div class="page-wrapper chiller-theme toggled" id="wrapper">

            {{-- @include('layouts.sidebar') --}}
            @include('layouts.header')
            <div class="content-page">
                <div class="content">
                    {{-- @livewire('container-component') --}}
                    @yield('content-principal')

                    @yield('content-factura')
                    {{-- @yield('content') --}}

                </div>

            </div>
            @mobile
            {{-- @include('layouts.footerMobile') --}}
            @elsemobile
            {{-- @include('layouts.footer') --}}
            @endmobile
            {{-- <div class="row w-100 m-0">
                <div class="col-md-2 p-0 contenedor-sidebar">

                </div>
                <div class="col-md-10 p-0 contenedor-main">
                    <div class="">

                    </div>
                </div>
            </div> --}}


        </div>

    </div>
    {{-- <script src="/assets/js/jquery.min.js"></script> --}}
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            var sidebar = document.getElementById('footerSidebar');
            var wrapper = document.querySelector('.page-wrapper');
            var openIcon = document.getElementById('openIcon');
            var closeIcon = document.getElementById('closeIcon');
            var button = document.getElementById('toggleFooterSidebar'); // Referencia al botón

            if (button) {
                button.addEventListener('click', function() {
                    if (sidebar.style.left === '0px') {
                        sidebar.style.left = '-250px'; // Oculta el sidebar
                        wrapper.classList.remove('active'); // Retira el desplazamiento del contenido
                        openIcon.style.display = 'block'; // Muestra el icono de barras
                        closeIcon.style.display = 'none'; // Oculta el icono de flecha
                        button.style.color = 'white'; // Cambia el color del botón a blanco
                        button.style.left = '10px'; // Posición del botón
                        button.style.top = '10px';
                        button.style.fontSize = '30px'; // Tamaño del botón
                        button.style.position = 'absolute'; // Posición del botón
                    } else {
                        sidebar.style.left = '0px'; // Muestra el sidebar
                        wrapper.classList.add('active'); // Añade desplazamiento al contenido
                        openIcon.style.display = 'none'; // Oculta el icono de barras
                        closeIcon.style.display = 'block'; // Muestra el icono de flecha
                        button.style.color = 'red'; // Cambia el color del botón a rojo
                        button.style.left = 'calc(0%)'; // Posición del botón
                        button.style.fontSize = '20px'; // Tamaño del botón
                        button.style.position = 'fixed'; // Posición del botón
                    }
                });
            }
        });
        </script>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"
        integrity="sha384-oBqDVmMz9ATKxIep9tiCxS/Z9fNfEXiDAYTujMAeBAsjFuCZSmKbSSUnQlmh/jp3" crossorigin="anonymous">
    </script>
    <script src="https://code.jquery.com/ui/1.13.2/jquery-ui.js"></script>
    {{-- <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.2/dist/js/bootstrap.min.js"
        integrity="sha384-IDwe1+LCz02ROU9k972gdyvl+AESN10+x7tBKgc9I5HFtuNz0wWnPclzo6p9vxnk" crossorigin="anonymous">
    </script>
    <script src="https://cdn.datatables.net/1.13.2/js/jquery.dataTables.min.js"></script> --}}
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <script src="/assets/js/bootstrap.bundle.min.js"></script>
    <script src="/assets/js/metismenu.min.js"></script>
    <script src="/assets/js/jquery.slimscroll.js"></script>
    <script src="/assets/js/waves.min.js"></script>

    <!--Morris Chart-->
    {{-- <script src="../plugins/morris/morris.min.js"></script> --}}
    <script src="/plugins/raphael/raphael.min.js"></script>

    {{-- <script src="../assets/pages/dashboard.init.js"></script> --}}
    <script src="https://uicdn.toast.com/calendar/latest/toastui-calendar.min.js"></script>

    <!-- App js -->
    <script src="/assets/js/app.js"></script>
    <script>

        document.addEventListener("DOMContentLoaded", function() {
                    var loader = document.getElementById('loadingOverlay');
                    if (loader) {
                        loader.style.display = 'none';
                        setTimeout(() => {
                        }, 1500);
                    }
                });




    </script>

    @livewireScripts
    @yield('scripts')
    @if (session('alerta'))
    <script>
        Swal.fire({
            title: '{{ session('alerta')->titulo }}',
            html: '<p>{{ session('alerta')->descripcion }}</p>' +
                @if(session('alerta')->imagen)
                '<img src="{{ asset("storage/" . session('alerta')->imagen) }}" alt="Imagen Alerta" style="width: 100%; height: auto;">' +
                @endif
                '',
            showCancelButton: true, // Para mostrar los dos botones
            confirmButtonText: 'Marcar esta alerta como leída',
            cancelButtonText: 'Marcar todas como leídas', // Texto del botón adicional
        }).then((result) => {
            if (result.isConfirmed) {
                // Realiza una llamada AJAX para marcar la alerta específica como leída
                $.ajax({
                    url: "{{ route('alertas.marcarLeida', session('alerta')->id) }}",
                    method: "POST",
                    data: {
                        _token: "{{ csrf_token() }}",
                    },
                    success: function() {
                        console.log('Alerta marcada como leída');
                        // Aquí podrías eliminar el 'alerta_mostrada' de la sesión
                        sessionStorage.removeItem('alerta_mostrada');
                    },
                    error: function() {
                        console.error('Error al marcar la alerta como leída');
                    }
                });
            } else if (result.dismiss === Swal.DismissReason.cancel) {
                // Realiza una llamada AJAX para marcar todas las alertas como leídas
                $.ajax({
                    url: "{{ route('alertas.marcarTodasLeidas') }}",
                    method: "POST",
                    data: {
                        _token: "{{ csrf_token() }}",
                    },
                    success: function() {
                        console.log('Todas las alertas marcadas como leídas');
                        // Aquí podrías eliminar el 'alerta_mostrada' de la sesión
                        sessionStorage.removeItem('alerta_mostrada');
                    },
                    error: function() {
                        console.error('Error al marcar todas las alertas como leídas');
                    }
                });
            }
        });
    </script>
@endif


</body>

</html>
