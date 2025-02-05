@extends('layouts.app')

@section('title', 'Contabilidad')

@section('content-principal')
<div class="container">
    <h1 class="my-4">Perdidas y Ganancias</h1>

    <!-- Formulario de filtros -->
    <form method="GET" action="{{ route('contabilidad.perdidasYGanancias') }}" class="mb-4">
        <div class="row mb-3">
            <div class="col-md-3">
                <label for="year" class="form-label">Seleccionar año:</label>
                <select name="year" id="year" class="form-select" onchange="this.form.submit()">
                    @for($i = date('Y'); $i >= 2020; $i--)
                        <option value="{{ $i }}" {{ $i == $year ? 'selected' : '' }}>{{ $i }}</option>
                    @endfor
                </select>
            </div>
        </div>
        <table class="table table-bordered table-striped">
            <tr>
                <td>
                   {{$totalNegocios}}
                    
                </td>
                <td>
                    Importe neto de la cifra de negocios
                </td>
            </tr>
            <tr>
                <td>
                    {{$variacionProductos}}
                </td>
                <td>
                    Variación de existencias de productos terminados y en curso de fabricación
                </td>
            </tr>
            <tr>
                <td>
                    {{$trabajosRealizados}}
                </td>
                <td>
                    Trabajos realizados por la empresa para su activo
                </td>
            </tr>
            <tr>
                <td>
                    {{$aprovisionamientos}}
                </td>
                <td>
                    Aprovisionamientos
                </td>
            </tr>
            <tr>
                <td>
                    {{$ingresosExplotacion}}
                </td>
                <td>
                    Otros ingresos de explotación
                </td>
                

            </tr>
            <tr>
                <td>
                    {{$gastoPersonal}}
                </td>
                <td>
                    Gastos de personal
                </td>
            </tr>

            <tr>
                <td>
                    {{$otrosGastosExplotacion}}
                </td>
                <td>
                    Otros gastos de explotación
                </td>
            </tr>
            <tr>
                <td>
                    {{$inmovilizado}}
                </td>
                <td>
                    Amortización del inmovilizado
                </td>
            </tr>
            <tr>
                <td>
                    {{$subvencionesNoFinancieras}}
                </td>
                <td>
                    Imputación de subvenciones de inmovilizado no financiero y otras.
                </td>
            </tr>
            <tr>
                <td>
                    {{$excesodeProvisiones}}
                </td>
                <td>
                    Exceso de provisiones
                </td>
            </tr>
            <tr>
                <td>
                    {{$deterioroInmovilizado}}
                </td>
                <td>
                    Deterioro y resultado por enajenaciones del inmovilizado
                </td>
            </tr>
            <tr class="bg-primary text-white">
                <td>
                    {{$totalPrimero}}
                </td>
                <td>
                    RESULTADO DE EXPLOTACIÓN (1 + 2 + 3 + 4 + 5 + 6 + 7 + 8 + 9 + 10 + 11)	
                </td>
            </tr>
            <tr>
                <td>
                    {{$ingresosFinancieros}}
                </td>
                <td>
                    Ingresos financieros
                </td>
            </tr>
            <tr>
                <td>
                    {{$gastosFinancieros}}
                </td>
                <td>
                    Gastos financieros
                </td>
            </tr>
            <tr>
                <td>
                    {{$variacionInstrumentosFinancieros}}
                </td>
                <td>
                    Variación de valor razonable en instrumentos financieros
                </td>
            </tr>
            <tr>
                <td>
                    {{$diferenciasCambio}}
                </td>
                <td>
                    Diferencias de cambio
                </td>
            </tr>
            <tr>
                <td>
                    {{$deterioroEnajenaciones}}
                </td>
                <td>
                    Deterioro y resultado por enajenaciones de instrumentos financieros
                </td>
            </tr>
            <tr class="bg-primary text-white">
                <td>
                    {{$totalResultadosFinancieros}}
                </td>
                <td>
                    RESULTADO FINANCIERO (12 + 13 + 14 + 15 + 16)
                </td>
            </tr>
            <tr class="bg-success text-white">
                <td>
                    {{$totalPrimero + $totalResultadosFinancieros}}
                </td>
                <td>
                    RESULTADO ANTES DE IMPUESTOS (A + B)
                </td>
            </tr>
            <tr>
                <td>
                    {{$impuestosBeneficio}}
                </td>
                <td>
                    Impuestos sobre beneficios
                </td>
            </tr>
            <tr class="bg-success text-white">
                <td>
                    {{$ResultadoEjercicio}}
                </td>
                <td>
                    RESULTADO DEL EJERCICIO (C + 17)
                </td>
            </tr>
        </table>
    </form>

</div>








@endsection
