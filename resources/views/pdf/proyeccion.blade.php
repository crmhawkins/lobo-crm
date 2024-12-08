<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Proyección Presupuestaria PDF</title>
    <style>
        .table {
            width: 100%;
            border-collapse: collapse;
        }
        .table th, .table td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: center;
        }
        .table th {
            background-color: #f8f9fa;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <h2 class="text-center">Proyección Presupuestaria {{ $year }}</h2>

    <!-- Tabla de Ventas -->
    <h3>1. Presupuesto de Ventas</h3>
    <table class="table">
        <thead>
            <tr>
                <th>Producto</th>
                <th>1 T. Unidades</th>
                <th>1 Trimestre</th>
                <th>2 T. Unidades</th>
                <th>2 Trimestre</th>
                <th>3 T. Unidades</th>
                <th>3 Trimestre</th>
                <th>4 T. Unidades</th>
                <th>4 Trimestre</th>
                <th>Total Botellas Anual</th>
                <th>Anual</th>
            </tr>
        </thead>
        <tbody>
            @php
                $productosAgrupados = [];
                $totalUnidades1T = 0;
                $totalPrecio1T = 0;
                $totalUnidades2T = 0;
                $totalPrecio2T = 0;
                $totalUnidades3T = 0;
                $totalPrecio3T = 0;
                $totalUnidades4T = 0;
                $totalPrecio4T = 0;
                $totalUnidadesAnual = 0;
                $totalPrecioAnual = 0;
            @endphp

            @foreach ($presupuestosPorTrimestre as $trimestre => $meses)
                @foreach ($meses as $mes => $productos)
                    @foreach ($productos as $producto => $data)
                        @php
                            if (!isset($productosAgrupados[$producto])) {
                                $productosAgrupados[$producto] = [
                                    'unidades1T' => 0,
                                    'coste1T' => 0,
                                    'unidades2T' => 0,
                                    'coste2T' => 0,
                                    'unidades3T' => 0,
                                    'coste3T' => 0,
                                    'unidades4T' => 0,
                                    'coste4T' => 0,
                                ];
                            }

                            foreach ($data['ventasDelegaciones'] as $delegacion => $ventas) {
                                if (isset($presupuestosPorTrimestre[1][$mes][$producto]['ventasDelegaciones'][$delegacion])) {
                                    $productosAgrupados[$producto]['unidades1T'] += $presupuestosPorTrimestre[1][$mes][$producto]['ventasDelegaciones'][$delegacion]['unidadesVendidas'];
                                    $productosAgrupados[$producto]['coste1T'] += $presupuestosPorTrimestre[1][$mes][$producto]['ventasDelegaciones'][$delegacion]['precioTotal'];
                                }
                                if (isset($presupuestosPorTrimestre[2][$mes][$producto]['ventasDelegaciones'][$delegacion])) {
                                    $productosAgrupados[$producto]['unidades2T'] += $presupuestosPorTrimestre[2][$mes][$producto]['ventasDelegaciones'][$delegacion]['unidadesVendidas'];
                                    $productosAgrupados[$producto]['coste2T'] += $presupuestosPorTrimestre[2][$mes][$producto]['ventasDelegaciones'][$delegacion]['precioTotal'];
                                }
                                if (isset($presupuestosPorTrimestre[3][$mes][$producto]['ventasDelegaciones'][$delegacion])) {
                                    $productosAgrupados[$producto]['unidades3T'] += $presupuestosPorTrimestre[3][$mes][$producto]['ventasDelegaciones'][$delegacion]['unidadesVendidas'];
                                    $productosAgrupados[$producto]['coste3T'] += $presupuestosPorTrimestre[3][$mes][$producto]['ventasDelegaciones'][$delegacion]['precioTotal'];
                                }
                                if (isset($presupuestosPorTrimestre[4][$mes][$producto]['ventasDelegaciones'][$delegacion])) {
                                    $productosAgrupados[$producto]['unidades4T'] += $presupuestosPorTrimestre[4][$mes][$producto]['ventasDelegaciones'][$delegacion]['unidadesVendidas'];
                                    $productosAgrupados[$producto]['coste4T'] += $presupuestosPorTrimestre[4][$mes][$producto]['ventasDelegaciones'][$delegacion]['precioTotal'];
                                }
                            }
                        @endphp
                    @endforeach
                @endforeach
            @endforeach

            @foreach ($productosAgrupados as $producto => $totales)
                @php
                    $costeAnual = $totales['coste1T'] + $totales['coste2T'] + $totales['coste3T'] + $totales['coste4T'];
                    $totalBotellasAnual = $totales['unidades1T'] + $totales['unidades2T'] + $totales['unidades3T'] + $totales['unidades4T'];

                    $totalUnidades1T += $totales['unidades1T'];
                    $totalPrecio1T += $totales['coste1T'];
                    $totalUnidades2T += $totales['unidades2T'];
                    $totalPrecio2T += $totales['coste2T'];
                    $totalUnidades3T += $totales['unidades3T'];
                    $totalPrecio3T += $totales['coste3T'];
                    $totalUnidades4T += $totales['unidades4T'];
                    $totalPrecio4T += $totales['coste4T'];
                    $totalUnidadesAnual += $totalBotellasAnual;
                    $totalPrecioAnual += $costeAnual;
                @endphp
                <tr>
                    <td>{{ $producto }}</td>
                    <td>{{ $totales['unidades1T'] }}</td>
                    <td>{{ number_format($totales['coste1T'], 2, ',', '.') }} €</td>
                    <td>{{ $totales['unidades2T'] }}</td>
                    <td>{{ number_format($totales['coste2T'], 2, ',', '.') }} €</td>
                    <td>{{ $totales['unidades3T'] }}</td>
                    <td>{{ number_format($totales['coste3T'], 2, ',', '.') }} €</td>
                    <td>{{ $totales['unidades4T'] }}</td>
                    <td>{{ number_format($totales['coste4T'], 2, ',', '.') }} €</td>
                    <td>{{ $totalBotellasAnual }}</td>
                    <td><strong>{{ number_format($costeAnual, 2, ',', '.') }} €</strong></td>
                </tr>
            @endforeach

            <tr class="font-weight-bold">
                <td><strong>Totales</strong></td>
                <td>{{ $totalUnidades1T }}</td>
                <td>{{ number_format($totalPrecio1T, 2, ',', '.') }} €</td>
                <td>{{ $totalUnidades2T }}</td>
                <td>{{ number_format($totalPrecio2T, 2, ',', '.') }} €</td>
                <td>{{ $totalUnidades3T }}</td>
                <td>{{ number_format($totalPrecio3T, 2, ',', '.') }} €</td>
                <td>{{ $totalUnidades4T }}</td>
                <td>{{ number_format($totalPrecio4T, 2, ',', '.') }} €</td>
                <td>{{ $totalUnidadesAnual }}</td>
                <td><strong>{{ number_format($totalPrecioAnual, 2, ',', '.') }} €</strong></td>
            </tr>
        </tbody>
    </table>

    <!-- Tabla de Compras -->
    <h3>2. Presupuesto de Compras</h3>
    <table class="table">
        <thead>
            <tr>
                <th>Producto</th>
                <th>1 T. Unidades</th>
                <th>1 Trimestre</th>
                <th>2 T. Unidades</th>
                <th>2 Trimestre</th>
                <th>3 T. Unidades</th>
                <th>3 Trimestre</th>
                <th>4 T. Unidades</th>
                <th>4 Trimestre</th>
                <th>Total Botellas Anual</th>
                <th>Anual</th>
            </tr>
        </thead>
        <tbody>
            @php
                $productosAgrupados = [];
                $totalUnidades1T = 0;
                $totalCoste1T = 0;
                $totalUnidades2T = 0;
                $totalCoste2T = 0;
                $totalUnidades3T = 0;
                $totalCoste3T = 0;
                $totalUnidades4T = 0;
                $totalCoste4T = 0;
                $totalUnidadesAnual = 0;
                $totalCosteAnual = 0;
            @endphp

            @foreach ($presupuestosPorTrimestre as $trimestre => $meses)
                @foreach ($meses as $mes => $productos)
                    @foreach ($productos as $producto => $data)
                        @php
                            if (!isset($productosAgrupados[$producto])) {
                                $productosAgrupados[$producto] = [
                                    'unidades1T' => 0,
                                    'coste1T' => 0,
                                    'unidades2T' => 0,
                                    'coste2T' => 0,
                                    'unidades3T' => 0,
                                    'coste3T' => 0,
                                    'unidades4T' => 0,
                                    'coste4T' => 0,
                                ];
                            }

                            foreach ($data['ventasDelegaciones'] as $delegacion => $ventas) {
                                if (isset($presupuestosPorTrimestre[1][$mes][$producto]['ventasDelegaciones'][$delegacion])) {
                                    $productosAgrupados[$producto]['unidades1T'] += $presupuestosPorTrimestre[1][$mes][$producto]['ventasDelegaciones'][$delegacion]['unidadesVendidas'];
                                    $productosAgrupados[$producto]['coste1T'] += $presupuestosPorTrimestre[1][$mes][$producto]['ventasDelegaciones'][$delegacion]['costeTotal'];
                                }
                                if (isset($presupuestosPorTrimestre[2][$mes][$producto]['ventasDelegaciones'][$delegacion])) {
                                    $productosAgrupados[$producto]['unidades2T'] += $presupuestosPorTrimestre[2][$mes][$producto]['ventasDelegaciones'][$delegacion]['unidadesVendidas'];
                                    $productosAgrupados[$producto]['coste2T'] += $presupuestosPorTrimestre[2][$mes][$producto]['ventasDelegaciones'][$delegacion]['costeTotal'];
                                }
                                if (isset($presupuestosPorTrimestre[3][$mes][$producto]['ventasDelegaciones'][$delegacion])) {
                                    $productosAgrupados[$producto]['unidades3T'] += $presupuestosPorTrimestre[3][$mes][$producto]['ventasDelegaciones'][$delegacion]['unidadesVendidas'];
                                    $productosAgrupados[$producto]['coste3T'] += $presupuestosPorTrimestre[3][$mes][$producto]['ventasDelegaciones'][$delegacion]['costeTotal'];
                                }
                                if (isset($presupuestosPorTrimestre[4][$mes][$producto]['ventasDelegaciones'][$delegacion])) {
                                    $productosAgrupados[$producto]['unidades4T'] += $presupuestosPorTrimestre[4][$mes][$producto]['ventasDelegaciones'][$delegacion]['unidadesVendidas'];
                                    $productosAgrupados[$producto]['coste4T'] += $presupuestosPorTrimestre[4][$mes][$producto]['ventasDelegaciones'][$delegacion]['costeTotal'];
                                }
                            }
                        @endphp
                    @endforeach
                @endforeach
            @endforeach

            @foreach ($productosAgrupados as $producto => $totales)
                @php
                    $costeAnual = $totales['coste1T'] + $totales['coste2T'] + $totales['coste3T'] + $totales['coste4T'];
                    $totalBotellasAnual = $totales['unidades1T'] + $totales['unidades2T'] + $totales['unidades3T'] + $totales['unidades4T'];

                    $totalUnidades1T += $totales['unidades1T'];
                    $totalCoste1T += $totales['coste1T'];
                    $totalUnidades2T += $totales['unidades2T'];
                    $totalCoste2T += $totales['coste2T'];
                    $totalUnidades3T += $totales['unidades3T'];
                    $totalCoste3T += $totales['coste3T'];
                    $totalUnidades4T += $totales['unidades4T'];
                    $totalCoste4T += $totales['coste4T'];
                    $totalUnidadesAnual += $totalBotellasAnual;
                    $totalCosteAnual += $costeAnual;
                @endphp
                <tr>
                    <td>{{ $producto }}</td>
                    <td>{{ $totales['unidades1T'] }}</td>
                    <td>{{ number_format($totales['coste1T'], 2, ',', '.') }} €</td>
                    <td>{{ $totales['unidades2T'] }}</td>
                    <td>{{ number_format($totales['coste2T'], 2, ',', '.') }} €</td>
                    <td>{{ $totales['unidades3T'] }}</td>
                    <td>{{ number_format($totales['coste3T'], 2, ',', '.') }} €</td>
                    <td>{{ $totales['unidades4T'] }}</td>
                    <td>{{ number_format($totales['coste4T'], 2, ',', '.') }} €</td>
                    <td>{{ $totalBotellasAnual }}</td>
                    <td><strong>{{ number_format($costeAnual, 2, ',', '.') }} €</strong></td>
                </tr>
            @endforeach

            <tr class="font-weight-bold">
                <td><strong>Totales</strong></td>
                <td>{{ $totalUnidades1T }}</td>
                <td>{{ number_format($totalCoste1T, 2, ',', '.') }} €</td>
                <td>{{ $totalUnidades2T }}</td>
                <td>{{ number_format($totalCoste2T, 2, ',', '.') }} €</td>
                <td>{{ $totalUnidades3T }}</td>
                <td>{{ number_format($totalCoste3T, 2, ',', '.') }} €</td>
                <td>{{ $totalUnidades4T }}</td>
                <td>{{ number_format($totalCoste4T, 2, ',', '.') }} €</td>
                <td>{{ $totalUnidadesAnual }}</td>
                <td><strong>{{ number_format($totalCosteAnual, 2, ',', '.') }} €</strong></td>
            </tr>
        </tbody>
    </table>

    <!-- Tabla de Beneficio -->
    <h3>3. Margen de Beneficio</h3>
    <table class="table">
        <thead>
            <tr>
                <th></th>
                <th>1 Trimestre</th>
                <th>2 Trimestre</th>
                <th>3 Trimestre</th>
                <th>4 Trimestre</th>
                <th>Anual</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>1. MARGEN DE BENEFICIO. (Ventas-Compras).Real</td>
                <td>{{ number_format($margenBeneficioReal['1T'], 2, ',', '.') }} €</td>
                <td>{{ number_format($margenBeneficioReal['2T'], 2, ',', '.') }} €</td>
                <td>{{ number_format($margenBeneficioReal['3T'], 2, ',', '.') }} €</td>
                <td>{{ number_format($margenBeneficioReal['4T'], 2, ',', '.') }} €</td>
                <td>{{ number_format($margenBeneficioReal['anual'], 2, ',', '.') }} €</td>
            </tr>
            <tr>
                <td>Margen</td>
                <td>{{ number_format($margenPorcentajeReal['1T'], 2, ',', '.') }} %</td>
                <td>{{ number_format($margenPorcentajeReal['2T'], 2, ',', '.') }} %</td>
                <td>{{ number_format($margenPorcentajeReal['3T'], 2, ',', '.') }} %</td>
                <td>{{ number_format($margenPorcentajeReal['4T'], 2, ',', '.') }} %</td>
                <td>{{ number_format($margenPorcentajeReal['anual'], 2, ',', '.') }} %</td>
            </tr>
            <tr>
                <td>2. MARGEN DE BENEFICIO. (Ventas-Compras).Presupuestado</td>
                <td>{{ number_format($margenBeneficioReal['1T'], 2, ',', '.') }} €</td>
                <td>{{ number_format($margenBeneficioReal['2T'], 2, ',', '.') }} €</td>
                <td>{{ number_format($margenBeneficioReal['3T'], 2, ',', '.') }} €</td>
                <td>{{ number_format($margenBeneficioReal['4T'], 2, ',', '.') }} €</td>
                <td>{{ number_format($margenBeneficioReal['anual'], 2, ',', '.') }} €</td>
            </tr>
            <tr>
                <td>Margen</td>
                <td>{{ number_format($margenPorcentajeReal['1T'], 2, ',', '.') }} %</td>
                <td>{{ number_format($margenPorcentajeReal['2T'], 2, ',', '.') }} %</td>
                <td>{{ number_format($margenPorcentajeReal['3T'], 2, ',', '.') }} %</td>
                <td>{{ number_format($margenPorcentajeReal['4T'], 2, ',', '.') }} %</td>
                <td>{{ number_format($margenPorcentajeReal['anual'], 2, ',', '.') }} %</td>
            </tr>
        </tbody>
    </table>

    <!-- Tabla de Gastos -->
    <h3>4. Presupuesto de Gastos</h3>
    <table class="table">
        <thead>
            <tr>
                <th>4. PRESUPUESTO DE GASTOS</th>
                <th>1 Trimestre</th>
                <th>2 Trimestre</th>
                <th>3 Trimestre</th>
                <th>4 Trimestre</th>
                <th>Anual</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td colspan="6" class="font-weight-bold">4.1. GASTOS ESTRUCTURALES</td>
            </tr>
            @foreach ($gastosEstructuralesPorTrimestre as $trimestre => $cuentas)
                @foreach ($cuentas as $cuenta => $data)
                    <tr>
                        <td>{{ $cuenta }} - {{$data['nombre']}}</td>
                        <td>{{ isset($gastosEstructuralesPorTrimestre[1][$cuenta]) ? number_format($gastosEstructuralesPorTrimestre[1][$cuenta]['total'], 2, ',', '.') : 0 }} €</td>
                        <td>{{ isset($gastosEstructuralesPorTrimestre[2][$cuenta]) ? number_format($gastosEstructuralesPorTrimestre[2][$cuenta]['total'], 2, ',', '.') : 0 }} €</td>
                        <td>{{ isset($gastosEstructuralesPorTrimestre[3][$cuenta]) ? number_format($gastosEstructuralesPorTrimestre[3][$cuenta]['total'], 2, ',', '.') : 0 }} €</td>
                        <td>{{ isset($gastosEstructuralesPorTrimestre[4][$cuenta]) ? number_format($gastosEstructuralesPorTrimestre[4][$cuenta]['total'], 2, ',', '.') : 0 }} €</td>
                        <td>{{ number_format($gastosEstructuralesAnuales[$cuenta], 2, ',', '.') }} €</td>
                    </tr>
                @endforeach
            @endforeach

            <tr class="font-weight-bold">
                <td>4.1. TOTAL GASTOS ESTRUCTURALES POR DELEGACIÓN</td>
                <td>{{ number_format($totalGastosEstructurales['1T'], 2, ',', '.') }} €</td>
                <td>{{ number_format($totalGastosEstructurales['2T'], 2, ',', '.') }} €</td>
                <td>{{ number_format($totalGastosEstructurales['3T'], 2, ',', '.') }} €</td>
                <td>{{ number_format($totalGastosEstructurales['4T'], 2, ',', '.') }} €</td>
                <td>{{ number_format($totalGastosEstructurales['anual'], 2, ',', '.') }} €</td>
            </tr>
            @foreach ($gastosVariablesPorTrimestre as $trimestre => $cuentas)
                @foreach ($cuentas as $cuenta => $data)
                    <tr>
                        <td>{{ $cuenta }} - {{$data['nombre']}}</td>
                        <td>{{ isset($gastosVariablesPorTrimestre[1][$cuenta]) ? number_format($gastosVariablesPorTrimestre[1][$cuenta]['total'], 2, ',', '.') : 0 }} €</td>
                        <td>{{ isset($gastosVariablesPorTrimestre[2][$cuenta]) ? number_format($gastosVariablesPorTrimestre[2][$cuenta]['total'], 2, ',', '.') : 0 }} €</td>
                        <td>{{ isset($gastosVariablesPorTrimestre[3][$cuenta]) ? number_format($gastosVariablesPorTrimestre[3][$cuenta]['total'], 2, ',', '.') : 0 }} €</td>
                        <td>{{ isset($gastosVariablesPorTrimestre[4][$cuenta]) ? number_format($gastosVariablesPorTrimestre[4][$cuenta]['total'], 2, ',', '.') : 0 }} €</td>
                        <td>{{ number_format($gastosVariablesAnuales[$cuenta], 2, ',', '.') }} €</td>
                    </tr>
                @endforeach
            @endforeach
            <tr class="font-weight-bold">
                <td>4.1. TOTAL GASTOS VARIABLES POR DELEGACIÓN</td>
                <td>{{ number_format($totalGastosVariables['1T'], 2, ',', '.') }} €</td>
                <td>{{ number_format($totalGastosVariables['2T'], 2, ',', '.') }} €</td>
                <td>{{ number_format($totalGastosVariables['3T'], 2, ',', '.') }} €</td>
                <td>{{ number_format($totalGastosVariables['4T'], 2, ',', '.') }} €</td>
                <td>{{ number_format($totalGastosVariables['anual'], 2, ',', '.') }} €</td>
            </tr>
            <tr class="font-weight-bold">
                @php
                    $totalT1 = $totalGastosEstructurales['1T'] + $totalGastosVariables['1T'];
                    $totalT2 = $totalGastosEstructurales['2T'] + $totalGastosVariables['2T'];
                    $totalT3 = $totalGastosEstructurales['3T'] + $totalGastosVariables['3T'];
                    $totalT4 = $totalGastosEstructurales['4T'] + $totalGastosVariables['4T'];
                    $totalAnual = $totalGastosEstructurales['anual'] + $totalGastosVariables['anual'];
                @endphp
                <td>4.TOTAL GASTO. 4.1+4.2</td>
                <td>{{number_format($totalT1, 2, ',', '.') }} €</td>
                <td>{{number_format($totalT2, 2, ',', '.') }} €</td>
                <td>{{number_format($totalT3, 2, ',', '.') }} €</td>
                <td>{{number_format($totalT4, 2, ',', '.') }} €</td>
                <td>{{number_format($totalGastosEstructurales['anual'] + $totalGastosVariables['anual'], 2, ',', '.') }} €</td>
            </tr>
        </tbody>
    </table>

    <!-- Tabla de Margen -->
    <h3>5. Margen</h3>
    @php
        $margenT1 = $margenBeneficioReal['1T'] - $totalT1;
        $margenT2 = $margenBeneficioReal['2T'] - $totalT2;
        $margenT3 = $margenBeneficioReal['3T'] - $totalT3;
        $margenT4 = $margenBeneficioReal['4T'] - $totalT4;
        $margenAnual = $margenBeneficioReal['anual'] - $totalAnual;
    @endphp
    <table class="table">
        <thead>
            <tr>
                <th></th>
                <th>1 Trimestre</th>
                <th>2 Trimestre</th>
                <th>3 Trimestre</th>
                <th>4 Trimestre</th>
                <th>Anual</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>MARGEN PRESUPUESTO</td>
                <td>{{ number_format($margenT1 , 2, ',', '.') }}€</td>
                <td>{{ number_format($margenT2 , 2, ',', '.') }}€</td>
                <td>{{ number_format($margenT3 , 2, ',', '.') }}€</td>
                <td>{{ number_format($margenT4 , 2, ',', '.') }}€</td>
                <td>{{ number_format($margenAnual , 2, ',', '.') }}€</td>
            </tr>
        </tbody>
    </table>

    <!-- Tabla de Inversión -->
    <h3>6. Inversión</h3>
    @php
        $inversionComercialT1 = $margenT1 * 0.30;
        $inversionComercialT2 = $margenT2 * 0.30;
        $inversionComercialT3 = $margenT3 * 0.30;
        $inversionComercialT4 = $margenT4 * 0.30;
        $inversionComercialAnual = $margenAnual * 0.30;

        $inversionMarketingT1 = $margenT1 * 0.10;
        $inversionMarketingT2 = $margenT2 * 0.10;
        $inversionMarketingT3 = $margenT3 * 0.10;
        $inversionMarketingT4 = $margenT4 * 0.10;  
        $inversionMarketingAnual = $margenAnual * 0.10;

        $inversionMarketingGeneralT1 = $margenT1 * 0.20;
        $inversionMarketingGeneralT2 = $margenT2 * 0.20;
        $inversionMarketingGeneralT3 = $margenT3 * 0.20;
        $inversionMarketingGeneralT4 = $margenT4 * 0.20;
        $inversionMarketingGeneralAnual = $margenAnual * 0.20;

        $inversionPatrocinioT1 = $margenT1 * 0.10;
        $inversionPatrocinioT2 = $margenT2 * 0.10;
        $inversionPatrocinioT3 = $margenT3 * 0.10;
        $inversionPatrocinioT4 = $margenT4 * 0.10;
        $inversionPatrocinioAnual = $margenAnual * 0.10;

        $inversionReservasT1 = 0;
        $inversionReservasT2 = 0;
        $inversionReservasT3 = 0;
        $inversionReservasT4 = 0;
        $inversionReservasAnual = 0;

        $totalInversionT1 = $inversionComercialT1 + $inversionMarketingT1 + $inversionPatrocinioT1 + $inversionReservasT1 + $inversionMarketingGeneralT1;
        $totalInversionT2 = $inversionComercialT2 + $inversionMarketingT2 + $inversionPatrocinioT2 + $inversionReservasT2 + $inversionMarketingGeneralT2;
        $totalInversionT3 = $inversionComercialT3 + $inversionMarketingT3 + $inversionPatrocinioT3 + $inversionReservasT3 + $inversionMarketingGeneralT3;
        $totalInversionT4 = $inversionComercialT4 + $inversionMarketingT4 + $inversionPatrocinioT4 + $inversionReservasT4 + $inversionMarketingGeneralT4;
        $totalInversionAnual = $inversionComercialAnual + $inversionMarketingAnual + $inversionPatrocinioAnual + $inversionReservasAnual + $inversionMarketingGeneralAnual;

        $totalBeneficioLibreT1 = $margenT1 - $totalInversionT1;
        $totalBeneficioLibreT2 = $margenT2 - $totalInversionT2;
        $totalBeneficioLibreT3 = $margenT3 - $totalInversionT3;
        $totalBeneficioLibreT4 = $margenT4 - $totalInversionT4;
        $totalBeneficioLibreAnual = $margenAnual - $totalInversionAnual;

        $descuentoComercialT1 = $totalPrecio1T != 0 ? ($inversionComercialT1 / $totalPrecio1T) * 100 : 0;
        $descuentoComercialT2 = $totalPrecio2T != 0 ? ($inversionComercialT2 / $totalPrecio2T) * 100 : 0;
        $descuentoComercialT3 = $totalPrecio3T != 0 ? ($inversionComercialT3 / $totalPrecio3T) * 100 : 0;
        $descuentoComercialT4 = $totalPrecio4T != 0 ? ($inversionComercialT4 / $totalPrecio4T) * 100 : 0;
        $descuentoComercialAnual = $totalPrecioAnual != 0 ? ($inversionComercialAnual / $totalPrecioAnual) * 100 : 0;
    @endphp
    <table class="table">
        <thead>
            <tr>
                <th></th>
                <th></th>
                <th>1 Trimestre</th>
                <th></th>
                <th>2 Trimestre</th>
                <th></th>
                <th>3 Trimestre</th>
                <th></th>
                <th>4 Trimestre</th>
                <th>Anual</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>1.Comercial</td>
                <td>30.00%</td>
                <td>{{number_format($inversionComercialT1, 2, ',', '.') }}€</td>
                <td>30.00%</td>
                <td>{{number_format($inversionComercialT2, 2, ',', '.') }}€</td>
                <td>30.00%</td>
                <td>{{number_format($inversionComercialT3, 2, ',', '.') }}€</td>
                <td>30.00%</td>
                <td>{{number_format($inversionComercialT4, 2, ',', '.') }}€</td>
                <td>{{number_format($inversionComercialAnual, 2, ',', '.') }}€</td>
            </tr>
            <tr>
                <td>2.Marketing Delegación</td>
                <td>10.00%</td>
                <td>{{number_format($inversionMarketingT1, 2, ',', '.') }}€</td>
                <td>10.00%</td>
                <td>{{number_format($inversionMarketingT2, 2, ',', '.') }}€</td>
                <td>10.00%</td>
                <td>{{number_format($inversionMarketingT3, 2, ',', '.') }}€</td>
                <td>10.00%</td>
                <td>{{number_format($inversionMarketingT4, 2, ',', '.') }}€</td>
                <td>{{number_format($inversionMarketingAnual, 2, ',', '.') }}€</td>
            </tr>
            <tr>
                <td>3.Marketing General</td>
                <td>20.00%</td>
                <td>{{number_format($inversionMarketingGeneralT1, 2, ',', '.') }}€</td>
                <td>20.00%</td>
                <td>{{number_format($inversionMarketingGeneralT2, 2, ',', '.') }}€</td>
                <td>20.00%</td>
                <td>{{number_format($inversionMarketingGeneralT3, 2, ',', '.') }}€</td>
                <td>20.00%</td>
                <td>{{number_format($inversionMarketingGeneralT4, 2, ',', '.') }}€</td>
                <td>{{number_format($inversionMarketingGeneralAnual, 2, ',', '.') }}€</td>
            </tr>
            <tr>
                <td>4.Patrocinio</td>
                <td>10.00%</td>
                <td>{{number_format($inversionPatrocinioT1, 2, ',', '.') }}€</td>
                <td>10.00%</td>
                <td>{{number_format($inversionPatrocinioT2, 2, ',', '.') }}€</td>
                <td>10.00%</td>
                <td>{{number_format($inversionPatrocinioT3, 2, ',', '.') }}€</td>
                <td>10.00%</td>
                <td>{{number_format($inversionPatrocinioT4, 2, ',', '.') }}€</td>
                <td>{{number_format($inversionPatrocinioAnual, 2, ',', '.') }}€</td>
            </tr>
            <tr class="bg-dark text-white">
                <td class="text-white" colspan="2">TOTAL PRESUPUESTO DE INVERSIÓN</td>
                <td class="text-white">{{number_format($totalInversionT1, 2, ',', '.') }}€</td>
                <td></td>
                <td class="text-white">{{number_format($totalInversionT2, 2, ',', '.') }}€</td>
                <td></td>
                <td class="text-white">{{number_format($totalInversionT3, 2, ',', '.') }}€</td>
                <td></td>
                <td class="text-white">{{number_format($totalInversionT4, 2, ',', '.') }}€</td>
                <td class="text-white">{{number_format($totalInversionAnual, 2, ',', '.') }}€</td>
            </tr>
            <tr class="bg-dark text-white">
                <td colspan="2" class="text-light">TOTAL BENEFICIO LIBRE</td>
                <td class="text-light">{{number_format($totalBeneficioLibreT1, 2, ',', '.') }}€</td>
                <td></td>
                <td class="text-light">{{number_format($totalBeneficioLibreT2, 2, ',', '.') }}€</td>
                <td></td>
                <td class="text-light">{{number_format($totalBeneficioLibreT3, 2, ',', '.') }}€</td>
                <td></td>
                <td class="text-light">{{number_format($totalBeneficioLibreT4, 2, ',', '.') }}€</td>
                <td class="text-light">{{number_format($totalBeneficioLibreAnual, 2, ',', '.') }}€</td>
            </tr>
            <tr class="bg-dark text-light">
                <td colspan="2" class="text-light">% DESCUENTO COMERCIAL</td>
                <td class="text-light">{{number_format($descuentoComercialT1, 2, ',', '.') }}%</td>
                <td></td>
                <td class="text-light">{{number_format($descuentoComercialT2, 2, ',', '.') }}%</td>
                <td></td>
                <td class="text-light">{{number_format($descuentoComercialT3, 2, ',', '.') }}%</td>
                <td></td>
                <td class="text-light">{{number_format($descuentoComercialT4, 2, ',', '.') }}%</td>
                <td class="text-light">{{number_format($descuentoComercialAnual, 2, ',', '.') }}%</td>
            </tr>
        </tbody>
    </table>
</body>
</html> 