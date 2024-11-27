<!DOCTYPE html>
<html>

<head>
    <style>
        body{
            font-size: 80% !important;
        }
        span,table {
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
    <table class="header-1" style="margin-bottom: 5%">
        <tr width="100%">
            <td width="25%" style="background-color: #fff !important; padding: 0;"><img style="margin: 8px" src="{{ public_path('images/LOGO-LOBO-COLOR.png') }}" alt="logo" width="100%" height="auto"></td>
            <td width="35%" style="background-color: #fff !important"></td>
            <th width="40%" style="background-color: #fff !important"><span style="background-color: #0196eb !important; padding: 2rem; display: block;">Administracion@serlobo.com</span></th>
        </tr>
    </table>
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
                <h1 style="display: inline; color:#0196eb; font-weight:bolder ;">ALBARÁN</h1><br>
                <span style="font-size: 80%"><span style="font-weight: bold;">#{{$num_albaran}}</span><br>
               
                <span style="font-weight: bold;">Nº Pedido:</span> {{$pedido->id}}<br>
                @if($pedido->npedido_cliente != null)
                    <span style="font-weight: bold;">Nº Pedido Cliente:</span> {{$pedido->npedido_cliente}}<br>
                @endif
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
                <br>
                @if(isset($cliente->observaciones))
                <span style="font-weight: bold; color:#0196eb">Observaciones Descarga </span>
                    <br>
                    {{$cliente->observaciones}}
                @endif
            </td>
        </tr>
    </table>
    <div style="margin-top: 2%; margin-bottom: 2%;">
        <span style="font-weight: bold; color:#0196eb">Observaciones:</span><br>
        <div style="background-color: #ececec; padding: 10px;">
            {{$pedido->observaciones}}
        </div>
    </div>
    <!-- Concepto, Precio, Unidades, Subtotal, IVA, Total -->
    <table>
        <tr style="background-color:#0196eb; color: #fff;" class="left-aligned">
            <th style="text-align: left !important">CONCEPTO</th>
            <th>LOTE</th>
            <th>PALLETS</th> <!-- Añadir columnas para pallets -->
            <th>CAJAS</th>
            <th>UNIDADES</th>
            <th>PESO TOTAL</th>
        </tr>
        <tr style="background-color:#fff; color: #fff;">
            <th style="padding: 0px !important; height: 10px !important;"></th>
        </tr>
        @php
            $pesoTotal = 0;
        @endphp
        @foreach($productos as $producto)
            <tr class="left-aligned" style="background-color:#ececec;">
                <td style="text-align: left !important"><span style="font-weight: bold !important;">{{ $producto['nombre'] }}</span></td>
                <td>{{ $producto['lote_id'] }}</td>
                <td>{{ $producto['num_pallet'] ?? 0 }}</td> <!-- Mostrar número de pallets -->
                <td>{{ $producto['num_cajas'] ?? 0 }}</td> <!-- Mostrar número de cajas -->
                <td>{{ $producto['cantidad'] }}</td>
                @php
                $pesoTotal += $producto['peso_kg'];
                @endphp
                <td>{{ number_format($producto['peso_kg'], 2) }} Kg</td>
            </tr>
        @endforeach
        @if($productosMarketing)
            @if(count($productosMarketing) > 0)
                @foreach($productosMarketing as $productoMarketingPedido)
                    @php
                        $producto = $productoMarketingPedido->producto; // Obtenemos el producto de marketing
                        
                        // Cálculos
                        $unidades = $productoMarketingPedido->unidades;
                        $cajas = floor($unidades / $producto->unidades_por_caja); // Calculamos el total de cajas redondeando hacia abajo
                        $pallets = floor($cajas / $producto->cajas_por_pallet); // Calculamos el total de pallets redondeando hacia abajo
                        $cajasSobrantes = $cajas % $producto->cajas_por_pallet; // Cajas sobrantes que no llenan un pallet
                        $pesoTotalProducto = $unidades * $producto->peso_neto_unidad / 1000; // Peso total en kg
                
                        // Sumamos el peso total al peso total de todo el pedido
                    @endphp
                
                    <tr class="left-aligned" style="background-color:#ececec;">
                        <td style="text-align: left !important">
                            <span style="font-weight: bold !important;">{{ $producto->nombre }}</span>
                        </td>
                        <td>{{ $productoMarketingPedido->lote_id ?? '' }}</td> <!-- Lote -->
                        <td>{{ $pallets }}</td> <!-- Número de pallets -->
                        <td>{{ $cajas }}</td> <!-- Cajas sobrantes -->
                        <td>{{ $unidades }}</td> <!-- Unidades restantes -->
                        @php
                        $pesoTotal += $pesoTotalProducto;
                        @endphp
                        <td>{{ number_format($pesoTotalProducto, 2) }} Kg</td> <!-- Peso total del producto -->
                    </tr>
                @endforeach
            @endif
        @endif
    </table>
    <table style="margin-top: 1% !important">
        <tr style="background-color:#ececec;">
            <td></td>
            <td></td>
            <td></td>
            <td></td>

            <td>PESO TOTAL</td>
            <td>{{ number_format($pesoTotal, 2) }} Kg</td>
        </tr>
    </table>

    @if(isset($nota) && $nota != '')
        <div style="margin-top: 2%; margin-bottom: 2%;">
            <span style="font-weight: bold; color:#0196eb">Nota:</span><br>
            <div style="background-color: #ececec; padding: 10px;">
                {{$nota}}
            </div>
        </div>
    @endif
    <div class="page-break"></div>
    <footer style="margin-top: 100px; page-break-after: avoid;position: fixed; top: -60px;padding-left:30px;padding-right:30px;height: 200px;">
        <strong>Condiciones legales</strong>
        <p>{{ $configuracion->texto_albaran }}</p>
    
    </footer>
    
</body>

</html>
