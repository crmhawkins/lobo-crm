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
            page-break-inside: avoid;
        }

        .table th {
            background-color: #3db1f3;
            text-align: center;
        }

        .table th,
        .table td {
            border: 1px solid #000;
            padding: 5px;
            text-align: left;
        }

        .i .totals {
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
            style="display: float; float: right; height:10%">
        <div class="row">
            <div class="label"><b>EDUCACIÓN, OCIO Y TIEMPO LIBRE LA FABRICA S.L.</b></b></div>
        </div>
        <div class="row">
            <div class="value">B-11949658</div>
        </div>
        <div class="row">
            <div class="value">Avd. Alcalde Cantos Ropero, 51 Pol. Ind. "Jerez 2000" Nave 14</div>
        </div>
        <div class="row">
            <div class="value">11408 Jerez de la Frontera ( Cádiz)</div>
        </div>
        <div class="row">
            <div class="value">956 042 751 &nbsp; &nbsp; &nbsp; &nbsp; 673 811 838</div>
        </div>
        <br>
        <br>
        <br>
        <br>

        <table class="table">
            <thead>
                <tr width="100%">
                    <th colspan="2">CONTRATO SERVICIOS</th>
                </tr>
            </thead>
            <tbody>
                <tr width="100%">
                    <td width="50%" style="border-right-color: #fff !important;"> <b>Contrato Nº</b>
                        {{ $nContrato }} </td>
                    <td width="50%"> <b>Fecha del evento:</b> {{ $evento->diaEvento }} </td>
                </tr>
            </tbody>
        </table>

        <table class="table">
            <thead>
                <tr width="100%">
                    <th colspan="2">DATOS DEL SOLICITANTE</th>
                </tr>
            </thead>
            <tbody>
                <tr width="100%">
                    <td width="70%" style="border-right-color: #fff !important;"><b>Nombre:</b>
                        {{ $cliente->nombre }} {{ $cliente->apellido }}</td>
                    <td width="30%"><b>DNI:</b> {{ $cliente->nif }}</td>
                </tr>
                <tr width="100%">
                    <td width="80%" style="border-right-color: #fff !important;"><b>Domicilio:</b>
                        {{ $cliente->tipoCalle }} {{ $cliente->calle }} , {{ $cliente->numero }}</td>
                    <td width="20%"><b>CP:</b> {{ $cliente->codigoPostal }}</td>
                </tr>
                <tr width="100%">
                    <td width="60%" style="border-right-color: #fff !important;"><b>Localidad:</b>
                        {{ $cliente->ciudad }}</td>
                    <td width="40%"><b>Provincia:</b> {{ $cliente->ciudad }}</td>
                </tr>
                <tr width="100%">
                    <td width="50%" style="border-right-color: #fff !important;"><b>Teléfono:</b>
                        {{ $cliente->tlf1 }}</td>
                    <td width="50%"><b>Email: </b>{{ $cliente->email1 }}</td>
                </tr>
            </tbody>
        </table>
        <table class="table">
            <thead>
                <tr width="100%">
                    <th colspan="3">DATOS DEL EVENTO</th>
                </tr>
            </thead>
            <tbody>
                <tr width="100%">
                    <td width="40%" style="border-right-color: #fff !important;"><b>Evento:</b>
                        {{ $evento->eventoNombre }} </td>
                    <td width="40%" style="border-right-color: #fff !important;"><b>Protagonistas:</b>
                        {{ $evento->eventoProtagonista }}</td>
                    <td width="20%"><b>Niños: {{ $evento->eventoNiños }}</td>
                </tr>
                <tr width="100%">
                    <td width="40%" style="border-right-color: #fff !important;"><b>Contacto:</b>
                        {{ $evento->eventoContacto }} </td>
                    <td width="20%" style="border-right-color: #fff !important;"><b>Parentesco:</b>
                        {{ $evento->eventoParentesco }} </td>
                    <td width="40%"><b>Teléfono: {{ $evento->eventoTelefono }}</td>
                </tr>
                <tr width="100%">
                    <td colspan="2" style="border-right-color: #fff !important;"><b>Lugar:</b>
                        {{ $evento->eventoLugar }}</td>
                    <td><b>Localidad:</b> {{ $evento->eventoLocalidad }}</td>
                </tr>
                <tr width="100%">
                    <td colspan="3"><b>Posibilidad de montaje:</b> {{ $evento->eventoLocalidad }}</td>
                </tr>
            </tbody>
        </table>
        @if (isset($listaPacks) && count($listaPacks) > 0)
            <table class="table">
                <thead>
                    <tr width="100%">
                        <th colspan="5">PACKS DE SERVICIOS CONTRATADOS</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($listaPacks as $pack)
                        <tr width="100%">
                            <td colspan="4" style="border-right-color: #fff !important;"><b>Pack de servicio:
                                </b>{{ $packs->where('id', $pack['id'])->first()->nombre }} </td>
                            <td><b>Importe: </b>{{ $pack['precio_final'] }} €</td>
                        </tr>
                        @foreach ($packs->where('id', $pack['id'])->first()->servicios as $servicioIndex => $servicio)
                            <tr width="100%">
                                <td style="text-align: center" rowspan="2"> <b> Servicio
                                        {{ $servicioIndex + 1 }} </b> </td>
                                <td style="border-right-color: #fff !important;" colspan="3">Servicio:
                                    {{ $servicio->nombre }}
                                </td>
                                <td>Importe base del servicio:
                                    {{ $servicio->precioBase + $servicio->minMonitor * $servicio->precioMonitor }} €
                                </td>
                            </tr>
                            <tr width="100%">
                                @if (!isset($pack['tiempos'][$servicioIndex]))
                                    <td style="border-right-color: #fff !important;">Tiempo: 00:00h</td>
                                @else
                                    <td style="border-right-color: #fff !important;">Tiempo:
                                        {{ $pack['tiempos'][$servicioIndex] }}h</td>
                                @endif
                                @if (!isset($pack['horas_inicio'][$servicioIndex]))
                                    <td style="border-right-color: #fff !important;">Hora de inicio: n/a</td>
                                @else
                                    <td style="border-right-color: #fff !important;">Hora de inicio:
                                        {{ $pack['horas_inicio'][$servicioIndex] }}</td>
                                @endif
                                @if (!isset($pack['horas_finalizacion'][$servicioIndex]))
                                    <td style="border-right-color: #fff !important;">Hora de finalización: n/a</td>
                                @else
                                    <td style="border-right-color: #fff !important;">Hora de finalización:
                                        {{ $pack['horas_finalizacion'][$servicioIndex] }}</td>
                                @endif
                                @if (!isset($pack['numero_monitores'][$servicioIndex]))
                                    <td>Monitores: 0</td>
                                @else
                                    <td>Monitores:
                                        {{ $pack['numero_monitores'][$servicioIndex] }}</td>
                                @endif
                            </tr>
                        @endforeach
                    @endforeach
                </tbody>
            </table>
        @endif
        @if (isset($listaServicios) && count($listaServicios) > 0)
            <table class="table">
                <thead>
                    <tr width="100%">
                        <th colspan="4">SERVICIOS CONTRATADOS</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($listaServicios as $servicio)
                        <tr width="100%">
                            <td colspan="3" style="border-right-color: #fff !important;"><b>Servicio:
                                </b>{{ $servicio['nombre'] }} </td>
                            <td><b>Importe:</b> {{ $servicio['precio_final'] }} €</td>
                        </tr>
                        <tr width="100%">
                            <td style="border-right-color: #fff !important;"><b>Tiempo:</b>
                                {{ $servicio['tiempo'] }}h</td>
                            <td style="border-right-color: #fff !important;"><b>Hora de inicio:</b>
                                {{ $servicio['hora_inicio'] }}</td>
                            <td style="border-right-color: #fff !important;"><b>Hora de finalización:</b>
                                {{ $servicio['hora_finalizacion'] }}</td>
                            <td style="border-right-color: #fff !important;"><b>Número de monitores:</b>
                                {{ $servicio['numero_monitores'] }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
        <table class="table">
            <thead>
                <tr width="100%">
                    <th>OBSERVACIONES</th>
                </tr>
            </thead>
            <tbody>
                <tr width="100%">
                    <td>{{ $observaciones }}</td>
                </tr>
            </tbody>
        </table>

        <table class="table">
            <thead>
                <tr width="100%">
                    <th colspan="3">RESERVA SERVICIOS CONTRATADOS*</th>
                </tr>
            </thead>
            <tbody>
                <tr width="100%">
                    <td colspan="3" style="border-bottom-color: #fff !important;">Sirva el presente contrato como
                        comprobante del pago en concepto de reserva, de
                        los servicios
                        anteriormente descritos.</td>
                </tr>
                <tr width="100%">
                    <td style="border-right-color: #fff !important; border-top-color: #fff !important;"><b>Total
                            servicios contratados:</b> {{ $presupuesto->precioFinal }} €</td>
                    <td style="border-right-color: #fff !important; border-top-color: #fff !important;"><b>Entrega:</b>
                        {{ $presupuesto->precioFinal * 0.2 }} € (20%)</td>
                    <td style="border-top-color: #fff !important;"><b>Método de pago:</b> {{ $metodoPago }}</td>
                </tr>
                <tr width="100%">
                    <td colspan="3" style="border-bottom-color: #fff !important;">En caso de transferencia:</td>
                </tr>
                <tr width="100%">
                    <td colspan="3"
                        style="border-top-color: #fff !important; border-bottom-color: #fff !important;">
                        ** Nº Cuenta Deutsche Bank ES06-0019-0120-2940-1006-2882</td>
                </tr>
                <tr width="100%">
                    <td colspan="3" style="border-top-color: #fff !important;"> **Nº Cuenta Caixabank
                        ES17-2100-2922-6002-0024-6441 </td>
                </tr>
            </tbody>
        </table>

        <table class="table">
            <thead>
                <tr width="100%">
                    <th>SUMINISTROS LUZ, AGUA y PERMISOS.</th>
                </tr>
            </thead>
            <tbody>
                <tr width="100%" style="border-bottom-color: #fff !important;">
                    <td><b>Será de cargo y cuenta del cliente</b> suministrar tantos puntos de <b>luz y agua</b> como
                        sean necesarios
                        para el correcto funcionamiento de los servicios contratados. Igualmente corresponde al cliente
                        el pago de los recibos/facturas que sean emitidos por el consumo realizado durante el evento,
                        así como <b>los permisos</b>, si fueran necesarios, para la instalación de nuestros servicios en
                        la vía
                        pública.
                    </td>
                </tr>
                <tr width="100%">
                    <td style="border-top-color: #fff !important;"><b>Con la firma del presente contrato el cliente
                            manifiesta haber sido correctamente informado y su
                            aceptación.</b>
                    </td>
                </tr>
            </tbody>
        </table>
        <table class="table">
            <thead>
                <tr width="100%">
                    <th colspan="2">CONSENTIMIENTO TRATAMIENTO DE DATOS DE CARÁCTER PERSONAL</th>
                </tr>
            </thead>
            <tbody>
                <tr width="100%" style="margin-bottom: -5px;">
                    <td colspan="2" style="border-bottom-color: #fff !important;">Identidad del responsable de
                        tratamiento:
                    </td>
                </tr>
                <tr width="100%" style="margin-bottom: -5px;">
                    <td colspan="2"
                        style="border-top-color: #fff !important; border-bottom-color: #fff !important;"><b>EDUCACIÓN,
                            OCIO Y TIEMPO LIBRE LA FABRICA S.L.</b>
                    </td>
                </tr>
                <tr width="100%" style="margin-bottom: -5px;">
                    <td colspan="2"
                        style="border-top-color: #fff !important; border-bottom-color: #fff !important;">B-11949658
                    </td>
                </tr>
                <tr width="100%" style="margin-bottom: -5px;">
                    <td colspan="2"
                        style="border-top-color: #fff !important; border-bottom-color: #fff !important;">Avd. Alcalde
                        Cantos Ropero, 51 Pol. Ind. "Jerez 2000" Nave 14
                    </td>
                </tr>
                <tr width="100%" style="margin-bottom: -5px;">
                    <td colspan="2"
                        style="border-top-color: #fff !important; border-bottom-color: #fff !important;">11408 Jerez de
                        la Frontera (Cádiz)
                    </td>
                </tr>
                <tr width="100%" style="margin-bottom: -5px;">
                    <td width="5%"
                        style="border-right-color: #fff !important; border-top-color: #fff !important; border-bottom-color: #fff !important;">
                        956 042 751
                    </td>
                    <td width="95%"
                        style="border-top-color: #fff !important; border-bottom-color: #fff !important;">673 811 838
                    </td>
                </tr>
                <tr width="100%">
                    <td style="border-right-color: #fff !important; border-top-color: #fff !important;">
                        eventos@fabricandoeventos.com
                    </td>
                    <td style="border-top-color: #fff !important;"></td>
                </tr>
                <tr width="100%">
                    <td colspan="2" style="border-bottom-color: #fff !important;">En cumplimiento del RGPD UE
                        2016/679 de Protección de Datos de Carácter Personal
                        le informamos de que sus datos personales pasarán a formar parte de los sistemas de información
                        de EDUCACIÓN, OCIO Y TIEMPO LIBRE LA FÁBRICA, S.L con la finalidad de gestionar los datos de los
                        clientes de EDUCACIÓN, OCIO Y TIEMPO LIBRE LA FÁBRICA, S.L para su coordinación integral y
                        control, así como el envío de comunicaciones.</td>
                </tr>
                <tr width="100%">
                    <td colspan="2"
                        style="border-top-color: #fff !important; border-bottom-color: #fff !important;">La
                        legitimación del tratamiento se basa en la aplicación del artículo 6.1a del
                        citado RGPD, por la que el interesado otorga a EDUCACIÓN, OCIO Y TIEMPO LIBRE LA FÁBRICA, S.L el
                        consentimiento para el tratamiento de sus datos personales. Los datos que nos ha proporcionado
                        se conservarán mientras no solicite su supresión o cancelación y siempre que resulten adecuados,
                        pertinentes y limitados a lo necesario para los fines para los que sean tratados.</td>
                </tr>
                <tr width="100%">
                    <td colspan="2"
                        style="border-top-color: #fff !important; border-bottom-color: #fff !important;">Sus datos no
                        serán comunicados a terceros salvo en las excepciones previstas por
                        obligaciones legales.</td>
                </tr>
                <tr width="100%">
                    <td colspan="2" style="border-top-color: #fff !important;">Asimismo, le informamos de que las
                        imágenes captadas durante actividades y
                        eventos en los que participe o promueva EDUCACIÓN, OCIO Y TIEMPO LIBRE LA FÁBRICA, S.L podrán
                        ser utilizadas en medios propios (publicaciones, página web, redes sociales…)</td>
                </tr>
                <tr width="100%">
                    <td colspan="2"><b>INDIQUE CON SI/NO LA SIGUIENTE AUTORIZACIÓN:</b></td>
                </tr>
                <tr width="100%">
                    @if ($authImagen == true)
                        <td width="5%"
                            style="border-right-color: #fff !important; border-top-color: #fff !important; border-bottom-color: #fff !important; background-color:yellow !important;">
                            SI</td>
                    @else
                        <td width="5%"
                            style="border-right-color: #fff !important; border-top-color: #fff !important; border-bottom-color: #fff !important; background-color:yellow !important;">
                            NO</td>
                    @endif
                    <td width="95%"
                        style="border-top-color: #fff !important; border-bottom-color: #fff !important;"> Autorizo la
                        captación y difusión de imágenes en medios propios. </td>
                </tr>
                <tr width="100%">
                    @if ($authMenores == 1)
                        <td width="5%"
                            style="border-right-color: #fff !important; border-top-color: #fff !important; border-bottom-color: #fff !important; background-color:yellow !important;">
                            SI</td>
                    @else
                        <td width="5%"
                            style="border-right-color: #fff !important; border-top-color: #fff !important; border-bottom-color: #fff !important; background-color:yellow !important;">
                            NO</td>
                    @endif
                    <td width="95%" style="border-top-color: #fff !important;"> En caso afirmativo, deseo que se
                        muestren los rostros de los menores. </td>
                </tr>
                <tr width="100%">
                    <td colspan="2" style="border-bottom-color: #fff !important;">Podrá ejercitar su derecho a
                        solicitar el acceso a sus datos, la rectificación o
                        supresión, la limitación del tratamiento, la oposición del tratamiento o la portabilidad de los
                        datos, dirigiendo un escrito junto a la copia de su DNI a en la siguiente dirección:
                        <b>info@fabricandoeventos.com</b>
                    </td>
                </tr>
                <tr width="100%">
                    <td colspan="2"
                        style="border-top-color: #fff !important; border-bottom-color: #fff !important;">En caso de
                        disconformidad, Vd. tiene derecho a elevar una reclamación ante la
                        Agencia Española de Protección de Datos (www.agpd.es).</td>
                </tr>
                <tr width="100%" style="border-top-color: #fff !important;">
                    <td colspan="2">He sido informado y autorizo expresamente el tratamiento, con la firma de este
                        contrato. </td>
                </tr>
            </tbody>
        </table>
        <table class="table">
            <thead>
                <tr width="100%">
                    <th colspan="2">FIRMA Y CONFORMIDAD</th>
                </tr>
            </thead>
            <tbody>
                <tr width="100%">
                    <td colspan="2"><b style="text-align: center !important;">{{ $fechaMostrar }}</td>
                </tr>
                <tr width="100%">
                    <td width="50%">Solicitante ({{$cliente->nombre}} {{$cliente->apellido}})</td>
                    <td width="50%">La Fábrica ({{$gestor->name}} {{$gestor->surname}})</td>
                </tr>
                <tr width="100%">
                    <td width="50%"></td>
                    <td width="50%"><img style="text-align: center !important"
                            src="{{ public_path('assets/images/firma_fabrica.png') }}" height="100px"></td>
                </tr>
            </tbody>
        </table>
        <table class="table">
            <tbody>
                <tr width="100%">
                    <td colspan="2"><b style="text-align: center !important;">* En caso de que se suspendiera el evento antes de montar el servicio por causas ajenas a La Fábrica, el adelanto del pago se podrá disfrutar otro día en común acuerdo con el mismo importe total. ** Contrato no válido sin justificante bancario.</td>
                </tr>
            </tbody>
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
