<!DOCTYPE html>
<html>

<head>
    <style>
        body {
            font-size: 80% !important;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            font-family: Arial, Helvetica, sans-serif;
        }

        tr.left-aligned>th,
        td {
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

        .page-break {
            page-break-after: always;
        }

        @media print {
            .avoid-page-break {
                page-break-inside: avoid;
            }
        }
        div.breakNow {
            page-break-inside: avoid;
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
            <td class="bold" @if($factura->tipo == 2) width="60%" @else width="40%" @endif style="text-align: right !important">
                <h1 style="display: inline; color:#0196eb; font-weight:bolder;">ALBARÁN DE RECOGIDA</h1><br>

                <span style="font-size: 80%"><span style="font-weight: bold;">#{{$factura->numero_factura}}</span><br>
                    @if(isset($pedido) )
                        @if($pedido->npedido_cliente)
                            <span style="font-weight: bold;">Ped.Cliente:</span> {{$pedido->npedido_cliente}}<br>
                        @endif

                        @if($albaran->num_albaran)
                            <span style="font-weight: bold;">Albarán:</span> {{$albaran->num_albaran}}<br>
                        @endif
                        @if($pedido->id)
                            <span style="font-weight: bold;">Pedido:</span> {{$pedido->id}}<br>
                        @endif
                    @endif

                    <span style="font-weight: bold;">Fecha:</span> {{$factura->fecha_emision}}<br>
                    <span style="font-weight: bold;">Vencimiento:</span> {{$factura->fecha_vencimiento}}</span>
            </td>
        </tr>
    </table>
    <div style="margin-left: -10%; width: 250%; border-bottom: 2px solid #bbbbbb"></div>
    <!-- Información del Cliente y Dirección de Recogida -->
    <table>
        <tr style="vertical-align: top;">
            <td style="text-align: left !important" width="40%">
                <span style="font-weight: bold; color:#0196eb">Cliente</span><br>
                <span style="font-weight: bold;">{{$cliente->nombre}}</span><br>
                {{$cliente->dni_cif}}<br>
                {{$cliente->direccion}}<br>
                {{$cliente->localidad}} ({{$cliente->cod_postal}}), {{$cliente->provincia}}, España<br>
                {{$cliente->telefono}}<br>
                {{$cliente->email}}
            </td>
            <td style="text-align: left !important" width="10%"></td>
            <td style="text-align: left !important" width="30%">
                <span style="font-weight: bold; color:#0196eb">Dirección de Recogida</span><br>
                @if(isset($destino))
                    {{ $destino }}
                @elseif(isset($pedido))
                    {{$pedido->direccion_entrega}}<br>
                    {{$pedido->cod_postal_entrega}} - {{$pedido->localidad_entrega}} ({{$pedido->provincia_entrega}})<br><br>
                @endif
                <br>
            </td>
            <td style="text-align: left !important" width="20%"></td>
        </tr>
    </table>

    <!-- Concepto, Precio, Unidades, Subtotal, IVA, Total -->
    @php
        $productosPorPagina = 13;
        $numeroPaginasProductos = ceil(count($productos) / $productosPorPagina);
        $ultimoProductoEnPagina = count($productos) % $productosPorPagina;
    @endphp

    @for ($i = 0; $i < $numeroPaginasProductos; $i++)
        <table class="avoid-page-break">
            <tr style="background-color:#0196eb; color: #fff;" class="left-aligned">
                <th style="text-align: left !important">CONCEPTO</th>
                <th>LOTE</th>
                <th>UNIDADES</th>
                <th>PESO TOTAL</th>
            </tr>
            <tr style="background-color:#fff; color: #fff;">
                <th style="padding: 0px !important; height: 10px !important;"></th>
            </tr>

            @foreach (array_slice($productos, $i * $productosPorPagina, $productosPorPagina) as $producto)
                <tr class="left-aligned" style="background-color:#ececec;">
                    <td style="text-align: left !important"><span style="font-weight: bold !important;"> {{ $producto['nombre'] }}</span></td>
                    <td>{{ $producto['lote_id'] }}</td>
                    <td>{{ $producto['cantidad'] }}</td>
                    <td>{{ $producto['peso_kg'] }} Kg</td>
                </tr>
            @endforeach
        </table>

        {{-- @if($i < $numeroPaginasProductos - 1)
            <div class="page-break"></div>
        @endif --}}
    @endfor
    <div class="page-break"></div>

    <footer style="margin-top: 100px; page-break-after: avoid;position: fixed; bottom: -60px;padding-left:30px;padding-right:30px;height: 200px;">
        <p>{{ $configuracion->texto_factura }}</p>
    </footer>
</body>

</html>
