@extends('layouts.app')

@section('title', 'Control Presupuestario Análisis Global')

@section('head')
    <style>
        .table-responsive {
            overflow-x: auto;
        }

        table th, table td {
            white-space: nowrap;
        }

        .trimestre-header {
            background-color: #f8f9fa;
            font-weight: bold;
        }

        .text-center {
            text-align: center;
        }

        .mes-header {
            font-weight: bold;
            background-color: grey;
            text-align: center;
        }

        .collapse-icon {
            margin-left: 10px;
            transition: transform 0.2s;
        }

        .collapsed .collapse-icon {
            transform: rotate(90deg);
        }
    </style>
@endsection

@section('content-principal')
    <div class="container-fluid">
        <h2>Análisis Global - Trimestre {{ $trimestre }} - Año {{ $year }}</h2>
        <button onclick="exportarTablasAExcel()" class="btn btn-success mb-4">Exportar a Excel</button>


        <!-- Filtro por año y trimestre -->
        <form action="{{ route('control-presupuestario.analisis-global') }}" method="GET" class="mb-4">
            <div class="form-group">
                <label for="year">Seleccionar Año:</label>
                <select name="year" id="year" class="form-control w-25 d-inline-block">
                    @for($i = 2020; $i <= \Carbon\Carbon::now()->year; $i++)
                        <option value="{{ $i }}" {{ $i == $year ? 'selected' : '' }}>{{ $i }}</option>
                    @endfor
                </select>

                <label for="trimestre">Seleccionar Trimestre:</label>
                <select name="trimestre" id="trimestre" class="form-control w-25 d-inline-block">
                    @foreach([1, 2, 3, 4] as $trim)
                        <option value="{{ $trim }}" {{ $trim == $trimestre ? 'selected' : '' }}>Trimestre {{ $trim }}</option>
                    @endforeach
                </select>

                <button type="submit" class="btn btn-primary">Filtrar</button>
            </div>
        </form>

        <!-- Tabla de ventas por trimestre y delegación -->
        <h3>(A) Ventas por Trimestre</h3>
        <div class="table-responsive">
            <table class="table table-bordered">
                <thead>
                    <tr class="trimestre-header text-center">
                        <th>Mes</th>
                        @foreach($delegaciones as $delegacion)
                            <th>{{ $delegacion->nombre }}</th>
                        @endforeach
                        <th>Total</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $totalesTrimestrePorDelegacion = [];
                        foreach ($delegaciones as $delegacion) {
                            $totalesTrimestrePorDelegacion[$delegacion->nombre] = 0;
                        }
                        $totalTrimestreGeneral = 0;
                    @endphp

                    <!-- Iterar por meses del trimestre -->
                    @foreach($ventasPorDelegacion as $mes => $delegacionVentas)
                        @php
                            $totalMesGeneral = 0;
                        @endphp
                        <tr class="mes-header">
                            <td>{{ \Carbon\Carbon::create()->month($mes)->translatedFormat('F') }}</td>
                            @foreach($delegaciones as $delegacion)
                                @php
                                    $totalDelegacionMes = $delegacionVentas[$delegacion->nombre] ?? 0;
                                    $totalesTrimestrePorDelegacion[$delegacion->nombre] += $totalDelegacionMes;
                                    $totalMesGeneral += $totalDelegacionMes;
                                @endphp
                                <td>{{ number_format($totalDelegacionMes, 2, ',', '.') }} €</td>
                            @endforeach
                            <td><strong>{{ number_format($totalMesGeneral, 2, ',', '.') }} €</strong></td>
                        </tr>
                    @endforeach

                    <!-- Fila de total del trimestre -->
                    <tr class="trimestre-header">
                        <td><strong>Total Trimestre</strong></td>
                        @foreach($totalesTrimestrePorDelegacion as $delegacionNombre => $total)
                            <td><strong>{{ number_format($total, 2, ',', '.') }} €</strong></td>
                        @endforeach
                        <td><strong>{{ number_format(array_sum($totalesTrimestrePorDelegacion), 2, ',', '.') }} €</strong></td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Tabla de compras por trimestre y delegación -->
        <h3>(B) Compras por Trimestre</h3>
        <div class="table-responsive">
            <table class="table table-bordered">
                <thead>
                    <tr class="trimestre-header text-center">
                        <th>Mes</th>
                        @foreach($delegaciones as $delegacion)
                            <th>{{ $delegacion->nombre }}</th>
                        @endforeach
                        <th>Total</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $totalesComprasTrimestre = [];
                        foreach ($delegaciones as $delegacion) {
                            $totalesComprasTrimestre[$delegacion->nombre] = 0;
                        }
                    @endphp

                    <!-- Iterar por meses del trimestre para compras -->
                    @foreach($comprasPorDelegacion as $mes => $comprasMes)
                        @php
                            $totalMesCompras = 0;
                        @endphp
                        <tr class="mes-header">
                            <td>{{ \Carbon\Carbon::create()->month($mes)->translatedFormat('F') }}</td>
                            @foreach($delegaciones as $delegacion)
                                @php
                                    $totalComprasDelegacionMes = $comprasMes[$delegacion->nombre] ?? 0;
                                    $totalesComprasTrimestre[$delegacion->nombre] += $totalComprasDelegacionMes;
                                    $totalMesCompras += $totalComprasDelegacionMes;
                                @endphp
                                <td>{{ number_format($totalComprasDelegacionMes, 2, ',', '.') }} €</td>
                            @endforeach
                            <td><strong>{{ number_format($totalMesCompras, 2, ',', '.') }} €</strong></td>
                        </tr>
                    @endforeach

                    <!-- Fila de total del trimestre para compras -->
                    <tr class="trimestre-header">
                        <td><strong>Total Trimestre Compras</strong></td>
                        @foreach($totalesComprasTrimestre as $delegacionNombre => $total)
                            <td><strong>{{ number_format($total, 2, ',', '.') }} €</strong></td>
                        @endforeach
                        <td><strong>{{ number_format(array_sum($totalesComprasTrimestre), 2, ',', '.') }} €</strong></td>
                    </tr>
                </tbody>
            </table>
        </div>
         <!-- Tabla de Resultados (A-B) -->
         <h3>Resultado (A-B) = C por Trimestre</h3>
         <div class="table-responsive mb-5">
             <table class="table table-bordered">
                 <thead>
                     <tr class="trimestre-header text-center">
                         <th>Mes</th>
                         @foreach($delegaciones as $delegacion)
                             <th>{{ $delegacion->nombre }}</th>
                         @endforeach
                         <th>Total</th>
                     </tr>
                 </thead>
                 <tbody>
                     @php
                         $totalesResultadoTrimestre = [];
                         foreach ($delegaciones as $delegacion) {
                             $totalesResultadoTrimestre[$delegacion->nombre] = 0;
                         }
                     @endphp
 
                     <!-- Iterar por meses del trimestre -->
                     @foreach($resultadosPorDelegacion as $mes => $resultadosMes)
                         @php
                             $totalMesResultado = 0;
                         @endphp
                         <tr class="mes-header">
                             <td>{{ \Carbon\Carbon::create()->month($mes)->translatedFormat('F') }}</td>
                             @foreach($delegaciones as $delegacion)
                                 @php
                                     $resultadoDelegacionMes = $resultadosMes[$delegacion->nombre] ?? 0;
                                     $totalesResultadoTrimestre[$delegacion->nombre] += $resultadoDelegacionMes;
                                     $totalMesResultado += $resultadoDelegacionMes;
                                 @endphp
                                 <td>{{ number_format($resultadoDelegacionMes, 2, ',', '.') }} €</td>
                             @endforeach
                             <td><strong>{{ number_format($totalMesResultado, 2, ',', '.') }} €</strong></td>
                         </tr>
                     @endforeach
 
                     <!-- Fila de total del trimestre para resultados -->
                     <tr class="trimestre-header">
                         <td><strong>Total Trimestre Resultados</strong></td>
                         @foreach($totalesResultadoTrimestre as $delegacionNombre => $total)
                             <td><strong>{{ number_format($total, 2, ',', '.') }} €</strong></td>
                         @endforeach
                         <td><strong>{{ number_format(array_sum($totalesResultadoTrimestre), 2, ',', '.') }} €</strong></td>
                     </tr>
                 </tbody>
             </table>
         </div>
          <!-- Tabla de gastos estructurales (D) -->
        <h3>(D) Gasto Estructural por Trimestre</h3>
        <div class="table-responsive mb-5">
            <table class="table table-bordered">
                <thead>
                    <tr class="trimestre-header text-center">
                        <th>Mes</th>
                        @foreach($delegaciones as $delegacion)
                            <th>{{ $delegacion->nombre }}</th>
                        @endforeach
                        <th>Total</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $totalesGastosEstructuralesTrimestre = [];
                        foreach ($delegaciones as $delegacion) {
                            $totalesGastosEstructuralesTrimestre[$delegacion->nombre] = 0;
                        }
                    @endphp

                    <!-- Iterar por meses del trimestre para gastos estructurales -->
                    @foreach($gastosEstructuralesPorDelegacion as $mes => $gastosMes)
                        @php
                            $totalMesGastos = 0;
                        @endphp
                        <tr class="mes-header">
                            <td>{{ \Carbon\Carbon::create()->month($mes)->translatedFormat('F') }}</td>
                            @foreach($delegaciones as $delegacion)
                                @php
                                    $totalGastosDelegacionMes = $gastosMes[$delegacion->nombre] ?? 0;
                                    $totalesGastosEstructuralesTrimestre[$delegacion->nombre] += $totalGastosDelegacionMes;
                                    $totalMesGastos += $totalGastosDelegacionMes;
                                @endphp
                                <td>{{ number_format($totalGastosDelegacionMes, 2, ',', '.') }} €</td>
                            @endforeach
                            <td><strong>{{ number_format($totalMesGastos, 2, ',', '.') }} €</strong></td>
                        </tr>
                    @endforeach

                    <!-- Fila de total del trimestre para gastos estructurales -->
                    <tr class="trimestre-header">
                        <td><strong>Total Trimestre Gastos Estructurales</strong></td>
                        @foreach($totalesGastosEstructuralesTrimestre as $delegacionNombre => $total)
                            <td><strong>{{ number_format($total, 2, ',', '.') }} €</strong></td>
                        @endforeach
                        <td><strong>{{ number_format(array_sum($totalesGastosEstructuralesTrimestre), 2, ',', '.') }} €</strong></td>
                    </tr>
                </tbody>
            </table>
        </div>
        <h3>(E) Gasto Variable por Trimestre</h3>
        <div class="table-responsive mb-5">
            <table class="table table-bordered">
                <thead>
                    <tr class="trimestre-header text-center">
                        <th>Mes</th>
                        @foreach($delegaciones as $delegacion)
                            <th>{{ $delegacion->nombre }}</th>
                        @endforeach
                        <th>Total</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $totalesGastosVariablesTrimestre = [];
                        foreach ($delegaciones as $delegacion) {
                            $totalesGastosVariablesTrimestre[$delegacion->nombre] = 0;
                        }
                    @endphp

                    <!-- Iterar por meses del trimestre para gastos estructurales -->
                    @foreach($gastosVariablesPorDelegacion as $mes => $gastosMes)
                        @php
                            $totalMesGastos = 0;
                        @endphp
                        <tr class="mes-header">
                            <td>{{ \Carbon\Carbon::create()->month($mes)->translatedFormat('F') }}</td>
                            @foreach($delegaciones as $delegacion)
                                @php
                                    $totalGastosDelegacionMes = $gastosMes[$delegacion->nombre] ?? 0;
                                    $totalesGastosVariablesTrimestre[$delegacion->nombre] += $totalGastosDelegacionMes;
                                    $totalMesGastos += $totalGastosDelegacionMes;
                                @endphp
                                <td>{{ number_format($totalGastosDelegacionMes, 2, ',', '.') }} €</td>
                            @endforeach
                            <td><strong>{{ number_format($totalMesGastos, 2, ',', '.') }} €</strong></td>
                        </tr>
                    @endforeach

                    <!-- Fila de total del trimestre para gastos estructurales -->
                    <tr class="trimestre-header">
                        <td><strong>Total Trimestre Gastos Variables</strong></td>
                        @foreach($totalesGastosVariablesTrimestre as $delegacionNombre => $total)
                            <td><strong>{{ number_format($total, 2, ',', '.') }} €</strong></td>
                        @endforeach
                        <td><strong>{{ number_format(array_sum($totalesGastosVariablesTrimestre), 2, ',', '.') }} €</strong></td>
                    </tr>
                </tbody>
            </table>
        </div>

        <h3>(G) Gasto Logístico por Trimestre</h3>
        <div class="table-responsive mb-5">
            <table class="table table-bordered">
                <thead>
                    <tr class="trimestre-header text-center">
                        <th>Mes</th>
                        @foreach($delegaciones as $delegacion)
                            <th>{{ $delegacion->nombre }}</th>
                        @endforeach
                        <th>Total</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $totalesGastosLogisticaTrimestre = [];
                        foreach ($delegaciones as $delegacion) {
                            $totalesGastosLogisticaTrimestre[$delegacion->nombre] = 0;
                        }
                    @endphp

                    <!-- Iterar por meses del trimestre para gastos logísticos -->
                    @foreach($gastosLogisticaPorDelegacion as $mes => $gastosMes)
                        @php
                            $totalMesGastos = 0;
                        @endphp
                        <tr class="mes-header">
                            <td>{{ \Carbon\Carbon::create()->month($mes)->translatedFormat('F') }}</td>
                            @foreach($delegaciones as $delegacion)
                                @php
                                    $totalGastosDelegacionMes = $gastosMes[$delegacion->nombre] ?? 0;
                                    $totalesGastosLogisticaTrimestre[$delegacion->nombre] += $totalGastosDelegacionMes;
                                    $totalMesGastos += $totalGastosDelegacionMes;
                                @endphp
                                <td>{{ number_format($totalGastosDelegacionMes, 2, ',', '.') }} €</td>
                            @endforeach
                            <td><strong>{{ number_format($totalMesGastos, 2, ',', '.') }} €</strong></td>
                        </tr>
                    @endforeach

                    <!-- Fila de total del trimestre para gastos logísticos -->
                    <tr class="trimestre-header">
                        <td><strong>Total Trimestre Gastos Logísticos</strong></td>
                        @foreach($totalesGastosLogisticaTrimestre as $delegacionNombre => $total)
                            <td><strong>{{ number_format($total, 2, ',', '.') }} €</strong></td>
                        @endforeach
                        <td><strong>{{ number_format(array_sum($totalesGastosLogisticaTrimestre), 2, ',', '.') }} €</strong></td>
                    </tr>
                </tbody>
            </table>
        </div>
        <h3>Resultado (C-D-E-F) = G por Trimestre</h3>
        <div class="table-responsive mb-5">
            <table class="table table-bordered">
                <thead>
                    <tr class="trimestre-header text-center">
                        <th>Mes</th>
                        @foreach($delegaciones as $delegacion)
                            <th>{{ $delegacion->nombre }}</th>
                        @endforeach
                        <th>Total</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $totalesResultadoGTrimestre = [];
                        foreach ($delegaciones as $delegacion) {
                            $totalesResultadoGTrimestre[$delegacion->nombre] = 0;
                        }
                    @endphp

                    <!-- Iterar por meses del trimestre para calcular G = C - D - E - F -->
                    @foreach($resultadosPorDelegacion as $mes => $resultadosMes)
                        @php
                            $totalMesG = 0;
                        @endphp
                        <tr class="mes-header">
                            <td>{{ \Carbon\Carbon::create()->month($mes)->translatedFormat('F') }}</td>
                            @foreach($delegaciones as $delegacion)
                                @php
                                    $resultadoC = $resultadosMes[$delegacion->nombre] ?? 0;
                                    $gastoEstructuralD = $gastosEstructuralesPorDelegacion[$mes][$delegacion->nombre] ?? 0;
                                    $gastoVariableE = $gastosVariablesPorDelegacion[$mes][$delegacion->nombre] ?? 0;
                                    $gastoLogisticoF = $gastosLogisticaPorDelegacion[$mes][$delegacion->nombre] ?? 0;

                                    // Calcular G = C - D - E - F
                                    $resultadoGDelegacionMes = $resultadoC - $gastoEstructuralD - $gastoVariableE - $gastoLogisticoF;

                                    $totalesResultadoGTrimestre[$delegacion->nombre] += $resultadoGDelegacionMes;
                                    $totalMesG += $resultadoGDelegacionMes;
                                @endphp
                                <td>{{ number_format($resultadoGDelegacionMes, 2, ',', '.') }} €</td>
                            @endforeach
                            <td><strong>{{ number_format($totalMesG, 2, ',', '.') }} €</strong></td>
                        </tr>
                    @endforeach

                    <!-- Fila de total del trimestre para el resultado G -->
                    <tr class="trimestre-header">
                        <td><strong>Total Trimestre Resultado (C-D-E-F)</strong></td>
                        @foreach($totalesResultadoGTrimestre as $delegacionNombre => $total)
                            <td><strong>{{ number_format($total, 2, ',', '.') }} €</strong></td>
                        @endforeach
                        <td><strong>{{ number_format(array_sum($totalesResultadoGTrimestre), 2, ',', '.') }} €</strong></td>
                    </tr>
                </tbody>
            </table>
        </div>
        <!-- Tabla de Inversión Comercial (IC) -->
        <h3>(IC) Inversión Comercial por Trimestre</h3>
        <div class="table-responsive mb-5">
            <table class="table table-bordered">
                <thead>
                    <tr class="trimestre-header text-center">
                        <th>Trimestre</th>
                        @foreach($delegaciones as $delegacion)
                            <th>{{ $delegacion->nombre }}</th>
                        @endforeach
                        <th>Total</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $totalesInversionComercialPorMes = [];
                        foreach ($delegaciones as $delegacion) {
                            $totalesInversionComercialPorMes[$delegacion->nombre] = 0;
                        }
                        $totalGeneralInversionComercial = 0;
                    @endphp
                
                    <!-- Iterar por los meses -->
                    @foreach ($inversionComercialPorDelegacion as $mes => $delegacionesInversion)
                        <tr class="mes-header">
                            <td>{{ \Carbon\Carbon::create()->month($mes)->translatedFormat('F') }}</td>
                            @foreach($delegaciones as $delegacion)
                                @php
                                    $inversionComercialDelegacion = $delegacionesInversion[$delegacion->nombre] ?? 0;
                                    $totalesInversionComercialPorMes[$delegacion->nombre] += $inversionComercialDelegacion;
                                    $totalGeneralInversionComercial += $inversionComercialDelegacion;
                                @endphp
                                <td>{{ number_format($inversionComercialDelegacion, 2, ',', '.') }} €</td>
                            @endforeach
                            <td><strong>{{ number_format($totalGeneralInversionComercial, 2, ',', '.') }} €</strong></td>
                        </tr>
                    @endforeach
                
                    <!-- Fila de total de Inversión Comercial -->
                    <tr class="trimestre-header">
                        <td><strong>Total Inversión Comercial por Mes</strong></td>
                        @foreach($totalesInversionComercialPorMes as $delegacionNombre => $total)
                            <td><strong>{{ number_format($total, 2, ',', '.') }} €</strong></td>
                        @endforeach
                        <td><strong>{{ number_format(array_sum($totalesInversionComercialPorMes), 2, ',', '.') }} €</strong></td>
                    </tr>
                </tbody>
            </table>
        </div>
        <!-- Tabla de Inversión Comercial (IC) -->
        <h3>(IMKT) Inversión MKT por Trimestre</h3>
        <div class="table-responsive mb-5">
            <table class="table table-bordered">
                <thead>
                    <tr class="trimestre-header text-center">
                        <th>Trimestre</th>
                        @foreach($delegaciones as $delegacion)
                            <th>{{ $delegacion->nombre }}</th>
                        @endforeach
                        <th>Total</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $totalesInversionMarketingPorMes = [];
                        foreach ($delegaciones as $delegacion) {
                            $totalesInversionMarketingPorMes[$delegacion->nombre] = 0;
                        }
                        $totalGeneralInversionMarketing = 0;
                    @endphp
                
                    <!-- Iterar por los meses -->
                    @foreach ($inversionMarketingPorDelegacion as $mes => $delegacionesInversion)
                        <tr class="mes-header">
                            <td>{{ \Carbon\Carbon::create()->month($mes)->translatedFormat('F') }}</td>
                            @foreach($delegaciones as $delegacion)
                                @php
                                    $inversionMarketingDelegacion = $delegacionesInversion[$delegacion->nombre] ?? 0;
                                    $totalesInversionMarketingPorMes[$delegacion->nombre] += $inversionMarketingDelegacion;
                                    $totalGeneralInversionMarketing += $inversionMarketingDelegacion;
                                @endphp
                                <td>{{ number_format($inversionMarketingDelegacion, 2, ',', '.') }} €</td>
                            @endforeach
                            <td><strong>{{ number_format($totalGeneralInversionMarketing, 2, ',', '.') }} €</strong></td>
                        </tr>
                    @endforeach
                
                    <!-- Fila de total de Inversión Comercial -->
                    <tr class="trimestre-header">
                        <td><strong>Total Inversión Marketing por Mes</strong></td>
                        @foreach($totalesInversionMarketingPorMes as $delegacionNombre => $total)
                            <td><strong>{{ number_format($total, 2, ',', '.') }} €</strong></td>
                        @endforeach
                        <td><strong>{{ number_format(array_sum($totalesInversionMarketingPorMes), 2, ',', '.') }} €</strong></td>
                    </tr>
                </tbody>
            </table>
        </div>
        <h3>(IP) Inversión Patrocinio por Trimestre</h3>

        <div class="table-responsive mb-5">
            <table class="table table-bordered">
                <thead>
                    <tr class="trimestre-header text-center">
                        <th>Trimestre</th>
                        @foreach($delegaciones as $delegacion)
                            <th>{{ $delegacion->nombre }}</th>
                        @endforeach
                        <th>Total</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $totalesInversionPatrocinioPorMes = [];
                        foreach ($delegaciones as $delegacion) {
                            $totalesInversionPatrocinioPorMes[$delegacion->nombre] = 0;
                        }
                        $totalGeneralInversionPatrocinio = 0;
                    @endphp
                
                    <!-- Iterar por los meses -->
                    @foreach ($inversionPatrocinioPorDelegacion as $mes => $delegacionesInversion)
                        <tr class="mes-header">
                            <td>{{ \Carbon\Carbon::create()->month($mes)->translatedFormat('F') }}</td>
                            @foreach($delegaciones as $delegacion)
                                @php
                                    $inversionPatrocinioDelegacion = $delegacionesInversion[$delegacion->nombre] ?? 0;
                                    $totalesInversionPatrocinioPorMes[$delegacion->nombre] += $inversionPatrocinioDelegacion;
                                    $totalGeneralInversionPatrocinio += $inversionPatrocinioDelegacion;
                                @endphp
                                <td>{{ number_format($inversionPatrocinioDelegacion, 2, ',', '.') }} €</td>
                            @endforeach
                            <td><strong>{{ number_format($totalGeneralInversionPatrocinio, 2, ',', '.') }} €</strong></td>
                        </tr>
                    @endforeach
                
                    <!-- Fila de total de Inversión Comercial -->
                    <tr class="trimestre-header">
                        <td><strong>Total Inversión Patrocinio por Mes</strong></td>
                        @foreach($totalesInversionPatrocinioPorMes as $delegacionNombre => $total)
                            <td><strong>{{ number_format($total, 2, ',', '.') }} €</strong></td>
                        @endforeach
                        <td><strong>{{ number_format(array_sum($totalesInversionPatrocinioPorMes), 2, ',', '.') }} €</strong></td>
                    </tr>
                </tbody>
            </table>
        </div>
        <h3>Resultado (G-I) por Trimestre</h3>
        <div class="table-responsive mb-5">
            <table class="table table-bordered">
                <thead>
                    <tr class="trimestre-header text-center">
                        <th>Mes</th>
                        @foreach($delegaciones as $delegacion)
                            <th>{{ $delegacion->nombre }}</th>
                        @endforeach
                        <th>Total</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $totalesResultadoGITrimestre = [];
                        foreach ($delegaciones as $delegacion) {
                            $totalesResultadoGITrimestre[$delegacion->nombre] = 0;
                        }
                        $totalGeneralResultadoGI = 0;
                    @endphp
        
                    <!-- Iterar por los meses -->
                    @foreach($resultadoPorDelegacionGI as $mes => $delegacionesResultado)
                        @php
                            $totalMesResultadoGI = 0;
                        @endphp
                        <tr class="mes-header">
                            <td>{{ \Carbon\Carbon::create()->month($mes)->translatedFormat('F') }}</td>
                            @foreach($delegaciones as $delegacion)
                                @php
                                    $resultadoGI = $delegacionesResultado[$delegacion->nombre] ?? 0;
                                    $totalesResultadoGITrimestre[$delegacion->nombre] += $resultadoGI;
                                    $totalMesResultadoGI += $resultadoGI;
                                @endphp
                                <td>{{ number_format($resultadoGI, 2, ',', '.') }} €</td>
                            @endforeach
                            <td><strong>{{ number_format($totalMesResultadoGI, 2, ',', '.') }} €</strong></td>
                        </tr>
                    @endforeach
        
                    <!-- Fila de total del trimestre para el resultado G-I -->
                    <tr class="trimestre-header">
                        <td><strong>Total Trimestre Resultado (G - I)</strong></td>
                        @foreach($totalesResultadoGITrimestre as $delegacionNombre => $total)
                            <td><strong>{{ number_format($total, 2, ',', '.') }} €</strong></td>
                        @endforeach
                        <td><strong>{{ number_format(array_sum($totalesResultadoGITrimestre), 2, ',', '.') }} €</strong></td>
                    </tr>
                </tbody>
            </table>
        </div>
           <!-- Incluyendo SheetJS desde un CDN -->
   <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
   <script>
    function exportarTablasAExcel() {
        // Crear un nuevo libro de trabajo
        var wb = XLSX.utils.book_new();

        // Seleccionar todas las tablas dentro del contenedor principal
        document.querySelectorAll('.container-fluid .table-responsive').forEach((tableContainer, index) => {
            // Obtener el nombre de la tabla desde el h3 anterior
            var tableName = tableContainer.previousElementSibling;
            while (tableName && tableName.tagName !== 'H3') {
                tableName = tableName.previousElementSibling;
            }
            tableName = tableName ? tableName.textContent.trim() : 'Tabla ' + (index + 1);

            // Truncar el nombre de la hoja si es necesario
            if (tableName.length > 31) {
                tableName = tableName.substring(0, 28) + '...';
            }

            // Convertir la tabla HTML a una hoja de cálculo
            var table = tableContainer.querySelector('table');
            var ws = XLSX.utils.table_to_sheet(table);

            // Añadir la hoja de cálculo al libro de trabajo
            XLSX.utils.book_append_sheet(wb, ws, tableName);
        });

        // Exportar el libro de trabajo a un archivo Excel
        XLSX.writeFile(wb, 'analisis_global.xlsx');
    }
</script>
    </div>
@endsection
