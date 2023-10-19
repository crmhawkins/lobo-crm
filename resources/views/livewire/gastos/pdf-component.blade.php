<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>Factura</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css"
        integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">

    <style>
        /* Agrega estilos aquí */
    </style>
</head>

<body style="margin-right: 2rem">
    <div class="row">
        <div class="col-12">
            <img src="{{ public_path('/assets/logo_formal.png') }}" alt="" title="" class="img-fluid"
                style="max-width: 300px">
        </div>
    </div>
    <div class="d-inline">
        <div class="col-12">
            <div style="float: left">
             <p class="mb-0">Formal SL</p>
            <p class="mb-0">Calle Camino de la Ermita S/N Parcela C5</p>
            <p class="mb-0">La Línea de la Concepción - 11300</p>
            <p class="mb-0">B11492642</p>
            </div>
            <div style="float: right">
                <p class="mb-0"><h5><strong>FACTURA Número {{$factura->numero_factura}}</strong></h5></p>
                <p class="mb-0">Fecha: {{$factura->fecha_emision}}</p>
            </div>
        </div>
    </div>
    <div class="d-block" style="clear:both; margin:3rem 0 ;">
        <div class="col-12" style="float: left">
            @if ($alumno->empresa_id > 0)
                <h4>Datos del cliente</h4>
                <p class="mb-0"><strong>Nombre:</strong> {{$empresa->nombre}}</p>
                <p class="mb-0"><strong>Dirección:</strong> {{$empresa->direccion}}</p>
                <p class="mb-0"><strong>CIF:</strong> {{$empresa->cif}}</p>
                <p class="mb-0"><strong>Email:</strong> {{$empresa->email}}</p>
            @else
                <h4>Datos del cliente</h4>
                <p class="mb-0"><strong>Nombre:</strong> {{$alumno->nombre}} {{$alumno->apellidos}}</p>
                <p class="mb-0"><strong>Dirección:</strong> {{$alumno->direccion}}</p>
                <p class="mb-0"><strong>DNI:</strong> {{$alumno->dni}}</p>
                <p class="mb-0"><strong>Email:</strong> {{$alumno->email}}</p>
            @endif

        </div>
    </div>

    <div class="d-block" style="clear:both;">
        <div class="col-12" style="float: left; margin-top: 3rem">
            <h5>Detalles de la factura</h5>
            <table class="table">
                <thead>
                    <tr>
                        <th>Curso</th>
                        <th>Total sin IVA</th>
                        <th>IVA</th>
                        <th>Descuento</th>
                        <th>Total</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Agrega filas aquí para cada producto -->
                    {{-- @foreach ($conceptos2 as $concepto)

                    @endforeach --}}
                    <tr>
                        <td>{{$curso->nombre}}</td>
                        <td>{{$presupuesto->total_sin_iva}} €</td>
                        <td>{{$presupuesto->iva}} %</td>
                        @if ($presupuesto->descuento != null)
                        <td>{{$presupuesto->descuento}} %</td>
                        @else
                        <td>0%</td>
                        @endif
                        <td>{{$presupuesto->precio}} €</td>
                    </tr>
                </tbody>
            </table>
            <br>
            {{-- <p><strong>Total:</strong> {{$factura->total}} €</p> --}}
        </div>

    </div>
    <footer>
        <br>
    </footer>
</body>

</html>
