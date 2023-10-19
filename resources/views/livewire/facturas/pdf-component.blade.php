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
    <table class="header-1" style="margin-bottom: 5%">
        <tr width="100%">
            <td width="20%" style="background-color: #fff !important"></td>
            <th width="40%">administracion@serlobo.com</th>
            <th width="40%">654183607</th>
        </tr>
    </table>
    <!-- Parte superior: Logo, Dirección, Factura -->
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
                <h1 style="display: inline; color:#0196eb; font-weight:bolder ;">FACTURA</h1><br>
                <span style="font-size: 80%"><span style="font-weight: bold;">#F230569</span><br>
                    <span style="font-weight: bold;">Fecha:</span> 16/05/2023<br>
                    <span style="font-weight: bold;">Vencimiento:</span> 16/08/2023</span>
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
                {{$cliente->localidad}} ({{$cliente->cod_postal}}), {{$cliente->provincia}}, España<br>
                {{$cliente->telefono}}<br>
                {{$cliente->email}}
            </td>
            <td style="text-align: left !important" width="50%">
                <span style="font-weight: bold; color:#0196eb">Dirección de envío</span><br>
                ALMACÉN VALENCIA<br>
                POLIGONO INDUSTRIAL EL BONY<br>
                CALLE 32 - NAVE 219<br>
                46470 - CATARROJA (VALENCIA)<br><br>
                HORARIO: 08:00 a 13:00 h<br>
                LLAMAR A JORGE ALMAGRO PARA CONCERTAR CITA<br>
                610118397
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
        <tr class="left-aligned" style="background-color:#ececec;">
            <td style="text-align: left !important"><span style="font-weight: bold !important;"> LOBO CREMA PINK</span><br>sabor fresa (13080523)</td>
            <td>7,50€</td>
            <td>630</td>
            <td>4.725,00€</td>
            <td>21%</td>
            <td>5.717,25€</td>
        </tr>
    </table>

    <table style="margin-top: 5% !important">
        <tr style="background-color:#ececec;">
            <td></td>
            <td>BASE IMPONIBLE</td>
            <td>3</td>
        </tr>
        <tr style="background-color:#ececec;">
            <td></td>
            <td>IVA 21%</td>
            <td>3</td>
        </tr>
        <tr style="background-color:#ececec;">
            <td></td>
            <td>TOTAL</td>
            <td>3</td>
        </tr>
    </table>

    <!-- Información adicional: Albarán, Pedido, Pallet, Transferencia -->
    <table class="footer" >
        <tr>
            <td style="text-align: left !important"><span style="font-weight: bold">Albarán:</span> A230334</td>
        </tr>
        <tr>
            <td style="text-align: left !important">Nº PEDIDO: SERGI12 050223</td>
        </tr>
        <tr>
            <td style="text-align: left !important">Enviado desde fábrica</td>
        </tr>
        <tr>
            <td style="text-align: left !important">1 PALLET PINK (105 CAJAS)</td>
        </tr>
        <tr>
            <td style="text-align: left !important">Pagar por transferencia bancaria al siguiente número de cuenta:<br>
                <span style="font-weight: bold">LA CAIXA: ES31 2100 8508 5102 0019 7802</span>
            </td>
        </tr>
    </table>
</body>

</html>
