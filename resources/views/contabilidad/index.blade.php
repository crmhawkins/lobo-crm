@extends('layouts.app')

@section('title', 'Contabilidad')

@section('content-principal')

<div class="container-fluid">
    <h1>Libro Mayor</h1>
    {{-- {{$beneficio}} --}}

    <!-- Barra de filtros -->
    <form action="{{ route('contabilidad.index') }}" method="GET" class="mb-4">
        <div class="row bg-gray p-4 rounded shadow-sm" style="align-items: center;">
            
            <!-- Filtro por Cuenta Contable -->
            <div class="col-md-4 mb-3">
                <label for="cuentaContable_id" class="form-label">Cuenta contable</label>
                <select name="cuentaContable_id" class="form-control">
                    <option value="">--- Seleccione una cuenta contable ---</option>
                    
                    <!-- Hacer que los grupos contables sean seleccionables -->
                    @foreach($cuentasContables as $grupo)
                        <option value="{{ $grupo['grupo']->numero }}"
                            {{ request('cuentaContable_id') == "{$grupo['grupo']->numero}" ? 'selected' : '' }}>
                            {{ $grupo['grupo']->numero }}. {{ $grupo['grupo']->nombre }}
                        </option>
            
                        <!-- Hacer que los subgrupos contables sean seleccionables -->
                        @foreach($grupo['subGrupo'] as $subGrupo)
                            <option value="{{ $subGrupo['item']->numero }}"
                                {{ request('cuentaContable_id') == "{$subGrupo['item']->numero}" ? 'selected' : '' }}>
                                &nbsp;&nbsp;&nbsp;{{ $subGrupo['item']->numero }}. {{ $subGrupo['item']->nombre }}
                            </option>
            
                            <!-- Listar cuentas y subcuentas -->
                            @foreach($subGrupo['cuentas'] as $cuenta)
                                <option value="{{ $cuenta['item']['numero'] }}"
                                    {{ request('cuentaContable_id') == $cuenta['item']['numero'] ? 'selected' : '' }}>
                                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{{ $cuenta['item']['numero'] }}. {{ $cuenta['item']['nombre'] }}
                                </option>
                                @foreach($cuenta['subCuentas'] as $subCuenta)
                                    <option value="{{ $subCuenta['item']['numero'] }}"
                                        {{ request('cuentaContable_id') == $subCuenta['item']['numero'] ? 'selected' : '' }}>
                                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{{ $subCuenta['item']['numero'] }}. {{ $subCuenta['item']['nombre'] }}
                                    </option>
                                    @foreach($subCuenta['subCuentasHija'] as $subCuentaHija)
                                        <option value="{{ $subCuentaHija->numero }}"
                                            {{ request('cuentaContable_id') == $subCuentaHija->numero ? 'selected' : '' }}>
                                            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{{ $subCuentaHija->numero }}. {{ $subCuentaHija->nombre }}
                                        </option>
                                    @endforeach
                                @endforeach
                            @endforeach
                        @endforeach
                    @endforeach
                </select>
            </div>

            <!-- Filtro por Fecha Desde -->
            <div class="col-md-3 mb-3">
                <label for="fecha_desde" class="form-label">Fecha Desde</label>
                <input type="date" id="fecha_desde" name="fecha_desde" class="form-control" value="{{ request('fecha_desde') }}">
            </div>

            <!-- Filtro por Fecha Hasta -->
            <div class="col-md-3 mb-3">
                <label for="fecha_hasta" class="form-label">Fecha Hasta</label>
                <input type="date" id="fecha_hasta" name="fecha_hasta" class="form-control" value="{{ request('fecha_hasta') }}">
            </div>

            <!-- Seleccionar número de transacciones por página -->
            <div class="col-md-3 mb-3">
                <label for="per_page" class="form-label">Transacciones por página</label>
                <select name="per_page" class="form-control">
                    <option value="10" {{ request('per_page') == 10 ? 'selected' : '' }}>10</option>
                    <option value="25" {{ request('per_page') == 25 ? 'selected' : '' }}>25</option>
                    <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50</option>
                    <option value="100" {{ request('per_page') == 100 ? 'selected' : '' }}>100</option>
                </select>
            </div>

            <!-- Botón de filtro -->
            <div class="col-md-3 mt-4 d-flex justify-content-start">
                <button type="submit" class="btn btn-primary">Filtrar</button>
            </div>
            <div class="col-md-4">
                <h5>Contabilidad</h5>
                <!-- Dropdown Button -->
                <div class="dropdown">
                    <button class="btn btn-primary w-100 dropdown-toggle" type="button" id="dropdownContabilidad" data-bs-toggle="dropdown" aria-expanded="false">
                        Opciones de Contabilidad
                    </button>
                    <ul class="dropdown-menu w-100" aria-labelledby="dropdownContabilidad">
                        <li><a class="dropdown-item" href="{{ route('admin.cuentasContables.index') }}">Cuentas Contables</a></li>
                        <li><a class="dropdown-item" href="{{ route('admin.subCuentasContables.index') }}">Sub-Cuentas Contables</a></li>
                        <li><a class="dropdown-item" href="{{ route('admin.subCuentasHijaContables.index') }}">Sub-Cuentas Hijas</a></li>
                        <li><a class="dropdown-item" href="{{ route('admin.grupoContabilidad.index') }}">Grupos Contables</a></li>
                        <li><a class="dropdown-item" href="{{ route('admin.subGrupoContabilidad.index') }}">Sub-Grupos Contables</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </form>

    <div class="d-flex justify-content-center mt-4 mb-5">
        {{ $cajas->appends(request()->query())->appends(['saldo_acumulado' => $saldoAcumulado])->links() }}
    </div>
    
    <!-- Mostrar el saldo acumulado al principio de la tabla -->
    <div class="mt-4 mb-3">
        <h4>Saldo acumulado hasta ahora: {{ number_format($saldoAcumulado, 2) }} €</h4>
    </div>
    
    <table class="table table-striped table-bordered dt-responsive nowrap">
        <thead>
            <tr>
                <th>Fecha</th>
                <th>Descripción</th>
                <th>Debe</th>
                <th>Haber</th>
                <th>Saldo</th>
                <th>Proveedor/Cliente</th>
                <th>Número de Cuenta Contable</th>
            </tr>
        </thead>
        <tbody>
            @php
    // Iniciar el saldo con el saldo acumulado hasta antes de la página actual
                $saldo = $saldoAcumulado ?? 0;
            @endphp

            @foreach($cajas as $caja)
                @php
                    // Ajusta el saldo según el tipo de movimiento
                    if ($caja->tipo_movimiento == 'Ingreso') {
                        $saldo += $caja->importe; // Debe (Ingreso)
                    } else {
                        $saldo -= $caja->importe; // Haber (Gasto)
                    }
                @endphp
                <tr>
                    <td>{{ $caja->fecha }}</td>
                    <td>{{ $caja->descripcion }}</td>
                    <td>{{ $caja->tipo_movimiento == 'Ingreso' ? number_format($caja->importe, 2) : '' }}</td>
                    <td>{{ $caja->tipo_movimiento == 'Gasto' ? number_format($caja->total, 2) : '' }}</td>
                    <td>{{ number_format($saldo, 2) }}</td>
                    <td>
                        @if($caja->proveedor)
                            {{ $caja->proveedor->nombre }}
                        @elseif( $caja->gasto_id)
                            {{ $caja->gasto->proveedor->nombre }}
                        @else
                            {{ $caja->facturas->first()->cliente->nombre ?? '' }}
                        @endif
                    </td>
                    <td>
                        @if($caja->proveedor && $caja->proveedor->cuentaContable)
                            {{ $caja->proveedor->cuentaContable->numero }}
                        @elseif($caja->gasto_id && $caja->gasto->proveedor && $caja->gasto->proveedor->cuentaContable)
                            {{ $caja->gasto->proveedor->cuentaContable->numero }}
                        @elseif($caja->facturas->first() && $caja->facturas->first()->cliente->cuentaContable)
                            {{ $caja->facturas->first()->cliente->cuentaContable->numero }}
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <!-- Paginación -->
    <div class="d-flex justify-content-center mt-4 mb-5">
        {{ $cajas->appends(request()->query())->links() }}
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

@endsection
