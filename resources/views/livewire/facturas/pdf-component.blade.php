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

        .page-break {
            page-break-after: always;
        }

        @media print {
            .avoid-page-break {
                page-break-inside: avoid;
            }
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
                <h1 style="display: inline; color:#0196eb; font-weight:bolder ;">FACTURA</h1><br>
                
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
    <!-- Información del Cliente y Dirección de Envío -->
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
                <span style="font-weight: bold; color:#0196eb">Dirección de envío</span><br>
                @if(isset($pedido))
                {{$pedido->direccion_entrega}}<br>
                {{$pedido->cod_postal_entrega}} - {{$pedido->localidad_entrega}} ({{$pedido->provincia_entrega}})<br><br>
                @else
                {{$cliente->direccionenvio}}<br>
                {{$cliente->codPostalenvio}} - {{$cliente->localidadenvio}} ({{$cliente->provinciaenvio}})<br><br>
                @endif
                <br>
                @if(isset($cliente->observaciones))
                <span style="font-weight: bold; color:#0196eb">Observaciones Descarga </span>
                <br>
                {{$cliente->observaciones}}
                @endif
            </td>
            <td style="text-align: left !important" width="20%"> 
                
            </td>
        </tr>
    </table>

    <!-- Concepto, Precio, Unidades, Subtotal, IVA, Total -->
    @php
        $productosPorPagina = 13;
        $numeroPaginasProductos = ceil(count($productos) / $productosPorPagina);
    @endphp

    @for ($i = 0; $i < $numeroPaginasProductos; $i++)
        <table class="avoid-page-break">
            @if(isset($pedido) && $factura->tipo != 2)
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
                @foreach (array_slice($productos, $i * $productosPorPagina, $productosPorPagina) as $producto)
                    <tr class="left-aligned" style="background-color:#ececec;">
                        <td style="text-align: left !important"><span style="font-weight: bold !important;"> {{ $producto['nombre'] }}</td>
                        <td>{{ $producto['lote_id'] }}</td>
                        <td>{{ $producto['cantidad'] }}</td>
                        <td>{{ $producto['peso_kg'] }} Kg</td>
                        <td>{{ number_format($producto['precio_ud'], 2) }}€</td>
                        <td>{{ number_format($producto['precio_total'], 2) }} €</td>
                    </tr>
                @endforeach
                
                
            @else
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
                @foreach (array_slice($productosFactura, $i * $productosPorPagina, $productosPorPagina) as $producto)
                    <tr class="left-aligned" style="background-color:#ececec;">
                        <td style="text-align: left !important"><span style="font-weight: bold !important;"> {{ $producto['nombre'] }}</td>
                        <td>{{ $producto['lote_id'] }}</td>
                        <td>{{ $producto['cantidad'] }}</td>
                        <td>{{ $producto['peso_kg'] }} Kg</td>
                        <td>{{ number_format($producto['precio_ud'], 2) }}€</td>
                        <td>{{ number_format($producto['precio_total'], 2) }} €</td>
                    </tr>
                @endforeach
            @endif
        </table>
        @if($i < $numeroPaginasProductos - 1)
        <div class="page-break"></div>
    @endif
    @endfor
    @if(isset($pedido))
        @if ($pedido->descuento && $factura->tipo != 2)
            <table>
                <tr>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td>Descuento Aplicado({{$pedido->porcentaje_descuento}}%):</td>
                    <td>{{$pedido->descuento_total}}€</td>
                </tr>
            </table>
        @endif
    @endif
    <table style="margin-top:10px;">
        @if(isset($servicios))
        <tr style="background-color:#0196eb; color: #fff;" class="left-aligned">
            <th style="text-align: left !important">Descripción</th>
            <th>UNIDADES</th>
            <th>PRECIO</th>
            <th>SUBTOTAL</th>
        </tr>
        <tr style="background-color:#fff; color: #fff;">
            <th style="padding: 0px !important; height: 10px !important;"></th>
        </tr>
        @foreach ($servicios as $servicio)
        <tr class="left-aligned" style="background-color:#ececec;">
            <td style="text-align: left !important"><span style="font-weight: bold !important;"> {{ $servicio->descripcion }}</td>
            <td>{{ $servicio->cantidad }}</td>
            <td>{{ $servicio->precio }}€</td>
            <td>{{ $servicio->total }}€</td>
        </tr>
        @endforeach
    
        @endif
    </table>
    @if(isset($pedido->gastos_envio) && $pedido->gastos_envio > 0)
        <table style="margin-top: 2% !important">
            <tr style="background-color:#ececec;" >
                <td></td>
                <td>Gastos Envío ({{ $pedido->transporte }})</td>
                <td>{{ $pedido->gastos_envio }}</td>
            </tr>
        </table>
    @endif
    @if($conIva)
        <table style="margin-top: 2% !important">
            <tr style="background-color:#ececec;">
                <td></td>
                <td>BASE IMPONIBLE</td>
                @if($factura->tipo == 2)
                    <td>{{ number_format($base_imponible , 2 , ',', '.')}}€</td>
                @else
                    <td>{{ number_format($factura->precio , 2 , ',', '.')}}€</td>
                @endif
                
            </tr>
            <tr style="background-color:#ececec;">
                <td></td>
                <td>IVA 21%</td>
                @if($factura->tipo == 2)
                    <td>{{ number_format($iva_productos , 2 , ',', '.')}}€</td>
                @else
                    <td>{{number_format(($factura->iva) , 2 , ',', '.')}}€</td>
                @endif
            </tr>
            @if(isset($factura->total_recargo) && $factura->tipo !=2)
                <tr style="background-color:#ececec;">
                    <td></td>
                    <td>Recargo {{number_format(($factura->recargo), 2 , ',', '.')}}%</td>
                    <td>{{number_format(($factura->total_recargo), 2 , ',', '.')}}€</td>
                </tr>
    
            @endif
            <tr style="background-color:#ececec;">
                <td></td>
                <td>TOTAL</td>
                @if($factura->tipo == 2)
                    <td>{{ number_format($total , 2 , ',', '.')}}€</td>
                @else
                    <td>{{number_format(($factura->total), 2 , ',', '.')}}€</td>
                @endif
                
            </tr>
        </table>
    @else
        <table style="margin-top: 5% !important">
            <tr style="background-color:#ececec;">
                <td></td>
                <td>Total</td>
                @if($factura->tipo == 2)
                    <td>{{ number_format($base_imponible , 2 , ',', '.')}}€</td>
                @else
                    <td>{{number_format(($factura->precio), 2 , ',', '.')}}€</td>
                @endif
            </tr>
        </table>
    @endif
    
    <!-- Información adicional: Albarán, Pedido, Pallet, Transferencia -->
    <table class="footer" >
        <tr>
            <td style="text-align: left !important"><span style="font-weight: bold">Forma de pago: </span>
                @switch($factura->metodo_pago)
                    @case('giro_bancario')
                        Giro Bancario
                        @break
                    @case('pagare')
                        Pagare
                        @break
                    @case('confirming')
                        Confirming
                        @break
                    @case('transferencia')
                        Transferencia
                        @break
                    @case('otros')
                        Otros
                        @break
                    @default
                        ------------------
                        @break
                @endswitch
                <br>
                <br>
                <span style="font-weight: bold">Número de cuenta: </span><span> {{$configuracion->cuenta}}  </span>
            </td>
    
            <td ><span style="font-weight: bold;">Vencimiento a {{ $cliente->vencimiento_factura_pref}} días </span></td>
        </tr>
        {{-- <tr>
            <td style="text-align: left !important"><span style="font-weight: bold">Albarán:</span> {{ $albaran->num_albaran }}</td>
            <td style="text-align: left !important">Nº PEDIDO: {{$pedido->id}}</td>
        </tr>
        <tr>
            <td style="text-align: left !important">Pagar por transferencia bancaria al siguiente número de cuenta:<br>
                <span style="font-weight: bold">LA CAIXA: ES31 2100 8508 5102 0019 7802</span>
            </td>
        </tr> --}}
    </table>
    @if(isset($factura->descripcion))
        <div style="margin-top: 20px ;">
            <span style="font-weight: bold; color:#0196eb">Nota:</span><br>
            <p style=" background-color:#ececec; padding: 10px">{{$factura->descripcion}}</p>
        </div>
    @endif
    
    </body>
    
    </html>
    
