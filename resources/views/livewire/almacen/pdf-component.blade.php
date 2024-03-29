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
                <h1 style="display: inline; color:#0196eb; font-weight:bolder ;">ALBARÁN</h1><br>
                <span style="font-size: 80%"><span style="font-weight: bold;">#{{$num_albaran}}</span><br>
                    <span style="font-weight: bold;">Fecha:</span> {{$fecha_albaran}}<br>
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
                {{$cliente->localidad}} ({{$cliente->cod_postal}}), {{$cliente->localidad}}, España<br>
                {{$cliente->telefono}}<br>
                {{$cliente->email}}
            </td>
            <td style="text-align: left !important" width="50%">
                <span style="font-weight: bold; color:#0196eb">Dirección de envío</span><br>
                {{$pedido->direccion_entrega}}<br>
                {{$pedido->cod_postal_entrega}} - {{$pedido->localidad_entrega}} ({{$pedido->provincia_entrega}})<br><br>
            </td>
        </tr>
    </table>

    <!-- Concepto, Precio, Unidades, Subtotal, IVA, Total -->
    <table>
        <tr style="background-color:#0196eb; color: #fff;" class="left-aligned">
            <th style="text-align: left !important">CONCEPTO</th>
            <th>LOTE</th>
            <th>UNIDADES</th>
            <th>PESO TOTAL</th>
            <th>PRECIO</th>
            <th>SUBTOTAL</th>
        </tr>
        <tr style="background-color:#fff; color: #fff;">
            <th style="padding: 0px !important; height: 10px !important;"></th>
        </tr>
        @foreach($productos as $producto)
        <tr class="left-aligned" style="background-color:#ececec;">
            <td style="text-align: left !important"><span style="font-weight: bold !important;">{{ $producto['nombre'] }}</span><br></td>
            <td>{{ $producto['lote_id'] }}</td>
            <td>{{ $producto['cantidad'] }}</td>
            <td>{{ $producto['peso_kg'] }} Kg</td>
            <td>{{ number_format($producto['precio_ud'], 2) }}€</td>
            <td>{{ number_format($producto['precio_total'], 2) }}€</td>
        </tr>
        @endforeach
        @if ($pedido->descuento )
        <tr>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td>Descuento Aplicado:</td>
            <td>{{$pedido->porcentaje_descuento}}%<</td>
        </tr>
        @endif
    </table>
    @if($conIva)
            <table style="margin-top: 5% !important">
                <tr style="background-color:#ececec;">
                    <td></td>
                    <td>BASE IMPONIBLE</td>
                    <td>{{ number_format($pedido->precio, 2) }}€</td>
                </tr>
                <tr style="background-color:#ececec;">
                    <td></td>
                    <td>IVA 21%</td>
                    <td>{{number_format($pedido->precio * 0.21, 2)}}€</td>
                </tr>
                <tr style="background-color:#ececec;">
                    <td></td>
                    <td>TOTAL</td>
                    <td>{{number_format($pedido->precio * 1.21, 2)}}€</td>
                </tr>
            </table>
    @else
        <table style="margin-top: 5% !important">
            <tr style="background-color:#ececec;">
                <td></td>
                <td>Total</td>
                <td>{{ number_format($pedido->precio, 2) }}€</td>
            </tr>
        </table>

    @endif
</body>

</html>
