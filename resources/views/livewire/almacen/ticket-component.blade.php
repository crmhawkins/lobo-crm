<!DOCTYPE html>
<html>

<head>
    <style>
        body{
            font-size: 80% !important;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            font-family: Arial, Helvetica, sans-serif;
        }

        tr.left-aligned > th, td{
            text-align: right !important;
        }

        th,
        td {
            border: 0px solid black;
            padding: 10px;
            text-align: center;
        }

        .header-1 {
            background-color: #0196eb;
            color: #fff;
            font-size: 90%;
        }

        .header,
        .footer {
            width: 100%;
        }
        div.breakNow { page-break-inside:avoid; page-break-after:always; }
        .page-break {
            page-break-after: always;
        }

    </style>
</head>

<body>
<table class="header">
        <tr width="100%">
            <td width="10%"><img src="{{ public_path('images/logo_head.png') }}" alt="logo" width="100"></td>
            <td width="40%" style="text-align: left !important">
                <span style="display: inline; color:#0196eb"><b>LOBO DEL SUR S.L.</b></span><br>
                B16914285<br>
                PLAZA DEL VILLAR 8, PLANTA 1-A<br>
                LOS BARRIOS (11370), Cádiz, España
            </td>
            <td width="10%">&nbsp;</td>
            <td class="bold" width="40%" style="text-align: right !important">
                <h1 style="display: inline; color:#0196eb; font-weight:bolder;">Ticket simplificado</h1><br>
                <span style="font-size: 80%"><span style="font-weight: bold;">#{{$albaran->num_albaran}}</span><br>
                    <span style="font-weight: bold;">Fecha:</span> {{$albaran->fecha}}<br>
            </td>
        </tr>
    </table>
<div style="margin-left: -10%; width: 250%; border-bottom: 2px solid #bbbbbb" ></div>
    <!-- Información del Cliente y Dirección de Envío -->
    <table>
        <tr style="vertical-align: top;">
            <td style="text-align: left !important" width="50%">
                <span style="font-weight: bold; color:#0196eb">Cliente</span><br>
                <span style="font-weight: bold;">{{$cliente->nombre}}</span><br>
                {{$cliente->dni_cif}}<br>
                {{$cliente->direccion}}<br>
            </td>
        </tr>
    </table>

    <!-- Concepto, Precio, Unidades, Subtotal, IVA, Total -->
    <table>
        <tr style="background-color:#0196eb; color: #fff;" class="left-aligned">
            <th style="text-align: left !important">CONCEPTO</th>
            <th>PRECIO</th>
            <th>UNIDADES</th>
            <th>SUBTOTAL</th>
            <th>IVA</th>
            <th>TOTAL</th>
        </tr>
        <tr style="background-color:#fff; color: #fff;">
            <th style="padding: 0px !important; height: 10px !important;"></th>
        </tr>
        @foreach($productos_pedido as $producto)
        <tr class="left-aligned" style="background-color:#ececec;">
            <td style="text-align: left !important"><span style="font-weight: bold !important;">{{$productos->where('id', $producto->id)->first()->nombre}}</span><br></td>
            <td>{{$producto->precio_ud}}€</td>
            <td>{{$producto->unidades}}</td>
            <td>{{$producto->unidades * $producto->precio_ud}}€</td>
            <td>{{$productos->where('id', $producto->id)->first()->iva}}%</td>
            <td>{{($producto->unidades * $producto->precio_ud) * (1 + ($productos->where('id', $producto->id)->first()->iva / 100))}}€</td>
        </tr>
        @endforeach
    </table>

    <table style="margin-top: 5% !important">
        <tr style="background-color:#ececec;">
            <td></td>
            <td>BASE IMPONIBLE</td>
            <td>{{$base_imponible}}€</td>
        </tr>
        <tr style="background-color:#ececec;">
            <td></td>
            <td>IVA 21%</td>
            <td>{{$base_imponible * 0.21}}€</td>
        </tr>
        <tr style="background-color:#ececec;">
            <td></td>
            <td>TOTAL</td>
            <td>{{$base_imponible * 1.21}}€</td>
        </tr>
    </table>
    <div class="page-break"></div>
    <footer style="margin-top: 100px; page-break-after: avoid;position: fixed; top: -60px;padding-left:30px;padding-right:30px;height: 200px;">
        <strong>Condiciones legales</strong>
        <p>{{ $configuracion->texto_albaran }}</p>
    
    </footer>
</body>

</html>
