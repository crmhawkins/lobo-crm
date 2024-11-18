@extends('layouts.app')

@section('title', 'Contabilidad')

@section('content-principal')
<div class="container">
    <h1 class="my-4">Libro Diario</h1>

    <!-- Formulario de filtros -->
    <form method="GET" action="{{ route('contabilidad.libroDiario') }}" class="mb-4">
        <div class="form-group">
            <label for="cuentaContable_id">Cuenta Contable:</label>
            <input type="text" name="cuentaContable_id" class="form-control" value="{{ request('cuentaContable_id') }}">
        </div>
        <div class="form-group">
            <label for="month">Mes:</label>
            <select name="month" class="form-control">
                <option value="">Todos</option>
                @php
                    $meses = [
                        1 => 'Enero', 2 => 'Febrero', 3 => 'Marzo', 4 => 'Abril',
                        5 => 'Mayo', 6 => 'Junio', 7 => 'Julio', 8 => 'Agosto',
                        9 => 'Septiembre', 10 => 'Octubre', 11 => 'Noviembre', 12 => 'Diciembre'
                    ];
                @endphp
                @foreach ($meses as $numero => $nombre)
                    <option value="{{ $numero }}" {{ request('month') == $numero ? 'selected' : '' }}>
                        {{ $nombre }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="form-group">
            <label for="year">Año:</label>
            <input type="number" name="year" class="form-control" value="{{ request('year', date('Y')) }}">
        </div>
        <button type="submit" class="btn btn-primary">Filtrar</button>
    </form>

    <table class="table table-bordered">
        <thead class="thead-dark">
            <tr>
                <th>COD. CUENTA</th>
                <th>CUENTA</th>
                <th>DEBE</th>
                <th>HABER</th>
            </tr>
        </thead>
        <tbody>
            @foreach($paginatedTotalesPorCuenta as $cuentaContable => $datos)
                <tr>
                    <td>{{ $cuentaContable }}</td>
                    <td>{{ $datos['nombre'] }}</td>
                    <td>{{ number_format($datos['Debe'], 2) }}</td>
                    <td>{{ number_format($datos['Haber'], 2) }}</td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <td colspan="2">Total</td>
                <td>{{ number_format($totalDebe, 2) }}</td>
                <td>{{ number_format($totalHaber, 2) }}</td>
            </tr>
        </tfoot>
    </table>

    <!-- Enlaces de paginación -->
    <div class="pagination justify-content-center">
        {{ $paginatedTotalesPorCuenta->links() }}
    </div>
</div>
@endsection
