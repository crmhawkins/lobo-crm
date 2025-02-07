<div class="container-body">


    <style>
        @page {
            size: A4;
            margin: 0mm;
        }
        canvas {
            border: 1px solid black;

        }
        .lado-izquierdo {
            position: fixed;
            top: 0;
            bottom: 0;
            left: 0;
            width: 50px;
            padding: 10px;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .lado-izquierdo > p {
            writing-mode: vertical-rl;
            transform: rotate(180deg);
            font-size: 10px;
            text-align: center;
        }

        article {
            margin-left: 60px; /* Deja espacio para la barra lateral */
            padding: 20px;
        }

        .container-body {
            font-size: 18px;
            margin: 0;
            padding: 0;
            width: 210mm;
            height: 297mm;
        }

        .header {
            background-color: #003366;
            color: white;
            padding: 10px;
            text-align: center;
            font-size: 10px;
            line-height: 1.2;
        }

        h1 {
            font-size: 16px;
            text-align: center;
            margin: 20px 0;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
        }

        th, td {
            border: 1px solid #000;
            padding: 5px;
            font-size: 12px;
        }

        th {
            background-color: #f2f2f2;

        }

        .signature-table {
            margin-top: 50px;
        }

        .signature-line {
            border-top: 1px solid #000;
            width: 80%;
            margin: 0 auto;
        }
        #productosLoboTable > tbody > tr > td > input , #productosOtrosTable > tbody > tr > td > input{
            width: 80%;
        }

        #productosLoboTable > tbody > tr > td:nth-child(1), #productosOtrosTable > tbody > tr > td:nth-child(1){
            width:  200px;
        }




        .container-global {
            padding: 0mm 6mm;
        }

        .footer {
            position: fixed;
            bottom: 0;
            width: 100%;
            height: 30px;
            text-align: center;
            font-size: 10px;
            color: #555;
        }

        .container-comercial{
            display: flex;
            justify-content: end;
        }

        .comercial{
            width: 45%;
        }
        .add-row , .buttonSave {
            background-color: #4CAF50;
            color: white;
            padding: 5px 10px;
            border: none;
            cursor: pointer;
            border-radius: 2px;
        }

        .remove-row {
            background-color: #f44336;
            color: white;
            padding: 5px 10px;
            border: none;
            cursor: pointer;
            border-radius: 2px;

        }

        @media print {
            .container-body {

            width: 100%;
            height: 100%;
        }
        .btn{
            display: none;
        }

        textarea{
            border: 0px;
            resize: none;

        }
        #productosLoboTable{
            margin-top: 200px;
        }
        input{
            border: 0px;
        }

            .remove-row, .add-row, .accion-col , .eliminar {
                display: none;
            }
            .lado-izquierdo {
                position: fixed;
                top: 0;
                bottom: 0;
                left: 0;
                width: 50px;
                padding: 10px;
            }
            canvas {
            border: 0px solid black;
            width: 100%;
            height: 150px;
            }
            .limpiar{
                display: none;
            }
            .footer {
                position: relative;
                width: 100%;
                height: 30px;
                display: block;
                font-size: 10px;
                text-align: center;
                color: #555;
                margin-top: 20px;
            }

            select {
                border: 0px;
                background: none;
                -webkit-appearance: none; /* Chrome, Safari, Opera */
                -moz-appearance: none; /* Firefox */
                appearance: none; /* Modern browsers */
                outline: none; /* Remove the focus outline */
                pointer-events: none; /* Disable interaction */
            }

            input[type="date"] {
                width: 100px;
                appearance: none;
                -webkit-appearance: none; /* Safari & Chrome */
                -moz-appearance: textfield; /* Firefox */
                border: none; /* Remove border */
                background-color: transparent;
                pointer-events: none; /* Disable interaction */
            }

            .dia, .anio {
                width: 30px;
            }


        }
    </style>



        <article>
            <div class="lado-izquierdo">
                <p>LOBO DEL SUR S.L – domicilio social: Avd. Caetaria 4.5 P.I La Menacha 11205, Algeciras (Cádiz) – R. M. De Cádiz 2 Hoja CA-59264, Folio 205, Libro 0 de sociedades, Inscripción 1º N.I.F. B-1691428</p>
            </div>
            <div class="container-global">

                <img src="{{ asset('images/logo.png') }}" style="width: 20% !important; margin-top: 20px;">
                <h1>ACUERDO COMERCIAL LOBO-LA OFICINA</h1>
                <div class="container-comercial">
                    <table class="table comercial">

                        <tbody>
                            <tr>
                                <td>COMERCIAL</td>
                                @if(Auth::user()->isAdmin())
                                    <td>
                                        <select name="" id="" wire:model="comercial_id">
                                            <option value="">---SELECCIONE UN COMERCIAL---</option>
                                            @foreach ($comerciales as $comercial)
                                                <option value="{{$comercial->id}}">{{$comercial->name}} {{$comercial->surname}}</option>
                                            @endforeach
                                        </select>
                                    </td>
                                @else
                                    <td> {{$user->name}} {{$user->surname}} </td>

                                @endif
                            </tr>
                            <tr>

                                <td>N.º ACUERDO</td>
                                <td><input type="text" style="width: 98%" wire:model="nAcuerdo" value={{$nAcuerdo}}></td>
                            </tr>
                            <tr>
                                <td>TIPO ACUERDO</td>
                                <td>COMERCIAL</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <p><strong>REUNIDOS:</strong></p>
                <p>De una parte,{{$user->name}} {{$user->surname}} , en representación de LOBO DEL SUR S.L con CIF B16914285 domiciliada en Avd. Caetaria 4.5 P.I La Menacha, Algeciras (Cádiz) 11205 y de otra parte el cliente:</p>


                <div style="position: relative;">
                    <table class="client-table">
                        <tr>
                            <th>Nombre y Apellidos/ Razón Social:</th>
                            <th>DNI/ CIF:</th>
                        </tr>
                        <tr>
                            <td><input type="text" style="width: 98%" name="nombre_empresa" wire:model="cliente.nombre"></td>
                            <td><input type="text" style="width: 98%" name="cif_empresa" wire:model="cliente.cif"></td>
                        </tr>
                        <tr>
                            <th>Nombre representante:</th>
                            <th>DNI:</th>
                        </tr>
                        <tr>
                            <td><input style="width: 98%" class="nombre" type="text" wire:model="nombre"></td>
                            <td><input style="width: 98%" class="dni" type="text" wire:model="dni"></td>
                        </tr>
                        <tr>
                            <th colspan="2">Domicilio:</th>
                        </tr>
                        <tr>
                            <td colspan="2"><input type="text" name="domicilio" style="width: 98%"  wire:model="cliente.direccion"></td>
                        </tr>
                        <tr>
                            <th>Mail:</th>
                            <th>Teléfono:</th>
                        </tr>
                        <tr>
                            <td><input type="text" name="email" style="width: 98%" wire:model="cliente.email"></td>
                            <td><input type="text" name="telefono" style="width: 98%" wire:model="cliente.telefono"></td>
                        </tr>
                        <tr>
                            <th colspan="2">Establecimientos:</th>
                        </tr>
                        <tr>
                            <td colspan="2"><input name="establecimientos" style="width: 98%" type="text" wire:model="establecimientos"></td>
                        </tr>
                        <tr>
                            <th colspan="2">Domicilios de los establecimientos:</th>
                        </tr>
                        <tr>
                            <td colspan="2"><input style="width: 98%" name="domicilios_establecimientos" type="text" wire:model="domicilios_establecimientos"></td>
                        </tr>
                    </table>
                </div>

                <p class="acuerdan" ><strong>ACUERDAN:</strong></p>
                <ol>
                    <li>Que en el periodo que transcurrirá entre el día <input name="fecha_inicio" type="date" wire:model="fecha_inicio">  y el día <input name="fecha_fin"  type="date" wire:model="fecha_fin">, adquiere el compromiso de compra de los siguientes productos con las aportaciones correspondientes:</li>
                </ol>
                <div style="position: relative;">
                    <table id="productosLoboTable">
                        <tr>
                            <th>REF.</th>
                            <th>VOL. ANUAL</th>
                            <th>APORTACIÓN DIRECTA</th>
                            <th>RAPPEL</th>
                            <th>TOTAL</th>
                            <th class="accion-col">Accion</th>
                        </tr>
                        <tr>
                            <td colspan="5" style="text-align: center; font-weight: bold;">LOBO</td>
                        </tr>

                        @foreach ($productos_lobo as $index => $producto)
                            <tr>
                                <td><input type="text" wire:model="productos_lobo.{{ $index }}.ref" /></td>
                                <td><input type="text" wire:model="productos_lobo.{{ $index }}.vol_anual" /></td>
                                <td><input type="text" wire:model="productos_lobo.{{ $index }}.aportacion_directa" /></td>
                                <td><input type="text" wire:model="productos_lobo.{{ $index }}.rappel" /></td>
                                <td><input type="text" wire:model="productos_lobo.{{ $index }}.total" /></td>
                                <td class="eliminar"><button type="button" class="remove-row" wire:click="deleteProductLobo({{ $index }})">Eliminar</button></td>

                            </tr>
                        @endforeach

                    </table>
                </div>
                <button type="button" class="add-row mb-2" style="margin-bottom: 20px;" wire:click="addProductLobo" >Agregar Producto</button>

                <table id="productosOtrosTable">

                    <tr>
                        <td colspan="5" style="text-align: center; font-weight: bold;">OTROS</td>
                    </tr>

                    @foreach ($productos_otros as $index => $producto)
                        <tr>
                            <td><input type="text" wire:model="productos_otros.{{ $index }}.ref" /></td>
                            <td><input type="text" wire:model="productos_otros.{{ $index }}.vol_anual" /></td>
                            <td><input type="text" wire:model="productos_otros.{{ $index }}.aportacion_directa" /></td>
                            <td><input type="text" wire:model="productos_otros.{{ $index }}.rappel" /></td>
                            <td><input type="text" wire:model="productos_otros.{{ $index }}.total" /></td>
                            <td class="eliminar"><button type="button" class="remove-row" wire:click="deleteProductOtros({{ $index }})">Eliminar</button></td>

                        </tr>
                    @endforeach
                </table>
                <button type="button" class="add-row" wire:click="addProductOtros">Agregar Otro Producto</button>

                <p class="small-text">*Volumen de LOBO en botellas.</p>


                <table>
                    <thead>
                        <th  style="text-align: center;"> APORTACIONES MATERIAL PLV- MARKETING </th>

                    </thead>
                    <tbody>
                        <tr>
                            <td>LOBO</td>
                        </tr>
                        <tr>
                            <td>
                                <textarea name="" id="" cols="30" rows="10" style="width: 98%" name="marketing" wire:model="marketing"></textarea>
                            </td>
                        </tr>
                    </tbody>
                </table>


                <p><strong>Condiciones:</strong></p>
                <ul class="indent">
                    <li>Las liquidaciones de aportaciones económicas serán abonadas una vez cumplido el acuerdo. Mientras que las promociones se aplicarán directamente en los pedidos.</li>
                    <li>El material se le entregará en el primer pedido o a la firma del acuerdo, al finalizar el mismo, en caso de no haberse cumplido se le descontará el precio del material del abono de liquidación en la parte proporcional.</li>
                    <li>En los acuerdos con activaciones, se realizarán a los 3 meses del inicio del acuerdo.</li>
                </ul>

                <ol start="2">
                    <li>La aportación económica acordada en el punto 1, así como las aportaciones de mercadería y marketing, se llevará a cabo tal y como se deje reflejado en el apartado de observaciones, una vez se haya demostrado el compromiso de unidades mínimas establecidas.</li>
                    <li>Para el pago de las aportaciones acordadas con el cliente, tiene que haberse alcanzado un mínimo del 90% de acuerdo, de no ser así, se ampliará el periodo o cancelará el acuerdo dependiendo del consumo que tenga en el momento de finalización de este. El pago de</li>
                    <li>El cliente adquiere el compromiso de destacar en los botelleros de sus recintos los productos de la marca, así como de incluirlos en campañas publicitarias y redes sociales, en el caso de zonas reservadas para la marca, se deberá generar un contenido gráfico especial.</li>
                    <li>Se venderá directamente desde la marca al cliente recogido en este contrato, estableciéndose un vencimiento de pago de 30 días.</li>
                </ol>

                <table>
                    <thead>
                        <tr>
                            <th>OBSERVACIONES:</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>
                                <textarea  name="" id="" cols="30" rows="10" style="width: 98%" name="observaciones" wire:model="observaciones"></textarea>
                            </td>
                        </tr>
                    </tbody>
                </table>



                <p>De conformidad con lo anteriormente expuesto, se firma por las partes a fecha de <input type="text" class="dia"  wire:model="dia_firma"> de <select wire:model="mes_firma">
                    <option value="">---SELECCIONE UN MES---</option>
                    <option value="Enero">Enero</option>
                    <option value="Febrero">Febrero</option>
                    <option value="Marzo">Marzo</option>
                    <option value="Abril">Abril</option>
                    <option value="Mayo">Mayo</option>
                    <option value="Junio">Junio</option>
                    <option value="Julio">Julio</option>
                    <option value="Agosto">Agosto</option>
                    <option value="Septiembre">Septiembre</option>
                    <option value="Octubre">Octubre</option>
                    <option value="Noviembre">Noviembre</option>
                    <option value="Diciembre">Diciembre</option>
                </select> de <input type="text"  class="anio" wire:model="anio_firma"></p>

                <p style="margin-top: 20px !important;"><strong>FIRMADO:</strong></p>
                <table class="signature-table">
                    <thead>
                        <tr>
                            <th>Director Comercial<br>LOBO</th>
                            <th>Comercial</th>
                            <th>Cliente</th>
                            <th>Distribuidor</th>
                        </tr>
                    </thead>

                    <tr>
                        <td>
                            <canvas id="firma_comercial_lobo"></canvas>
                                <button class="limpiar" type="button" onclick="limpiarCanvas('firma_comercial_lobo')">Limpiar</button>
                                <input type="hidden" name="firma_comercial_lobo_data" id="firma_comercial_lobo_data" >
                            Iván Ruiz Pecino
                        </td>
                        <td>
                                <canvas id="firma_comercial"></canvas>
                                <button class="limpiar" type="button" onclick="limpiarCanvas('firma_comercial')">Limpiar</button>
                                <input type="hidden" name="firma_comercial_data" id="firma_comercial_data">
                            ........................
                        </td>
                        <td>
                                <canvas id="firma_cliente"></canvas>
                                <button class="limpiar" type="button" onclick="limpiarCanvas('firma_cliente')">Limpiar</button>
                                <input type="hidden" name="firma_cliente_data" id="firma_cliente_data">
                            ........................
                        </td>
                        <td>
                            <canvas id="firma_distribuidor"></canvas>
                                <button class="limpiar" type="button" onclick="limpiarCanvas('firma_distribuidor')">Limpiar</button>
                                <input type="hidden" name="firma_distribuidor_data" id="firma_distribuidor_data">
                            ...............................
                        </td>
                    </tr>
                </table>
                {{-- <button class="btn btn-primary buttonSave" type="button" wire:click="saveAcuerdoComercial()">Guardar Acuerdo Comercial</button> --}}
                <button class="btn btn-primary buttonSave" type="button" onclick="guardarFirmas(); @this.saveAcuerdoComercial()">Guardar Acuerdo Comercial</button>

                <footer class="footer" style="font-size: 18px;">
                    <p>DEPARTAMENTO COMERCIAL</p>
                    <p>www.serlobo.com</p>
                </footer>
            </div>
        </article>
    {{-- <div class="lado-izquierdo">
        <p>LOBO DEL SUR S.L – domicilio social: Avd. Caetaria 4.5 P.I La Menacha 11205, Algeciras (Cádiz) – R. M. De Cádiz 2 Hoja CA-59264, Folio 205, Libro 0 de sociedades, Inscripción 1º N.I.F. B-1691428</p>
    </div> --}}
    @livewireScripts
    <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            window.addEventListener('acuerdoGuardado', function () {
                Swal.fire({
                    icon: 'success',
                    title: '¡Éxito!',
                    text: 'El acuerdo comercial ha sido creado correctamente.',
                    confirmButtonText: 'OK'
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Aquí usamos el id del cliente desde PHP con Blade
                        const clienteId = {{ $cliente['id'] }};
                        window.location.href = `/admin/comercial/edit/${clienteId}`; // Redirige a la ruta de edición del cliente con el ID
                    }
                });
            });
        });
    </script>
    <script>
        // Inicializar canvas para capturar firmas
        const canvases = ['firma_comercial_lobo', 'firma_comercial', 'firma_cliente', 'firma_distribuidor'];

        canvases.forEach(id => {
            const canvas = document.getElementById(id);
            const ctx = canvas.getContext('2d');
            let dibujando = false;
            let x = 0;
            let y = 0;

            canvas.addEventListener('mousedown', function(e) {
                dibujando = true;
                const rect = canvas.getBoundingClientRect();
                x = e.clientX - rect.left;
                y = e.clientY - rect.top;
                ctx.beginPath();
                ctx.moveTo(x, y);
            });

            canvas.addEventListener('mousemove', function(e) {
                if (dibujando) {
                    const rect = canvas.getBoundingClientRect();
                    x = e.clientX - rect.left;
                    y = e.clientY - rect.top;
                    ctx.lineTo(x, y);
                    ctx.stroke();
                }
            });

            canvas.addEventListener('mouseup', function(e) {
                if (dibujando) {
                    dibujando = false;
                    ctx.closePath();
                }
            });

            canvas.addEventListener('mouseleave', function(e) {
                if (dibujando) {
                    dibujando = false;
                    ctx.closePath();
                }
            });

            // Soporte para pantallas táctiles
            canvas.addEventListener('touchstart', function(e) {
                e.preventDefault();
                dibujando = true;
                const rect = canvas.getBoundingClientRect();
                x = e.touches[0].clientX - rect.left;
                y = e.touches[0].clientY - rect.top;
                ctx.beginPath();
                ctx.moveTo(x, y);
            });

            canvas.addEventListener('touchmove', function(e) {
                if (dibujando) {
                    e.preventDefault();
                    const rect = canvas.getBoundingClientRect();
                    x = e.touches[0].clientX - rect.left;
                    y = e.touches[0].clientY - rect.top;
                    ctx.lineTo(x, y);
                    ctx.stroke();
                }
            });

            canvas.addEventListener('touchend', function(e) {
                if (dibujando) {
                    dibujando = false;
                    ctx.closePath();
                }
            });
        });

        function limpiarCanvas(id) {
            const canvas = document.getElementById(id);
            const ctx = canvas.getContext('2d');
            ctx.clearRect(0, 0, canvas.width, canvas.height);
        }

        function guardarFirmas() {
            const canvases = ['firma_comercial_lobo', 'firma_comercial', 'firma_cliente', 'firma_distribuidor'];

            canvases.forEach(id => {
                const canvas = document.getElementById(id);
                const dataUrl = canvas.toDataURL(); // Obtener la firma en formato base64
                @this.set(id + '_data', dataUrl); // Enviar la firma a Livewire
            });
}
    </script>
    <script>
        let loboCount = 1;
        let otrosCount = 1;

        function agregarFila() {
            const table = document.getElementById('productosLoboTable').getElementsByTagName('tbody')[0];
            const newRow = `
                <tr>
                    <td><input type="text" wire:model="productos_lobo.${loboCount}.ref"></td>
                    <td><input type="text" wire:model="productos_lobo.${loboCount}.vol_anual"></td>
                    <td><input type="text" wire:model="productos_lobo.${loboCount}.aportacion_directa"></td>
                    <td><input type="text" wire:model="productos_lobo.${loboCount}.rappel"></td>
                    <td><input type="text" wire:model="productos_lobo.${loboCount}.total"></td>
                    <td class="eliminar" ><button type="button" class="remove-row" onclick="eliminarFila(this)">Eliminar</button></td>
                </tr>`;
                table.insertAdjacentHTML('beforeend', newRow);
                loboCount++;
        }

        function agregarFilaOtros() {
            const table = document.getElementById('productosOtrosTable').getElementsByTagName('tbody')[0];
            const newRow = `
                <tr>
                    <td><input type="text" wire:model="productos_otros.${otrosCount}.ref"></td>
                    <td><input type="text" wire:model="productos_otros.${otrosCount}.vol_anual"></td>
                    <td><input type="text" wire:model="productos_otros.${otrosCount}.aportacion_directa"></td>
                    <td><input type="text" wire:model="productos_otros.${otrosCount}.rappel"></td>
                    <td><input type="text" wire:model="productos_otros.${otrosCount}.total"></td>
                    <td class="eliminar"><button type="button" class="remove-row" onclick="eliminarFila(this)">Eliminar</button></td>
                </tr>`;
            table.insertAdjacentHTML('beforeend', newRow);
            otrosCount++;
        }

        function eliminarFila(button) {
            const row = button.parentElement.parentElement;
            row.remove();
        }


    </script>
    <script>
        function guardarTabla() {
            const productosLoboData = [];
            const productosOtrosData = [];

            // Recoger datos de productos LOBO
            document.querySelectorAll('#productosLoboTable tbody tr').forEach(row => {
                const producto = {
                    ref: row.querySelector('input[name^="productos_lobo["][name$="[ref]"]').value,
                    vol_anual: row.querySelector('input[name^="productos_lobo["][name$="[vol_anual]"]').value,
                    aportacion_directa: row.querySelector('input[name^="productos_lobo["][name$="[aportacion_directa]"]').value,
                    rappel: row.querySelector('input[name^="productos_lobo["][name$="[rappel]"]').value,
                    total: row.querySelector('input[name^="productos_lobo["][name$="[total]"]').value
                };
                productosLoboData.push(producto);
            });

            // Recoger datos de productos OTROS
            document.querySelectorAll('#productosOtrosTable tbody tr').forEach(row => {
                const producto = {
                    ref: row.querySelector('input[name^="productos_otros["][name$="[ref]"]').value,
                    vol_anual: row.querySelector('input[name^="productos_otros["][name$="[vol_anual]"]').value,
                    aportacion_directa: row.querySelector('input[name^="productos_otros["][name$="[aportacion_directa]"]').value,
                    rappel: row.querySelector('input[name^="productos_otros["][name$="[rappel]"]').value,
                    total: row.querySelector('input[name^="productos_otros["][name$="[total]"]').value
                };
                productosOtrosData.push(producto);
            });

            // Guardar los valores serializados en los campos ocultos
            document.getElementById('productos_lobo_data').value = JSON.stringify(productosLoboData);
            document.getElementById('productos_otros_data').value = JSON.stringify(productosOtrosData);
        }
    </script>
</div>
