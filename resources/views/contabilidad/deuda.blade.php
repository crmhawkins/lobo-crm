@extends('layouts.app')

@section('title', 'Informe de Deuda')

@section('content-principal')
<link href="https://cdn.datatables.net/v/bs5/jq-3.7.0/jszip-3.10.1/dt-2.0.3/b-3.0.1/b-colvis-3.0.1/b-html5-3.0.1/b-print-3.0.1/r-3.0.1/datatables.min.css" rel="stylesheet">

<div class="container">
    <h1 class="my-4">Informe de Deuda al {{ $fecha }}</h1>

    <form method="GET" action="{{ route('contabilidad.deudaAFecha') }}" class="mb-4 d-flex align-items-end gap-3">
        <div>
            <label for="fecha">Fecha de corte:</label>
            <input type="date" name="fecha" value="{{ $fecha }}" class="form-control" required>
        </div>
        <div>
            <button type="submit" class="btn btn-primary">Filtrar</button>
        </div>
    </form>

    {{-- Clientes --}}
    <h4>Clientes con facturas impagadas</h4>
    <div class="table-responsive">
        <table id="tablaClientes" class=" table table-striped table-bordered dt-responsive nowrap table-sm">
            <thead>
                <tr>
                    <th>Cliente</th>
                    <th>Nº Facturas</th>
                    <th>Total Impagado</th>
                </tr>
            </thead>
            <tbody>
                @foreach($clientes as $cliente)
                    <tr>
                        <td>{{ $cliente->nombre }}</td>
                        <td>{{ $cliente->facturas_count }}</td>
                        <td>{{ number_format($cliente->total_impagado, 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    {{-- Proveedores --}}
    <h4 class="mt-5">Proveedores con gastos no pagados</h4>
    <div class="table-responsive">
        <table id="tablaProveedores" class="table table-striped table-bordered dt-responsive nowrap table-sm">
            <thead>
                <tr>
                    <th>Proveedor</th>
                    <th>Nº Gastos</th>
                    <th>Total Impagado</th>
                </tr>
            </thead>
            <tbody>
                @foreach($proveedores as $prov)
                    <tr>
                        <td>{{ $prov->proveedor_nombre }}</td>
                        <td>{{ $prov->gastos_count }}</td>
                        <td>{{ number_format($prov->total_impagado, 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection

@push('scripts')
@section('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>
<script src="https://cdn.datatables.net/v/bs5/jq-3.7.0/jszip-3.10.1/dt-2.0.3/b-3.0.1/b-colvis-3.0.1/b-html5-3.0.1/b-print-3.0.1/r-3.0.1/datatables.min.js"></script>

<script>
    $(document).ready(function () {
        $('#tablaClientes, #tablaProveedores').DataTable({
            responsive: true,
            pageLength: 25,
            dom: 'Bfrtip',
            buttons: [
                {
                    extend: 'excelHtml5',
                    title: 'Informe de Deuda'
                },
                {
                    extend: 'pdfHtml5',
                    title: 'Informe de Deuda'
                },
                {
                    extend: 'csvHtml5',
                    title: 'Informe de Deuda'
                },
                {
                    extend: 'print',
                    title: 'Informe de Deuda'
                }
            ],
            language: {
                url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json'
            }
        });
    });
</script>
@endsection
@endpush
