
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
            <td width="25%" style="background-color: #fff !important; padding: 0;"><img style="margin: 8px" src="{{ public_path('images/LOGO-LOBO-COLOR.png') }}" alt="logo" width="100%" height="auto"></td>
            <td width="35%" style="background-color: #fff !important"></td>
            <th width="40%" style="background-color: #fff !important"><span style="background-color: #0196eb !important; padding: 2rem; display: block;">Administracion@serlobo.com</span></th>
        </tr>
    </table>
    <!-- Parte superior: Logo, Dirección, Factura -->
    <table class="header">
        <tr width="100%">
            <td width="40%" style="text-align: left !important">
                <span style="display: inline; color:#0196eb"><b>LOBO DEL SUR S.L.</b></span><br>
                B16914285<br>
                AVD. CAETARIA 4.5 P.I LA MENACHA<br>
                ALGECIRAS (CÁDIZ) 11205, España
            </td>
            <td width="20%">&nbsp;</td>
            <td class="bold" width="40%" style="text-align: right !important">
                <h1 style="display: inline; color:#0196eb; font-weight:bolder ;">Historial de Stock @if(isset($tipo) && $tipo === "Saliente") Saliente @else Entrante @endif</h1><br>
            </td>
        </tr>
    </table>
<div style="margin-left: -10%; width: 250%; border-bottom: 2px solid #bbbbbb" ></div>
   
<br>
<table   class="table table-striped table-bordered dt-responsive nowrap"  >
    <thead>
        <tr style="background-color:#0196eb; color: #fff;" class="left-aligned">
            <th>Nº Interno</th>
            <th>N.º Lote</th>
            <th>Pedido</th>
            <th>Almacen</th>
            <th>Producto</th>
            <th>Fecha de entrada</th>
            <th>Cantidad (en Botellas)</th>
            <th>Cantidad (en Cajas)</th>
            <th>Tipo</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($producto_lotes as $loteIndex => $lote)
            <tr style="background-color:#ececec;">
                <th>{{ $lote['lote_id'] }}</th>
                <th>{{ $lote['orden_numero'] }}</th>
                <th>{{ $lote['pedido_id'] }}</th>
                <th>{{ $lote['almacen'] }}</th>
                <th>{{ $lote['producto'] }}</th>
                <td>{{ $lote['fecha'] }}</td>
                <td>{{ $lote['cantidad'] }}</td>   
                <td>{{ $lote['cajas']}}</td>
                <td>{{ $lote['tipo'] }}</td>
            </tr>
        @endforeach
</table>

 
</body>

</html>





    

