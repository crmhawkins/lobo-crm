<!DOCTYPE html>
<html lang="en">

<head>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 10px;
        }

        .container {
            width: 100%;
            padding: 15px;
        }

        .header {
            text-align: center;
            margin-bottom: 30px;
            font-size: 1rem;

        }

        .row {
            clear: both;
            margin-top: 10px;
            margin-left: auto;
        }

        .row .label {
            float: right;
            width: 25%;
            font-weight: bold;
        }

        .row .value {
            float: right;
            width: 75%;
        }

        .signature {
            margin-top: 50px;
        }

        .signature img {
            max-width: 200px;
        }

        .table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            page-break-inside: avoid;
        }

        .table th {
            background-color: #3db1f3;
            text-align: center;
        }

        .table th,
        .table td {
            border: 1px solid #000;
            padding: 5px;
            text-align: left;
        }

        .i .totals {
            float: right;
            width: 30%;
            margin-top: 20px;
            border-collapse: collapse;
        }

        .totals th,
        .totals td {
            border: 1px solid #000;
            padding: 5px;
            text-align: left;
        }
    </style>
</head>

<body>
    <div class="container">
        <div style="text-align: right !important">
            <div class="row" style="text-align: right !important">
                <div class="label"><b>EDUCACIÓN, OCIO Y TIEMPO LIBRE LA FABRICA S.L.</b></b></div>
            </div>
            <div class="row" style="text-align: right !important">
                <div class="value">B-11949658</div>
            </div>
            <div class="row" style="text-align: right !important">
                <div class="value">Avd. Alcalde Cantos Ropero, 51 Pol. Ind. "Jerez 2000" Nave 14</div>
            </div>
            <div class="row" style="text-align: right !important">
                <div class="value">11408 Jerez de la Frontera ( Cádiz)</div>
            </div>
            <div class="row" style="text-align: right !important">
                <div class="value">956 042 751 &nbsp; &nbsp; &nbsp; &nbsp; 673 811 838</div>
            </div>
        </div>
        <br>
        <br>
        <br>
        <br>
        <img src="{{ public_path('assets/images/logo_factura.png') }}" alt="Firma del Cliente" style="display: float; float: right; height:10%">

        <table class="table" style="width: 60% !important">
            <thead>
                <tr width="100%">
                    <th>Presupuesto</th>
                    <th>Fecha</th>
                </tr>
            </thead>
            <tbody>
                <tr width="100%">
                    <td width="50%"> <b>{{ $id_presupuesto }}</b></td>
                    <td width="50%">{{ $fechaEmision }} </td>
                </tr>
                <tr width="100%">
                    <th>Creado por</th>
                    <th>Valido hasta</th>
                </tr>
                <tr width="100%">
                    <td width="50%"> <b>{{ $id_presupuesto }}</b></td>
                    <td width="50%">{{ $fechaVencimiento }} </td>
                </tr>
            </tbody>
        </table>

        <table class="table">
            <thead>
                <tr width="100%">
                    <th>Cliente</th>
                    <th colspan="2">Dirección</th>
                    <th>CIF</th>
                </tr>
            </thead>
            <tbody>
                <tr width="100%">
                    <td> {{ $cliente->nombre }} {{ $cliente->apellido }}
                    </td>
                    <td>
                        {{ $cliente->tipoCalle }} {{ $cliente->calle }} , {{ $cliente->numero }}
                    </td>
                    <td>{{ $cliente->nif }}</td>
                </tr>
                <tr width="100%">
                    <th>Contacto</th>
                    <th>Email</th>
                    <th colspan="2">Telefono</th>
                </tr>
                <tr width="100%">
                    <td>{{ $evento->eventoContacto }}
                    </td>
                    <td>{{ $evento->eventoEmail }}</td>
                    <td>{{ $evento->eventoTelefono }}</td>
                </tr>
                <tr width="100%">
                    <th>DATOS DEL EVENTO</th>
                    <td colspan="3">{{ $evento->eventoNombre }}</th>
                </tr>
                <tr width="100%">
                    <th>Lugar</th>
                    <th>Fecha</th>
                    <th>Contacto</th>
                    <th>Horario</th>
                </tr>
                <tr width="100%">
                    <td></td>
                    <td>{{$evento->eventoFecha}}</td>
                    <td></td>
                    <td></td>
                </tr>
            </tbody>
        </table>
        @if (!empty($listaPacks)) <table class="table">
            <thead>
                <tr width="100%">
                    <th colspan="4"></th>
                </tr>
            </thead>
            <tbody>
                <tr width="100%">
                    <td>SERVICIO</td>
                    <td>DIAS</td>
                    <td>TIEMPO CONT.</td>
                    <td>PRECIO</td>
                </tr>
                @foreach ($listaPacks as $pack)
                <tr width="100%">
                    <td colspan="3"><b>Pack de servicio:
                        </b>{{ $packs->where('id', $pack->id)->first()->nombre }} </td>
                    <td><b>Importe: </b>{{ $pack->pivot->precio_final }} €</td>
                </tr>
                @foreach ($packs->where('id', $pack['id'])->first()->servicios as $servicioIndex => $servicio)
                <tr width="100%">
                    <td>{{ $servicioIndex + 1 }} - </td>
                    <td>Servicio: {{ $servicio->nombre }}
                    </td>
                    @if (!isset(json_decode($pack->pivot->numero_monitores, true)[$servicioIndex]))
                    <td>Monitores: 0</td>
                    @else
                    <td>Monitores:
                        {{ json_decode($pack->pivot->numero_monitores, true)[$servicioIndex] }}
                    </td>
                    @endif
                    <td>Importe base del servicio:
                        {{ $servicio->precioBase + $servicio->minMonitor * $servicio->precioMonitor }} €
                    </td>
                </tr>
                @endforeach
                @endforeach
            </tbody>
            </table>
            @endif
            @if (!empty($listaServicios)) <table class="table">
                <thead>
                    <tr width="100%">
                        <th colspan="4"></th>
                    </tr>
                </thead>
                <tbody>
                    <tr width="100%">
                        <td>SERVICIO</td>
                        <td>DIAS</td>
                        <td>TIEMPO CONT.</td>
                        <td>PRECIO</td>
                    </tr>
                    @foreach ($listaServicios as $servicio)
                    <tr width="100%">
                        <td colspan="2"><b>Servicio:
                            </b>{{ $servicios->firstWhere('id', $servicio['id'])->nombre }} </td>
                        <td><b>Número de monitores:</b>
                            {{ $servicio['numero_monitores'] }}
                        </td>
                        <td><b>Importe:</b> {{ $servicio['precioFinal'] }} €</td>
                    </tr>
                    @endforeach
                </tbody>
                </table>
                @endif



                <table class="table">
                    <tr width="100%">
                        <th colspan="4"></th>
                    </tr>
                    <tr width="100%">
                        <td colspan="4">Necesidades a cubrir por parte del contratante para el correcto funcionamiento del evento</td>
                    </tr>
                </table>

                <table class="table">
                    <thead>
                        <tr width="100%">
                            <th>OBSERVACIONES</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr width="100%">
                            <td>{{ $observaciones }}</td>
                        </tr>
                    </tbody>
                </table>

                <table class="table">
                    <tr width="100%">
                        <th colspan="4">PRESUPUESTO</th>
                    </tr>
                    <tr width="100%">
                        <td colspan="3">IMPORTE NETO</th>
                        <td colspan="1">{{$presupuesto->precioFinal}} €</th>
                    </tr>
                    <tr width="100%">
                        <td colspan="3">
                            </th>
                        <td colspan="1"> - €</th>
                    </tr>
                    <tr width="100%">
                        <td colspan="3">BASE IMPONIBLE</th>
                        <td colspan="1">{{$presupuesto->precioFinal}} €</th>
                    </tr>
                    <tr width="100%">
                        <th colspan="3">TOTAL</th>
                        <th colspan="1">{{$presupuesto->precioFinal}} €</th>
                    </tr>
                </table>
                <br>
                <br>
                <br>
                <br><br>
                <br>
                <br>
                <br>
    </div>
</body>

</html>