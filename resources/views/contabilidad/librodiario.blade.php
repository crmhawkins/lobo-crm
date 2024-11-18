@extends('layouts.app')

@section('title', 'Contabilidad')

@section('content-principal')
<div class="container-principal">
    <h1>Libro Diario</h1>
    <table class="table table-bordered">
        <thead>
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
                <td>{{ number_format($paginatedTotalesPorCuenta->sum('Debe'), 2) }}</td>
                <td>{{ number_format($paginatedTotalesPorCuenta->sum('Haber'), 2) }}</td>
            </tr>
        </tfoot>
    </table>

    <!-- Enlaces de paginación -->
    <div class="pagination">
        {{ $paginatedTotalesPorCuenta->links() }}
    </div>
</div>
@endsection
