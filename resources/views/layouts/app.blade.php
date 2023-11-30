<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>LOBO - @yield('title') </title>

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
    <script src="//unpkg.com/alpinejs" defer></script>
    <link rel="stylesheet" href="//code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css">

    <x-livewire-alert::scripts />
    <!-- Scripts -->
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])
    <!--Morris Chart CSS -->
    <link rel="stylesheet" href="http://lobo-crm.test/plugins/morris/morris.css">

    <link href="http://lobo-crm.test/assets/css/bootstrap.min.css" rel="stylesheet" type="text/css">
    <link href="http://lobo-crm.test/assets/css/metismenu.min.css" rel="stylesheet" type="text/css">
    <link href="http://lobo-crm.test/assets/css/icons.css" rel="stylesheet" type="text/css">
    <link href="http://lobo-crm.test/assets/css/style.css" rel="stylesheet" type="text/css">
    {{-- <link rel="stylesheet" href="../css/metismenu.min.css"> --}}
    @yield('head')


</head>

<body>
    @php
        $user = Auth::user();
        $user_rol = $user->role;
    @endphp
    <div id="app">
        <style>
            /* .sidebar {
                width: 15%;
            } */
        </style>
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
            @include('layouts.footerMobile')
            @elsemobile
            @include('layouts.footer')
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
    <script src="http://lobo-crm.test/assets/js/jquery.min.js"></script>

    <script src="https://code.jquery.com/jquery-3.6.1.min.js"
        integrity="sha256-o88AwQnZB+VDvE9tvIXrMQaPlFFSUTR+nldQm1LuPXQ=" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"
        integrity="sha384-oBqDVmMz9ATKxIep9tiCxS/Z9fNfEXiDAYTujMAeBAsjFuCZSmKbSSUnQlmh/jp3" crossorigin="anonymous">
    </script>
    <script src="https://code.jquery.com/ui/1.13.2/jquery-ui.js"></script>
    {{-- <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.2/dist/js/bootstrap.min.js"
        integrity="sha384-IDwe1+LCz02ROU9k972gdyvl+AESN10+x7tBKgc9I5HFtuNz0wWnPclzo6p9vxnk" crossorigin="anonymous">
    </script>
    <script src="https://cdn.datatables.net/1.13.2/js/jquery.dataTables.min.js"></script> --}}
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <script src="http://lobo-crm.test/assets/js/bootstrap.bundle.min.js"></script>
    <script src="http://lobo-crm.test/assets/js/metismenu.min.js"></script>
    <script src="http://lobo-crm.test/assets/js/jquery.slimscroll.js"></script>
    <script src="http://lobo-crm.test/assets/js/waves.min.js"></script>

    <!--Morris Chart-->
    {{-- <script src="../plugins/morris/morris.min.js"></script> --}}
    <script src="http://lobo-crm.test/plugins/raphael/raphael.min.js"></script>

    {{-- <script src="../assets/pages/dashboard.init.js"></script> --}}

    <!-- App js -->
    <script src="http://lobo-crm.test/assets/js/app.js"></script>

    @livewireScripts
    @yield('scripts')


</body>

</html>
