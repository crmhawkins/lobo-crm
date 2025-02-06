@extends('layouts.app')

@section('title', 'Contabilidad')

@section('content-principal')
<div class="container">
    <h1 class="my-4">Balance de Situación</h1>

    <!-- Formulario de filtros -->
    <form method="GET" action="{{ route('contabilidad.balanceSituacion') }}" class="mb-4">
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
                <td colspan="2" class="text-center">
                <h2>Activo</h2>
                </td>
                <td colspan="2" class="text-center">
                <h2>Pasivo</h2>
                </td>
            </tr>

            <tr>
                <td>
                    <h3>Activo No Corriente</h3>
                </td>
                <td></td>
                <td>
                    <h3>Patrimonio Neto</h3>
                </td>
                <td></td>
            </tr>


            <tr>
                <td>
                    INMOVILIZADO INTANGIBLE
                </td>
                <td> {{ $inmovilizadoIntangible }}</td>
                <td>Capital</td>
                <td> {{ $capital }} </td>
            </tr>


            <tr>
                <td>INMOVILIZADO MATERIAL</td>
                <td> {{ $inmovilizadoMaterial }}</td>
                <td>
                    Prima de emisión
                </td>
                <td> {{ $primaDeEmision }}</td>
            </tr>

            <tr>
                <td>
                    Inversiones inmobiliarias
                </td>
                <td> {{ $inversionesInmobiliarias }}</td>
                <td>

                    Reservas
                </td>
                <td> {{ $reservas }}</td>
            </tr>

            <tr>
                <td>
                    Inversiones a largo plazo
                </td>
                <td> {{ $inversionesLargoPlazo }}</td>
                <td>

                    Acciones y participaciones en patrimonio propias.
                </td>
                <td> {{ $accionesParticipaciones }}</td>


            </tr>

            <tr>
                <td>Inversiones financieras a largo plazo</td>
                <td> {{ $inversionesFinancierasLargoPlazo }}</td>
                <td>Resultados de ejercicios anteriores</td>
                <td> {{ $resultadosEjerciciosAnteriores }}</td>

            </tr>

            <tr>
                <td>
                    Activos por impuestos diferidos
                </td>
                <td> {{ $activosDiferidos }}</td>
                <td>

                    Otras aportaciones de los socios
                </td>

                <td> {{ $otrosAportacionesSocios }}</td>
            </tr>

            <tr>
                <td>
                   <h3>ACTIVO CORRIENTE</h3>
                </td>
                <td></td>
                <td>

                   Resultado del ejercicio
                </td>
                <td> {{ $resultadoEjercicio }}</td>
            </tr>
          
            <tr>
                <td>
                    Existencias
                </td>
                <td> {{ $existencias }}</td>
                <td>
                    Dividendo a cuenta
                </td>
                <td> {{ $dividendoACuenta }}</td>
            </tr>

            <tr>
                <td>
                    Deudores comerciales y otras cuentas
                </td>
                <td>{{ $deudores }}</td>
                <td>Subvenciones, donaciones y legados recibidos</td>
                <td> {{ $subvencionesDonacionesLegados }}</td>

            </tr>

            <tr>
                <td>
                    Inversiones financieras a corto plazo
                </td>
                <td>{{ $inversionesacortoPlazo }}</td>
                <td><h3>PASIVO NO CORRIENTE</h3></td>
                <td></td>
                
            </tr>


            <tr>
                <td>
                    Periodificaciones a corto plazo
                </td>
                <td>{{ $periodificacionesCortoPlazo }}</td>
                <td>Provisiones a largo plazo</td>
                <td> {{ $provisionesLargoPlazo }}</td>
            </tr>

            <tr>
                <td>
                    Efectivo y otros activos líquidos equivalentes
                </td>
                <td>{{ $efectivoOtrosActivos }}</td>
                <td>Deudas a largo plazo</td>
                <td> {{ $deudasLargoPlazo }}</td>

            </tr>
            <tr>
                <td>
                </td>
                <td></td>
                <td>Deudas con empresas del grupo y asociadas a largo plazo</td>
                <td>{{ $deudasConEmpresasGrupoAsociadasLargoPlazo }} </td>

            </tr>
            <tr>
                <td>
                </td>
                <td></td>
                <td>Pasivos por impuesto diferido</td>
                <td>{{ $pasivosImpuestoDiferido }} </td>


            </tr>

            <tr>
                <td>
                </td>
                <td></td>
                <td>Periodificaciones a largo plazo</td>
                <td>{{ $periodificacionesLargoPlazo }} </td>
            </tr>


            <tr>
                <td>
                </td>
                <td></td>
                <td>Provisiones a corto plazo</td>
                <td>{{ $provisionesCortoPlazo }} </td>
            </tr>

            <tr>
                <td>
                </td>
                <td></td>
                <td>Deudas a corto plazo</td>
                <td>{{ $deudasCortoPlazo }} </td>
            </tr>
            <tr>
                <td>
                </td>
                <td></td>
                <td>Deudas con empresas del grupo y asociadas a corto plazo</td>
                <td>{{ $deudasConEmpresasGrupoAsociadasCortoPlazo }} </td>
            </tr>

            <tr>
                <td>
                </td>
                <td></td>
                <td>Acreedores comerciales y otras cuentas a pagar</td>
                <td>{{ $acreedoresComercialesOtrasCuentas }} </td>
            </tr>
            <tr>
                <td>
                </td>
                <td></td>
                <td>Periodificaciones a corto plazo</td>
                <td>{{ $periodificacionesCortoPlazo2 }} </td>
            </tr>




            <tr>
                <td>
                    <h3>
                        TOTAL ACTIVO
                    </h3>
                </td>
                <td>{{ $totalActivo }}</td>
                <td>
                    <h3>
                        TOTAL PASIVO
                    </h3>
                </td>
                <td> {{ $totalPasivo }} </td>


            </tr>


        </table>


    </form>


</div>


@endsection
