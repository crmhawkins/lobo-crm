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
            float: left;
            width: 25%;
            font-weight: bold;
        }

        .row .value {
            float: left;
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
        }

        .table th,
        .table td {
            border: 1px solid #000;
            padding: 5px;
            text-align: left;
        }

        .totals {
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
        <img src="{{ public_path('assets/images/logo_factura.png') }}" alt="Firma del Cliente"
            style="display: float; float: left; height:10%">

        <div class="header">
            <h2>Factura número {{$factura->id}}</h2>
        </div>
        <br>
        <br>

        <div class="row">
            <div class="label">Fecha de emisión:</div>
            <div class="value">{{ substr($factura->fecha_emision, 0, 10) }}</div>
        </div>
        <div class="row">
            <div class="label">Fecha de vencimiento:</div>
            <div class="value">{{ substr($factura->fecha_vencimiento, 0, 10) }}</div>
        </div>

        <div class="row">
            <div class="label">Cliente:</div>
            <div class="value">{{ $cliente->nombre }}</div>
        </div>

        <br>
        <br>
        <br>
        <br>

        <table class="table">
            <thead>
                <tr>
                    <th>Servicio</th>
                    <th>Monitores</th>
                    <th>Precio</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($listaPacks as $packIndex => $pack)
                    <tr>
                        <th>{{ $packs->where('id', $pack['id'])->first()->nombre }}</th>
                        <th>{{ array_sum($pack['numero_monitores']) }} monitores </th>
                        <th>{{ $pack['precioFinal'] }} € </th>
                    </tr>

                    @foreach ($packs->where('id', $pack['id'])->first()->servicios()->get() as $keyPack => $servicioPack)
                        <tr>
                            <td>{{ $servicioPack->nombre }} </td>
                            <td>{{ $pack['numero_monitores'][$keyPack] }} monitores</td>
                            <td>  </td>
                        </tr>
                    @endforeach
                @endforeach
                @foreach ($listaServicios as $servicioIndex => $servicio)
                <tr>
                    <td>{{ $servicio->nombre }} </td>
                    <td>{{ $servicio['numero_monitores'][$keyPack] }} monitores</td>
                    <td>{{ $pack['precioFinal'] }} € </td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <table class="totals">
            <tr>
                <th>Subtotal</th>
                <td>{{ $presupuesto->precioBase }}</td>
            </tr>
            <tr>
                <th>Descuento</th>
                <td>{{ $presupuesto->descuento }}</td>
            </tr>
            <tr>
                <th>Precio Total</th>
                <td>{{ $presupuesto->precioFinal }}</td>
            </tr>
        </table>
        <br>
        <br>
        <br>
        <br><br>
        <br>
        <br>
        <br>
        {{-- <div class="signature">
            <p>Firma del Cliente:</p>
            <img src="{{ public_path('storage/' . $parte->firma) }}" alt="Firma del Cliente">

        </div> --}}
    </div>
</body>

</html>
