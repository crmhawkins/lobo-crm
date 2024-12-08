@extends('layouts.app')

@section('title', 'Proyeccion presupuestaria')

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
    </style>
    {{-- <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet"> --}}

@endsection

@section('content-principal')
<div class="container" style="min-height: 100vh;">
    <h2 class="text-center mb-4 d-flex justify-content-center gap-2 align-items-center">Presupuesto por Delegación @if($delegacion)<span style="font-size: 22px;" class="badge badge-secondary text-center">{{$delegacion->nombre}}</span> @endif <span style="font-size: 22px"  class="badge badge-primary">{{ $year }}</span></h2>
    <button onclick="exportarTablasAExcel()" class="btn btn-success mb-4">Exportar a Excel</button>
    @if($delegacion)
        <a href="{{ route('exportarProyeccionAPDF', ['delegacion' => $delegacion->id, 'year' => $year]) }}" class="btn btn-success mb-4">Exportar a PDF</a>
    @endif

    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="card shadow-sm mb-4">
                    <div class="card-body text-center d-flex gap-2 justify-content-center">
        
                        <!-- Botón desplegable para las delegaciones -->
                        <div class="dropdown mb-4">
                            <button class="btn btn-outline-primary dropdown-toggle" type="button" id="dropdownDelegaciones" data-bs-toggle="dropdown" aria-expanded="false">
                                Seleccione una Delegación
                            </button>
                            <ul class="dropdown-menu" aria-labelledby="dropdownDelegaciones">
                                @foreach($delegaciones as $delegacion2)
                                    <li>
                                        <a class="dropdown-item" href="{{ route('control-presupuestario.proyeccion', ['delegacion' => $delegacion2->id, 'year' => $year]) }}">
                                            {{ $delegacion2->nombre }}
                                        </a>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                        @if($delegacion)
                            <!-- Selector de año -->
                            <form method="GET" action="{{ route('control-presupuestario.proyeccion', ['delegacion' => $delegacion->id]) }}" style="display: flex; gap: 10px; align-items: start;">
                                <input type="hidden" name="delegacion" value="{{ $delegacion->id }}">
                                <div class="input-group justify-content-center">
                                    <label for="year" class="input-group-text bg-primary text-white">Año:</label>
                                    <select name="year" id="year" class="form-select" style="max-width: 150px;" onchange="this.form.submit()">
                                        @for($i = 2020; $i <= \Carbon\Carbon::now()->year; $i++)
                                            <option value="{{ $i }}" {{ $i == $year ? 'selected' : '' }}>{{ $i }}</option>
                                        @endfor
                                    </select>
                                </div>
                                <div class="input-group justify-content-center">
                                    <label for="porcentaje" class="input-group-text bg-primary text-white">Porcentaje:</label>
                                    <input type="number" name="porcentaje" id="porcentaje" class="form-select" style="max-width: 150px;" value="{{ $porcentaje }}" onchange="this.form.submit()">
                                </div>
                            </form>
                        @endif
                    </div>
                    <div class="d-flex justify-content-center gap-2">
                        @if($delegacion)
                            <button class="btn btn-primary mb-4" type="button" data-toggle="collapse" data-target="#tablaPresupuesto" aria-expanded="false" aria-controls="tablaPresupuesto">
                                Tabla de Ventas
                            </button>
                            <button class="btn btn-primary mb-4" type="button" data-toggle="collapse" data-target="#tablaCompras" aria-expanded="false" aria-controls="tablaCompras">
                                Tabla de Compras
                            </button>
                            <button class="btn btn-primary mb-4" type="button" data-toggle="collapse" data-target="#tablaBeneficio" aria-expanded="false" aria-controls="tablaBeneficio">
                                Tabla de Beneficio
                            </button>
                            <button class="btn btn-primary mb-4" type="button" data-toggle="collapse" data-target="#tablaGastos" aria-expanded="false" aria-controls="tablaGastos">
                                Tabla de Gastos
                            </button>
                            <button class="btn btn-primary mb-4" type="button" data-toggle="collapse" data-target="#tablaMargen" aria-expanded="false" aria-controls="tablaMargen">
                                Tabla de Margen
                            </button>
                            <button class="btn btn-primary mb-4" type="button" data-toggle="collapse" data-target="#tablaInversion" aria-expanded="false" aria-controls="tablaInversion">
                                Tabla de Inversión
                            </button>
                        @endif
                    </div>
                    
                </div>
            </div>
        </div>


    </div>
    @if($delegacion)
        
                                                
        <!-- Tabla desplegable -->
        <div class="collapse" id="tablaPresupuesto">
            <h2>1. Presupuesto de ventas</h2>
            <table class="table table-bordered table-striped table-hover mb-5">
                <thead class="thead-dark">
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
                        <th>Total Botellas Anual</th> <!-- Nueva columna para el total de botellas -->
                        <th>Anual</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        // Creamos un array para almacenar los productos únicos y los totales por trimestre
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
                                    // Inicializamos el array del producto si no existe
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
            
                                    // Sumar las unidades y costes de todas las delegaciones para este producto
                                    foreach ($data['ventasDelegaciones'] as $delegacion => $ventas) {
                                        // Sumar los valores del primer trimestre
                                        if (isset($presupuestosPorTrimestre[1][$mes][$producto]['ventasDelegaciones'][$delegacion])) {
                                            $productosAgrupados[$producto]['unidades1T'] += $presupuestosPorTrimestre[1][$mes][$producto]['ventasDelegaciones'][$delegacion]['unidadesVendidas'];
                                            $productosAgrupados[$producto]['coste1T'] += $presupuestosPorTrimestre[1][$mes][$producto]['ventasDelegaciones'][$delegacion]['precioTotal'];
                                        }
            
                                        // Sumar los valores del segundo trimestre
                                        if (isset($presupuestosPorTrimestre[2][$mes][$producto]['ventasDelegaciones'][$delegacion])) {
                                            $productosAgrupados[$producto]['unidades2T'] += $presupuestosPorTrimestre[2][$mes][$producto]['ventasDelegaciones'][$delegacion]['unidadesVendidas'];
                                            $productosAgrupados[$producto]['coste2T'] += $presupuestosPorTrimestre[2][$mes][$producto]['ventasDelegaciones'][$delegacion]['precioTotal'];
                                        }
            
                                        // Sumar los valores del tercer trimestre
                                        if (isset($presupuestosPorTrimestre[3][$mes][$producto]['ventasDelegaciones'][$delegacion])) {
                                            $productosAgrupados[$producto]['unidades3T'] += $presupuestosPorTrimestre[3][$mes][$producto]['ventasDelegaciones'][$delegacion]['unidadesVendidas'];
                                            $productosAgrupados[$producto]['coste3T'] += $presupuestosPorTrimestre[3][$mes][$producto]['ventasDelegaciones'][$delegacion]['precioTotal'];
                                        }
            
                                        // Sumar los valores del cuarto trimestre
                                        if (isset($presupuestosPorTrimestre[4][$mes][$producto]['ventasDelegaciones'][$delegacion])) {
                                            $productosAgrupados[$producto]['unidades4T'] += $presupuestosPorTrimestre[4][$mes][$producto]['ventasDelegaciones'][$delegacion]['unidadesVendidas'];
                                            $productosAgrupados[$producto]['coste4T'] += $presupuestosPorTrimestre[4][$mes][$producto]['ventasDelegaciones'][$delegacion]['precioTotal'];
                                        }
                                    }
                                @endphp
                            @endforeach
                        @endforeach
                    @endforeach
            
                    {{-- Mostrar los productos agrupados y sumar las columnas para evitar la repetición --}}
                    @foreach ($productosAgrupados as $producto => $totales)
                        @php
                            // Sumar el coste total anual
                            $costeAnual = $totales['coste1T'] + $totales['coste2T'] + $totales['coste3T'] + $totales['coste4T'];
            
                            // Sumar el total de botellas anual
                            $totalBotellasAnual = $totales['unidades1T'] + $totales['unidades2T'] + $totales['unidades3T'] + $totales['unidades4T'];
            
                            // Sumar a los totales generales
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
                            <td>{{ $totalBotellasAnual }}</td> <!-- Mostrar el total de botellas anual -->
                            <td><strong>{{ number_format($costeAnual, 2, ',', '.') }} €</strong></td>
                        </tr>
                    @endforeach
            
                    {{-- Fila de totales generales --}}
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
                        <td>{{ $totalUnidadesAnual }}</td> <!-- Total de botellas anual -->
                        <td><strong>{{ number_format($totalPrecioAnual, 2, ',', '.') }} €</strong></td>
                    </tr>
                </tbody>
            </table>
            
        </div>


        <div class="collapse" id="tablaCompras">
            <h2>2. Presupuesto de compras</h2>

            <table class="table table-bordered table-striped table-hover mb-5">
                <thead class="thead-dark">
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
                        <th>Total Botellas Anual</th> <!-- Nueva columna para el total de botellas -->
                        <th>Anual</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        // Creamos un array para almacenar los productos únicos y los totales por trimestre
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
                                    // Inicializamos el array del producto si no existe
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
            
                                    // Sumar las unidades y costes de todas las delegaciones para este producto
                                    foreach ($data['ventasDelegaciones'] as $delegacion => $ventas) {
                                        // Sumar los valores del primer trimestre
                                        if (isset($presupuestosPorTrimestre[1][$mes][$producto]['ventasDelegaciones'][$delegacion])) {
                                            $productosAgrupados[$producto]['unidades1T'] += $presupuestosPorTrimestre[1][$mes][$producto]['ventasDelegaciones'][$delegacion]['unidadesVendidas'];
                                            $productosAgrupados[$producto]['coste1T'] += $presupuestosPorTrimestre[1][$mes][$producto]['ventasDelegaciones'][$delegacion]['costeTotal'];
                                        }
            
                                        // Sumar los valores del segundo trimestre
                                        if (isset($presupuestosPorTrimestre[2][$mes][$producto]['ventasDelegaciones'][$delegacion])) {
                                            $productosAgrupados[$producto]['unidades2T'] += $presupuestosPorTrimestre[2][$mes][$producto]['ventasDelegaciones'][$delegacion]['unidadesVendidas'];
                                            $productosAgrupados[$producto]['coste2T'] += $presupuestosPorTrimestre[2][$mes][$producto]['ventasDelegaciones'][$delegacion]['costeTotal'];
                                        }
            
                                        // Sumar los valores del tercer trimestre
                                        if (isset($presupuestosPorTrimestre[3][$mes][$producto]['ventasDelegaciones'][$delegacion])) {
                                            $productosAgrupados[$producto]['unidades3T'] += $presupuestosPorTrimestre[3][$mes][$producto]['ventasDelegaciones'][$delegacion]['unidadesVendidas'];
                                            $productosAgrupados[$producto]['coste3T'] += $presupuestosPorTrimestre[3][$mes][$producto]['ventasDelegaciones'][$delegacion]['costeTotal'];
                                        }
            
                                        // Sumar los valores del cuarto trimestre
                                        if (isset($presupuestosPorTrimestre[4][$mes][$producto]['ventasDelegaciones'][$delegacion])) {
                                            $productosAgrupados[$producto]['unidades4T'] += $presupuestosPorTrimestre[4][$mes][$producto]['ventasDelegaciones'][$delegacion]['unidadesVendidas'];
                                            $productosAgrupados[$producto]['coste4T'] += $presupuestosPorTrimestre[4][$mes][$producto]['ventasDelegaciones'][$delegacion]['costeTotal'];
                                        }
                                    }
                                @endphp
                            @endforeach
                        @endforeach
                    @endforeach
            
                    {{-- Mostrar los productos agrupados y sumar las columnas para evitar la repetición --}}
                    @foreach ($productosAgrupados as $producto => $totales)
                        @php
                            // Sumar el coste total anual
                            $costeAnual = $totales['coste1T'] + $totales['coste2T'] + $totales['coste3T'] + $totales['coste4T'];
            
                            // Sumar el total de botellas anual
                            $totalBotellasAnual = $totales['unidades1T'] + $totales['unidades2T'] + $totales['unidades3T'] + $totales['unidades4T'];
            
                            // Sumar a los totales generales
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
                            <td>{{ $totalBotellasAnual }}</td> <!-- Mostrar el total de botellas anual -->
                            <td><strong>{{ number_format($costeAnual, 2, ',', '.') }} €</strong></td>
                        </tr>
                    @endforeach
            
                    {{-- Fila de totales generales --}}
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
                        <td>{{ $totalUnidadesAnual }}</td> <!-- Total de botellas anual -->
                        <td><strong>{{ number_format($totalCosteAnual, 2, ',', '.') }} €</strong></td>
                    </tr>
                </tbody>
            </table>
            
        </div>
        <div class="mt-4 collapse" id='tablaBeneficio'>
            <h2>3. Margen de Beneficio</h2>
            <table class="table table-bordered table-striped table-hover mb-5">
                <thead class="thead-dark">
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
                    <!-- Margen de beneficio real -->
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

                    <!-- Margen de beneficio presupuestado -->
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
        </div>
        <div class="mt-4 collapse" id='tablaGastos'>
            <h2 class="text-center mb-4">Presupuesto de Gastos  {{ $year }}</h2>

            <table class="table table-bordered table-striped table-hover mb-5">
                <thead class="thead-dark">
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
                    <!-- Gastos Estructurales -->
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
            
                    <!-- Total Gastos Estructurales -->
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
        
        </div>
        <div class="mt-4 collapse" id='tablaMargen'>
            <h2 class="text-center mb-4">Margen {{ $year }}</h2>
            @php
                $margenT1 = $margenBeneficioReal['1T'] - $totalT1;
                $margenT2 = $margenBeneficioReal['2T'] - $totalT2;
                $margenT3 = $margenBeneficioReal['3T'] - $totalT3;
                $margenT4 = $margenBeneficioReal['4T'] - $totalT4;
                $margenAnual = $margenBeneficioReal['anual'] - $totalAnual;
            @endphp
            <table class="table table-bordered table-striped table-hover mb-5">
                <thead class="thead-dark">
                    <th></th>
                    <th>1 Trimestre</th>
                    <th>2 Trimestre</th>
                    <th>3 Trimestre</th>
                    <th>4 Trimestre</th>
                    <th>Anual</th>
                </thead>
                <tbody>
                    <td>MARGEN PRESUPUESTO</td>
                    <td>{{ number_format($margenT1 , 2, ',', '.') }}€</td>
                    <td>{{ number_format($margenT2 , 2, ',', '.') }}€</td>
                    <td>{{ number_format($margenT3 , 2, ',', '.') }}€</td>
                    <td>{{ number_format($margenT4 , 2, ',', '.') }}€</td>
                    <td>{{ number_format($margenAnual , 2, ',', '.') }}€</td>



                </tbody>
            </table>
        </div>

        <div class="mt-4 collapse" id="tablaInversion">
            <h2 class="text-center mb-4">Inversion {{ $year }}</h2>
            @php
                $inversionComercialT1 = $margenT1 * 0.30;
                $inversionComercialT2 = $margenT2 * 0.30;
                $inversionComercialT3 = $margenT3 * 0.30;
                $inversionComercialT4 = $margenT4 * 0.30;
                $inversionComercialAnual = $margenAnual * 0.30;
                //-----------//
                $inversionMarketingT1 = $margenT1 * 0.10;
                $inversionMarketingT2 = $margenT2 * 0.10;
                $inversionMarketingT3 = $margenT3 * 0.10;
                $inversionMarketingT4 = $margenT4 * 0.10;  
                $inversionMarketingAnual = $margenAnual * 0.10;

                //-----------//

                $inversionMarketingGeneralT1 = $margenT1 * 0.20;
                $inversionMarketingGeneralT2 = $margenT2 * 0.20;
                $inversionMarketingGeneralT3 = $margenT3 * 0.20;
                $inversionMarketingGeneralT4 = $margenT4 * 0.20;
                $inversionMarketingGeneralAnual = $margenAnual * 0.20;
                //-----------//
                $inversionPatrocinioT1 = $margenT1 * 0.10;
                $inversionPatrocinioT2 = $margenT2 * 0.10;
                $inversionPatrocinioT3 = $margenT3 * 0.10;
                $inversionPatrocinioT4 = $margenT4 * 0.10;
                $inversionPatrocinioAnual = $margenAnual * 0.10;
                //-----------//
                // $inversionReservasT1 = $margenT1 * 0.02;
                // $inversionReservasT2 = $margenT2 * 0.02;
                // $inversionReservasT3 = $margenT3 * 0.02;
                // $inversionReservasT4 = $margenT4 * 0.02;
                // $inversionReservasAnual = $margenAnual * 0.02;
                $inversionReservasT1 = 0;
                $inversionReservasT2 = 0;
                $inversionReservasT3 = 0;
                $inversionReservasT4 = 0;
                $inversionReservasAnual = 0;

                //-----------//
                $totalInversionT1 = $inversionComercialT1 + $inversionMarketingT1 + $inversionPatrocinioT1 + $inversionReservasT1 + $inversionMarketingGeneralT1;
                $totalInversionT2 = $inversionComercialT2 + $inversionMarketingT2 + $inversionPatrocinioT2 + $inversionReservasT2 + $inversionMarketingGeneralT2;
                $totalInversionT3 = $inversionComercialT3 + $inversionMarketingT3 + $inversionPatrocinioT3 + $inversionReservasT3 + $inversionMarketingGeneralT3;
                $totalInversionT4 = $inversionComercialT4 + $inversionMarketingT4 + $inversionPatrocinioT4 + $inversionReservasT4 + $inversionMarketingGeneralT4;
                $totalInversionAnual = $inversionComercialAnual + $inversionMarketingAnual + $inversionPatrocinioAnual + $inversionReservasAnual + $inversionMarketingGeneralAnual;

                //---------//

                $totalBeneficioLibreT1 = $margenT1 - $totalInversionT1;
                $totalBeneficioLibreT2 = $margenT2 - $totalInversionT2;
                $totalBeneficioLibreT3 = $margenT3 - $totalInversionT3;
                $totalBeneficioLibreT4 = $margenT4 - $totalInversionT4;
                $totalBeneficioLibreAnual = $margenAnual - $totalInversionAnual;

                //---------//
                $descuentoComercialT1 = $totalPrecio1T != 0 ? ($inversionComercialT1 / $totalPrecio1T) * 100 : 0;
                $descuentoComercialT2 = $totalPrecio2T != 0 ? ($inversionComercialT2 / $totalPrecio2T) * 100 : 0;
                $descuentoComercialT3 = $totalPrecio3T != 0 ? ($inversionComercialT3 / $totalPrecio3T) * 100 : 0;
                $descuentoComercialT4 = $totalPrecio4T != 0 ? ($inversionComercialT4 / $totalPrecio4T) * 100 : 0;
                $descuentoComercialAnual = $totalPrecioAnual != 0 ? ($inversionComercialAnual / $totalPrecioAnual) * 100 : 0;



            @endphp
            <table class="table table-bordered table-striped  mb-5">
                <thead class="thead-dark">
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
                    {{-- <tr>
                        <td>5.Reservas</td>
                        <td>2.00%</td>
                        <td>{{number_format($inversionReservasT1, 2, ',', '.') }}€</td>
                        <td>2.00%</td>
                        <td>{{number_format($inversionReservasT2, 2, ',', '.') }}€</td>
                        <td>2.00%</td>
                        <td>{{number_format($inversionReservasT3, 2, ',', '.') }}€</td>
                        <td>2.00%</td>
                        <td>{{number_format($inversionReservasT4, 2, ',', '.') }}€</td>
                        <td>{{number_format($inversionReservasAnual, 2, ',', '.') }}€</td>
                    </tr> --}}
                    <tr class="bg-dark  text-white">
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
                    <td>
                        <td colspan="2"></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                    </td>
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

            <table class="table table-bordered table-striped  mb-5">
                <tbody>
                   
                </tbody>
            </table>

        </div>
   
    @endif
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
   <script>
    function exportarTablasAExcel() {
        // Crear un nuevo libro de trabajo
        var wb = XLSX.utils.book_new();
        var tablesFound = false; // Variable para verificar si se encontraron tablas
        var sheetNames = new Set(); // Conjunto para almacenar nombres de hojas únicos

        // Seleccionar todas las tablas dentro del contenedor principal
        document.querySelectorAll('.container .table').forEach((table, index) => {
            // Obtener el nombre de la tabla desde el h2 anterior
            var tableNameElement = table.closest('div.collapse').querySelector('h2');
            var baseTableName = tableNameElement ? tableNameElement.textContent.trim() : 'Tabla ' + (index + 1);

            // Limpiar el nombre de la hoja eliminando caracteres no permitidos
            baseTableName = baseTableName.replace(/[:\\\/?*\[\]]/g, '');

            // Truncar el nombre de la hoja si es necesario
            if (baseTableName.length > 31) {
                baseTableName = baseTableName.substring(0, 28) + '...';
            }

            // Asegurar que el nombre de la hoja sea único
            var tableName = baseTableName;
            var suffix = 1;
            while (sheetNames.has(tableName)) {
                tableName = baseTableName + ' (' + suffix + ')';
                suffix++;
            }
            sheetNames.add(tableName);

            // Convertir la tabla HTML a una hoja de cálculo
            var ws = XLSX.utils.table_to_sheet(table);
            // Añadir la hoja de cálculo al libro de trabajo
            XLSX.utils.book_append_sheet(wb, ws, tableName);
            tablesFound = true; // Indicar que se encontró al menos una tabla
        });

        // Verificar si se encontraron tablas antes de intentar exportar
        if (tablesFound) {
            // Exportar el libro de trabajo a un archivo Excel
            XLSX.writeFile(wb, 'control_presupuestario_logistica.xlsx');
        } else {
            alert('No se encontraron tablas para exportar.');
        }
    }
</script>
    
@endsection
