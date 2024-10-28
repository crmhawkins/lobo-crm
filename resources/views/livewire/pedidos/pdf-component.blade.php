<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Pedido</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            font-size: 12px;
        }
        .header-table, .details-table {
            width: 100%;
            border-collapse: collapse;
        }
        .header-table td {
            padding: 5px;
        }
        .logo {
            width: 100px;
        }
        .company-details {
            text-align: left;
            vertical-align: top;
        }
        .order-details {
            text-align: right;
            vertical-align: top;
        }
        .details-table th, .details-table td {
            border: 1px solid #ddd;
            padding: 8px;
        }
        .details-table th , .footer-table td {
            background-color: #f2f2f2;
            color: black;
        }
        .footer-table td{
            font-weight: bold;
            padding: 10px 0px;
        }
        .footer-table {
            margin-top: 20px;
            width: 100%;
            text-align: center;
            border: 1px solid #ddd;

        }
        div.breakNow {
            page-break-inside: avoid;
            page-break-after: always;
        }
    </style>
</head>
<body>
    <footer style="margin-top: 100px; page-break-after: avoid;position: fixed; bottom: -60px;padding-left:30px;padding-right:30px;height: 200px;">
        <strong>Condiciones legales</strong>
        <p>{{ $configuracion->texto_pedido }}</p>
    </footer>
    <table class="header-table">
        <tr>
            <td><img src="{{ public_path('images/logo_head.png') }}" alt="Logo" class="logo"></td>
            <td class="company-details">
                <strong>LOBO DEL SUR S.L.</strong><br>
                B16914285<br>
                AVD. CAETARIA 4.5 P.I LA MENACHA<br>
                ALGECIRAS (CÁDIZ) 11205, España
            </td>
            <td class="order-details">
                <strong>Pedido #{{ $pedido->id }}</strong><br>
                Fecha: {{ $pedido->fecha }}<br>
                Cliente: {{ $cliente->nombre }}
            </td>
        </tr>
    </table>

    <h2>Detalles del Pedido</h2>
    <table class="details-table">
        <tr>
            <th>Producto</th>
            <th>Cantidad</th>
            <th>Precio Unitario</th>
            <th>Total</th>
        </tr>
        @foreach ($productos as $producto)
        <tr>
            <td>{{ $producto['nombre'] }}</td>
            <td>{{ $producto['cantidad'] }}</td>
            <td>{{ number_format($producto['precio_ud'], 2) }}€</td>
            <td>{{ number_format($producto['precio_total'], 2) }}€</td>
        </tr>
        @endforeach
    </table>

    <table class="footer-table">
        <tr>
            <td>Total Pedido: {{ number_format($pedido->precio, 2) }}€</td>
        </tr>
        @if($conIva)
            <tr>
                <td>Total Iva: {{ number_format($pedido->iva_total, 2) }}€</td>
            </tr>
            <tr>
                <td>Total + Iva: {{ number_format($pedido->iva_total + $pedido->precio, 2) }}€</td>
            </tr>
        @endif
        {{-- <tr>
            <td>Descuento Aplicado: {{ $pedido->descuento ? 'Sí' : 'No' }}</td>
        </tr> --}}
    </table>
    @if($pedido->observaciones)
        <p>Observaciones: {{ $pedido->observaciones }}</p>
    @endif

    <p class="footer-table">
        Gracias por su pedido.<br>
        Para cualquier consulta, no dude en contactarnos.
    </p>
</body>
</html>
