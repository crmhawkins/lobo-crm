<div class="container-fluid">
    <div class="page-title-box">
        <div class="row align-items-center">
            <div class="col-sm-6">
                <h4 class="page-title">Control Presupuestario</h4>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-right">
                    <li class="breadcrumb-item"><a href="javascript:void(0);">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="javascript:void(0);">Control Presupuestario</a></li>
                </ol>
            </div>
        </div> <!-- end row -->
    </div>
    <div>
    <div class="col-md-12 mt-4 scroll" x-data="{}" x-init="$nextTick(() => {
                            $('#datatable-buttons').DataTable({
                                stateSave: true,
                                responsive: false,
                                layout: {
                                    
                                },
                                scrollX: true,
                                searching: false,
                                info: false,
                                lengthChange: false,
                                 paging: false,
                                buttons: ['copy', 'excelHtml5', 'pdf', 'colvis'],
                                language: {
                                    lengthMenu: 'Mostrar _MENU_ registros por página',
                                    zeroRecords: 'No se encontraron registros',
                                    info: 'Mostrando página _PAGE_ de _PAGES_',
                                    infoEmpty: 'No hay registros disponibles',
                                    infoFiltered: '(filtrado de _MAX_ total registros)',
                                    search: 'Buscar:'
                                },
                                
                            });
                        })" wire:key='{{ rand() }}'>
                        <table id="datatable-buttons" class="table table-striped table-bordered dt-responsive nowrap " style="border-collapse: collapse; border-spacing: 0; width: 100%;" wire:key='{{ rand() }}'>
                            <thead>
                                <tr>
                                    <th>VENTAS (A)</th>
                                    @foreach ($delegaciones as $delegacion)
                                        @if($delegacion->nombre != '00 GENERAL GLOBAL')
                                            <th>{{ $delegacion->nombre }}</th>
                                        @endif
                                    @endforeach
                                    <th>Total</th> <!-- Columna para el total general -->
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($totalesPorDelegacionYTrimestre as $trimestre => $totalesDelegacion)
                                    <tr>
                                        <td><b>{{ $trimestre }}</b></td>
                                        @foreach ($delegaciones as $delegacion)
                                            @if($delegacion->nombre != '00 GENERAL GLOBAL')
                                                @php
                                                    $totalTrimestreDelegacion = $totalesDelegacion[$delegacion->COD] ?? 0;
                                                @endphp
                                                <td>{{ number_format($totalTrimestreDelegacion, 2) }}€</td> <!-- Aplicar number_format -->
                                            @endif
                                        @endforeach
                                        @php
                                            $totalTrimestreGeneral = $totalesDelegacion['total_general'] ?? 0;
                                        @endphp
                                        <td><b>{{ number_format($totalTrimestreGeneral, 2) }}€</b></td> <!-- Aplicar number_format -->
                                    </tr>
                                @endforeach

                                <!-- Fila para mostrar el total por delegación del año completo -->
                                <tr>
                                    <td><b>Total Anual</b></td>
                                    @foreach ($delegaciones as $delegacion)
                                        @if($delegacion->nombre != '00 GENERAL GLOBAL')
                                            @php
                                                $totalAnualDelegacion = 0;
                                                // Iterar sobre todos los trimestres para sumar el total anual de la delegación
                                                foreach ($totalesPorDelegacionYTrimestre as $totalesDelegacion) {
                                                    $totalAnualDelegacion += $totalesDelegacion[$delegacion->COD] ?? 0;
                                                }
                                            @endphp
                                            <td><b>{{ number_format($totalAnualDelegacion, 2) }}€</b></td> <!-- Aplicar number_format -->
                                        @endif
                                    @endforeach
                                    @php
                                        $totalAnualGeneral = 0;
                                        // Iterar sobre todos los trimestres para sumar el total anual general
                                        foreach ($totalesPorDelegacionYTrimestre as $totalesDelegacion) {
                                            $totalAnualGeneral += $totalesDelegacion['total_general'] ?? 0;
                                        }
                                    @endphp
                                    <td><b>{{ number_format($totalAnualGeneral, 2) }}€</b></td> <!-- Mostrar el total general anual con number_format -->
                                </tr>
                            </tbody>
                        </table>
    </div>
    <div class="col-md-12 mt-4 mb-5" x-data="{}" x-init="$nextTick(() => {
                            $('#datatable-compras').DataTable({
                                stateSave: true,
                                responsive: false,
                                layout: {
                                    
                                },
                                scrollX: true,
                                searching: false,
                                 paging: false,
                                 info: false,
                                buttons: ['copy', 'excelHtml5', 'pdf', 'colvis'],
                                language: {
                                    lengthMenu: 'Mostrar _MENU_ registros por página',
                                    zeroRecords: 'No se encontraron registros',
                                    info: 'Mostrando página _PAGE_ de _PAGES_',
                                    infoEmpty: 'No hay registros disponibles',
                                    infoFiltered: '(filtrado de _MAX_ total registros)',
                                    search: 'Buscar:'
                                },
                                
                            });
                        })" wire:key='{{ rand() }}'>
                        <table id="datatable-compras" class="table table-striped table-bordered dt-responsive nowrap" style="border-collapse: collapse; border-spacing: 0; width: 100%;" wire:key='{{ rand() }}'>
                                <thead>
                                    <tr>
                                        <th>COMPRAS (B)</th>
                                        @foreach ($delegaciones as $delegacion)
                                            @if($delegacion->nombre != '00 GENERAL GLOBAL')
                                                <th>{{ $delegacion->nombre }}</th>
                                            @endif
                                        @endforeach
                                        <th>Total General</th> <!-- Columna para el total general -->
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($totalesPorDelegacionYTrimestreCompras as $trimestre => $totalesDelegacion)
                                        <tr>
                                            <td><b>{{ $trimestre }}</b></td>
                                            @foreach ($delegaciones as $delegacion)
                                                @if($delegacion->nombre != '00 GENERAL GLOBAL')
                                                    @php
                                                        $totalTrimestreDelegacion = $totalesDelegacion[$delegacion->id] ?? 0;
                                                    @endphp
                                                    <td>{{ number_format($totalTrimestreDelegacion, 2) }} €</td> <!-- Aplicar number_format -->
                                                @endif
                                            @endforeach
                                            @php
                                                $totalTrimestreGeneral = $totalesDelegacion['total_general'] ?? 0;
                                            @endphp
                                            <td><b>{{ number_format($totalTrimestreGeneral, 2) }} €</b></td> <!-- Aplicar number_format -->
                                        </tr>
                                    @endforeach

                                    <!-- Fila para mostrar el total por delegación del año completo -->
                                    <tr>
                                        <td><b>Total Anual</b></td>
                                        @foreach ($delegaciones as $delegacion)
                                            @if($delegacion->nombre != '00 GENERAL GLOBAL')
                                                @php
                                                    $totalAnualDelegacionCompras = 0;
                                                    // Iterar sobre todos los trimestres para sumar el total anual de la delegación (compras)
                                                    foreach ($totalesPorDelegacionYTrimestreCompras as $totalesDelegacion) {
                                                        $totalAnualDelegacionCompras += $totalesDelegacion[$delegacion->id] ?? 0;
                                                    }
                                                @endphp
                                                <td><b>{{ number_format($totalAnualDelegacionCompras, 2) }} €</b></td> <!-- Aplicar number_format -->
                                            @endif
                                        @endforeach
                                        @php
                                            $totalAnualGeneralCompras = 0;
                                            // Iterar sobre todos los trimestres para sumar el total anual general (compras)
                                            foreach ($totalesPorDelegacionYTrimestreCompras as $totalesDelegacion) {
                                                $totalAnualGeneralCompras += $totalesDelegacion['total_general'] ?? 0;
                                            }
                                        @endphp
                                        <td><b>{{ number_format($totalAnualGeneralCompras, 2) }} €</b></td> <!-- Aplicar number_format -->
                                    </tr>

                                    <!-- Fila para mostrar el porcentaje de total ventas sobre total compras por delegación -->
                                    <tr>
                                        <td><b>Ventas/Compras</b></td>
                                        @foreach ($delegaciones as $delegacion)
                                            @if($delegacion->nombre != '00 GENERAL GLOBAL')
                                                @php
                                                    // Sumar el total anual de ventas por delegación
                                                    $totalAnualDelegacionVentas = 0;
                                                    foreach ($totalesPorDelegacionYTrimestre as $totalesDelegacion) {
                                                        $totalAnualDelegacionVentas += $totalesDelegacion[$delegacion->COD] ?? 0;
                                                    }

                                                    // Recalcular el total anual de compras para cada delegación
                                                    $totalAnualDelegacionCompras = 0;
                                                    foreach ($totalesPorDelegacionYTrimestreCompras as $totalesDelegacion) {
                                                        $totalAnualDelegacionCompras += $totalesDelegacion[$delegacion->id] ?? 0;
                                                    }

                                                    // Calcular el porcentaje de ventas sobre compras
                                                    $porcentajeVentasCompras = ($totalAnualDelegacionCompras > 0) 
                                                        ? ($totalAnualDelegacionCompras / $totalAnualDelegacionVentas) * 100 
                                                        : 0;
                                                @endphp
                                                <td>{{ number_format($porcentajeVentasCompras, 2) }}%</td>
                                            @endif
                                        @endforeach
                                        @php
                                            // Calcular el porcentaje general de ventas sobre compras
                                            $totalAnualGeneralVentas = 0;
                                            foreach ($totalesPorDelegacionYTrimestre as $totalesDelegacion) {
                                                $totalAnualGeneralVentas += $totalesDelegacion['total_general'] ?? 0;
                                            }
                                            $porcentajeGeneralVentasCompras = ($totalAnualGeneralCompras > 0)
                                                ? ($totalAnualGeneralVentas / $totalAnualGeneralCompras) * 100
                                                : 0;
                                        @endphp
                                        <td><b>{{ number_format($porcentajeGeneralVentasCompras, 2) }}%</b></td>
                                    </tr>
                                </tbody>
                            </table>

    </div>
    <div class="col-md-12 mt-4 mb-5" x-data="{}" x-init="$nextTick(() => {
                            $('#resultado-ab').DataTable({
                                stateSave: true,
                                responsive: false,
                                layout: {
                                    
                                },
                                scrollX: true,
                                searching: false,
                                 paging: false,
                                 info: false,
                                buttons: ['copy', 'excelHtml5', 'pdf', 'colvis'],
                                language: {
                                    lengthMenu: 'Mostrar _MENU_ registros por página',
                                    zeroRecords: 'No se encontraron registros',
                                    info: 'Mostrando página _PAGE_ de _PAGES_',
                                    infoEmpty: 'No hay registros disponibles',
                                    infoFiltered: '(filtrado de _MAX_ total registros)',
                                    search: 'Buscar:'
                                },
                                
                            });
                        })" wire:key='{{ rand() }}'>
        <table id="resultado-ab" class="table table-striped table-bordered dt-responsive nowrap mb-5" style="border-collapse: collapse; border-spacing: 0; width: 100%;" wire:key='{{ rand() }}'>
            <thead>
                <tr>
                    <th>RESULTADO (A - B) = C</th>
                    @foreach ($delegaciones as $delegacion)
                        @if($delegacion->nombre != '00 GENERAL GLOBAL')
                            <th>{{ $delegacion->nombre }}</th>
                        @endif
                    @endforeach
                    <th>Total General</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td><b>Resultado</b></td>
                    @foreach ($delegaciones as $delegacion)
                        @if($delegacion->nombre != '00 GENERAL GLOBAL')
                            <td>{{ number_format($resultadosPorDelegacion[$delegacion->COD] ?? 0, 2) }}€</td> <!-- Mostrar el resultado calculado -->
                        @endif
                    @endforeach
                    <td><b>{{ number_format(array_sum($resultadosPorDelegacion), 2) }}€</b></td> <!-- Mostrar el total general -->
                </tr>
            </tbody>
        </table>
    </div>


</div>

@section('scripts')

<script src="../assets/js/jquery.slimscroll.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>
<link href="https://cdn.datatables.net/v/bs5/jq-3.7.0/jszip-3.10.1/dt-2.0.3/b-3.0.1/b-colvis-3.0.1/b-html5-3.0.1/b-print-3.0.1/r-3.0.1/datatables.min.css" rel="stylesheet">
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.8/js/select2.min.js" defer></script>

<script src="https://cdn.datatables.net/v/bs5/jq-3.7.0/jszip-3.10.1/dt-2.0.3/b-3.0.1/b-colvis-3.0.1/b-html5-3.0.1/b-print-3.0.1/r-3.0.1/datatables.min.js"></script>
<!-- Responsive examples -->
{{-- <script src="../assets/pages/datatables.init.js"></script> --}}

@endsection