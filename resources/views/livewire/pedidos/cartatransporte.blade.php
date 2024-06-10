<div class="container-fluid">
    @livewireScripts
<style>
    h1{
        border: 1px solid black;
        font-size: 20px;
        
        padding: 5px 20px;
        margin-bottom: 0px;
    }
    .header{
        width: 70%;
    }
    .header > small{
        font-size: 8px;
        font-weight: bold;
        text-align: center;
        width: 100%;
        display: block;
        height: 20%;
    }
    .container-fluid{
        max-width: 210mm;
        width: 210mm;
        min-width: 210mm;
        min-height: 297mm;
        height: 297mm;
        max-height: 297mm;
        margin: 0 auto;
        background: #fdfcfc;
    }
    article{
        display: grid;
        grid-template-columns: 20px 1fr 20px;
        margin-top: 20px;
        height: 90%;
        width: 100%; 
    }

    aside{
        display: flex;
        align-items: center;
        width:10%;
    }
    aside>small{
        display: block;
        
        text-align: center;
        writing-mode: vertical-rl;
        transform: rotate(180deg);
        font-size: 15px;
    }

    .aside-2>small{
        transform: rotate(360deg);
    }

    .contenedorprincipal{
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        /* grid-template-rows: 90px 250px 150px 100px 200px 100px; */
        grid-column-gap: 0px;
        grid-row-gap: 0px;
        border: 1px solid black;
        height: 100%;
        width: 100%;
    }

    
   
    .container>header{
        display: grid;
        grid-template-columns: 50% 50%;
    }

    .container>header>div>h2{
        margin: 0px;
        font-size:14px;
        text-align: center;
    }
    #remitente, #instrucciones{
       
        position: relative;

    }
    #remitente , #operador , #instrucciones{
        border-bottom: 1px solid;
        padding: 5px;
    }
    #remitente, #consignatario, #sietefilas ,#instrucciones , #formapago{
        border-right: 1px solid;
        border-bottom: 1px solid;
        display: grid;
        grid-template-columns: 10px 1fr;
    }

    #instrucciones{
        grid-template-columns: 20px 1fr;
        grid-template-rows: 20px 1fr;   
    }
    #sietefilas{
        border-right: 0px;
        display: grid;
        grid-template-columns: repeat(7, 1fr);
    }
    #lugarentrega{
       border: 3px solid;
       
    }
    .numero{
        font-weight: bold;
    }
    .flex-column{
        display: flex;
        flex-direction: column;
    }
    .font-size-small{
        font-size:12px;
    }
    .margin-0{
        margin: 0px;
    }

    #consignatario , #lugarentrega, #formapago{
        display: grid;
        grid-template-columns: 1fr;
        
    
    }
    
    #consignatario>div , #lugarentrega>div , #formapago>div{
        padding: 5px;
        display: grid;
        grid-template-columns: 10px 1fr;
        border-bottom: 1px solid;
        gap:0px 10px;

    }

    #formapago>div{
        grid-template-rows: 10px 10px 10px;
        gap:10px;
    }


    #sietefilas>div{
        padding: 5px;
        display: grid;
        grid-template-columns: 20px 1fr;
        grid-template-rows: 30px 1fr;
        border-right: 1px solid;
    }
    #sietefilas>div:last-child{
        border-right: 0px;
    }
    
    #consignatario>div:last-child , #lugarentrega>div:last-child , #formapago>div:last-child{
        border-bottom: 0px solid;
    }
    #sietefilas{
        grid-area: 3 / 1 / 4 / 3;
    }

    .no-border-right{
        border-right: 0px !important;
    
    }

    #tabla>div:first-child{
        display: grid;
        grid-template-columns: repeat(4, 100px);
        /* grid-template-rows: repeat(4, 1fr); */
        grid-column-gap: 0px;
        grid-row-gap: 0px;
    }
    #tabla{
        display: grid;
        grid-template-columns: 1fr;
        border-top: 1px solid;
    
    
    }

    #tabla>div:last-child{
        display: grid;
        grid-template-columns: 20px 1fr;
        grid-column-gap: 0px;
        grid-row-gap: 0px;
    }

    #tabla>div>div{
        border-bottom: 1px solid;
        border-right: 1px solid;
        text-align: center;
    }
    #tabla>div>div:last-child , #tabla>div>div:first-child{
        border-right: 0px;
    }

    #firmas{ 
        grid-area: 6 / 1 / 7 / 3; 
    }

    @media print{
        .btn{
            display: none;
        }

        textarea{
            border: 0px;
            resize: none;

        }

        input{
            border: 0px;
        }

    }

    
</style>
    <div>
        <button class="btn" wire:click="save" @if($firma_transportista == null) id="save-png-button" @endif>Guardar</button>
        <button class="btn btn-primary" wire:click="eliminar">Eliminar</button>
        <button class="btn btn-warning" onClick="window.print()">Imprimir</button>
    </div>
    
    <header class="header">
        <div> 
            <h1 style="text-align: center;">DOCUMENTO DE CONTROL DE LOS ENVÍOS DE TRANSPORTE PÚBLICO DE MERCANCÍAS</h1>
        </div>
        <small>DOCUMENTO DE CONTROL: Orden FOM/2861/2012 de 13 de diciembre (BOE 5 de enero de 2013), que deroga la orden FOM 238/2003</small>
    </header>
    <article>
        <aside>
            <small>A rellenar bajo la responsabilidad del remitente 1-15 &nbsp; &nbsp; ambos inclusive y 19+21+22 &nbsp; &nbsp; &nbsp; &nbsp; Los recuadros en linea gruesa deben ser rellenados por el portador</small>
        </aside>
        <div class="container">
            <header>
                <div></div>
                <div><h2>CARTA DE PORTE NACIONAL</h2></div>           
            </header>
            <div class="contenedorprincipal">
                <div id="remitente">
                    <div class="numero"><span>1</span></div>
                    <div class="flex-column">
                        <label class="font-size-small">
                            <input type="checkbox" wire:model="remitente" />Remitente(nombre,CIF,domicilio país)
                        </label>
                        <label class="font-size-small">
                            <input type="checkbox" wire:model="cargador_contractual"/>Cargador Contractual(nombre, domicilio y CIF o DNI)
                        </label>
                    </div>
                    <div></div>
                    <div>
                        <textarea style="width: 80%;" wire:model="remitente_1"></textarea>
                    </div>
                </div>
                <div id="operador">
                    <div><p class="font-size-small margin-0">Operador de transporte(nombre, CIF, domicilio, país)</p></div>
                    <div style="height: 100%;">
                        <textarea wire:model="operador_transporte" style="width: 80%; margin-top: 10px;"></textarea>   
                    </div>
                </div>
                <div id="consignatario">
                    <div>
                        <div class="numero"><span>2</span></div>
                        <div><p class="font-size-small margin-0">Consignatario(nombre, CIF, domicilio)</p></div>
                        <div></div>
                        <div style="height: 100%;">
                            <textarea wire:model="consignatario" style="width: 80%; "></textarea>   
                        </div>
                    </div>
                    <div>
                        <div class="numero"><span>3</span></div>
                        <div><p class="font-size-small margin-0">Lugar de entrega de la mercancía (lugar, país)</p></div>
                        <div></div>
                        <div style="height: 100%;">
                            <input type="text" style="width: 80%;"  wire:model="lugar_entrega"/>
                        </div>
                    </div>
                    <div>
                        <div class="numero"><span>4</span></div>
                        <div><p class="font-size-small margin-0">Lugar y fecha de carga de la mercancía (lugar, país)</p></div>
                        <div></div>
                        <div style="height: 100%;">
                            <input type="text" style="width: 80%;"  wire:model="lugar_fecha_carga"/>   
                        </div>
                    </div>
                    <div>
                        <div class="numero"><span>5</span></div>
                        <div><p class="font-size-small margin-0">Documentos anexos</p></div>
                        <div></div>
                        <div style="height: 100%;">
                            <textarea style="width: 80%;" wire:model="documentos_anexos"></textarea>   
                        </div>
                    </div>
                </div>
                <div id="lugarentrega">
                    <div>
                        <div class="numero"><span>16</span></div>
                        <div><p class="font-size-small margin-0">Lugar de entrega de la mercancía (lugar, país)</p></div>
                        <div></div>
                        <div style="height: 100%;">
                            <input type="text" style="width: 80%;" wire:model="lugar_entrega_16" />
                        </div>
                    </div>
                    <div>
                        <div><p class="font-size-small margin-0">Vehículo</p></div>
                        <div></div>
                        <div style="height: 100%;">
                            <input type="text"  wire:model="vehiculo" />   
                        </div>
                    </div>
                    <div>
                        <div class="numero"><span>17</span></div>
                        <div><p class="font-size-small margin-0">Porteadores sucesivos (nombre, CIF, domicilio, país)</p></div>
                        <div></div>
                        <div style="height: 100%;">
                            <input type="text" style="width: 80%; margin-top: 5px;" wire:model="porteadores_sucesivos" />   
                        </div>
                    </div>
                    <div>
                        <div class="numero"><span>18</span></div>
                        <div><p class="font-size-small margin-0">Reservas y observaciones del porteador</p></div>
                        <div></div>
                        <div style="height: 100%; font-weight: bold; text-align:center; font-size:11px;" class="font-size-small margin-0">
                            LAS PARTES INTERVINIENTES EN ESTE CONTRATO CON RENUNCIA A SU PROPIO FUERO, SE SOMENTE EXPRESAMENTE A LA JUNTA ARBITRAL DEL TRANSPORTE DE ESTA PROVINCIA CUALQUIERA QUE SEA LA CUANTÍAS DE LA CONTROVERSIA   
                        </div>
                    </div>
                </div>
                <div id="sietefilas">
                    <div>
                        <div class="numero"><span>6</span></div>
                        <div><p class="font-size-small margin-0">Marcas y números</p></div>
                        <div></div>
                        <div style="height: 100%;">
                            <textarea style="width: 80%; height: 90%;" wire:model="marca_numeros" ></textarea>  
                        </div>
                    </div>
                    <div>
                        <div class="numero"><span>7</span></div>
                        <div><p class="font-size-small margin-0">Número de bultos</p></div>
                        <div></div>
                        <div style="height: 100%;">
                            <textarea style="width: 80%; height: 90%;" wire:model="numero_bultos" ></textarea>  
                        </div>
                    </div>
                    <div>
                        <div class="numero"><span>8</span></div>
                        <div><p class="font-size-small margin-0">Clases de embalaje</p></div>
                        <div></div>
                        <div style="height: 100%;">
                            <textarea style="width: 80%; height: 90%;" wire:model="clases_embalaje" ></textarea>  
                        </div>
                    </div>
                    <div>
                        <div class="numero"><span>9</span></div>
                        <div><p class="font-size-small margin-0">Naturaleza de la mercancía</p></div>
                        <div></div>
                        <div style="height: 100%;">
                            <textarea style="width: 80%; height: 90%;" wire:model="naturaleza"></textarea>  
                        </div>
                    </div>
                    <div>
                        <div class="numero"><span>10</span></div>
                        <div><p class="font-size-small margin-0">Nº Estadístico</p></div>
                        <div></div>
                        <div style="height: 100%;">
                            <textarea style="width: 80%; height: 90%;" wire:model="n_estadistico" ></textarea>  
                        </div>
                    </div>
                    <div>
                        <div class="numero"><span>11</span></div>
                        <div><p class="font-size-small margin-0">Peso bruto kgs.</p></div>
                        <div></div>
                        <div style="height: 100%;">
                            <textarea style="width: 80%; height: 90%;" wire:model="peso_bruto" ></textarea>  
                        </div>
                    </div>
                    <div>
                        <div class="numero"><span>12</span></div>
                        <div><p class="font-size-small margin-0" >Volumen m<sup>3</sup></p></div>
                        <div></div>
                        <div style="height: 100%;">
                            <textarea style="width: 80%; height: 90%;" wire:model="volumen" ></textarea>  
                        </div>
                    </div>
                </div>
                <div id="instrucciones">
                    <div class="numero"><span>13</span></div>
                    <div><p class="font-size-small margin-0">Instrucciones del remitente</p></div>
                    <div></div>
                    <div style="height: 100%;">
                        <textarea style="width: 80%; height: 90%;" wire:model="instrucciones" ></textarea>  
                    </div>
                </div>
                <div class="no-border-right" id="instrucciones">
                    <div class="numero"><span>13</span></div>
                    <div><p class="font-size-small margin-0">Estipulaciones particulares</p></div>
                    <div></div>
                    <div style="height: 100%; font-weight: bold; text-align:center; font-size:11px;" class="font-size-small margin-0">
                        LAS PARTES INTERVINIENTES EN ESTE CONTRATO SE SOMENTEN EXPRESAMENTE A LA JUNTA ARBITRAL DEL TRANSPORTE DE ESTA PROVINCIA, INCLUSO EN CONTROVERSIAS QUE EXCEDAN DE 3.000€
                    </div>
                </div>
                <div id="formapago">
                    <div></div>
                    <div>
                        <div class="numero" ><span>14</span></div>
                        <div><p class="font-size-small margin-0" >Forma de pago</p></div>
                        <div></div>
                        <label class="font-size-small">
                            <input type="checkbox" id="porte_pagado" wire:model="porte_pagado" />
                               Porte pagado
                        </label>
                        <div></div>
                        <label class="font-size-small">
                                <input type="checkbox" id="porte_debido"  wire:model="porte_debido" />
                               Porte debido
                        </label>
                    </div>
                    <div>
                        <div class="numero"><span>15</span></div>
                        <div><p class="font-size-small margin-0">Formalizado en </p> <input type="text" wire:model="formalizado" /> 200</div>
                    </div>
                </div>
                <div id="tabla" style="border-top:3px solid; border-left: 3px solid; border-right: 3px solid;">
                    <div> 
                        <div>
                            <div></div>
                            <div style="display:grid; grid-template-columns: 20px 1fr;">
                                <div><div class="numero"><span>20</span></div></div>
                                <div><p class="font-size-small margin-0">A pagar por: </p></div>
                            </div>
                            
                        </div>
                        <div>
                            <p  class="font-size-small margin-0" style="font-weight:bold">Remitente</p>
                            <input type="text" style="width: 70%" wire:model="remitente_tabla" />
                        </div>
                        <div>
                            <p class="font-size-small margin-0" style="font-weight:bold">Moneda</p>
                            <input type="text" style="width: 70%" wire:model="moneda_tabla"/>
                        </div>
                        <div>
                            <p class="font-size-small margin-0" style="font-weight:bold">Consignatario</p>
                            <input type="text" style="width: 70%" wire:model="consignatario_tabla"/>
                        </div>
                        <div style="text-align: start; padding:5px; display:grid; ">
                            <p class="font-size-small margin-0">Precio del transporte</p>
                            <p class="font-size-small margin-0" >Descuentos</p>
                        </div>
                        <div style="display:grid; grid-template-rows:1fr 1fr; align-items:center; justify-content:center;">
                           <input type="text" style="width: 80%; height:10px; margin: 0 auto; " wire:model="precio_remitente" />
                            <input type="text" style="width: 80%; height:10px; margin: 0 auto;" wire:model="descuento_remitente" />
                        </div>
                        <div style="display:grid; grid-template-rows:1fr 1fr;  align-items:center; justify-content:center;">
                            <input type="text" style="width: 80%; height:10px; margin: 0 auto;" wire:model="precio_moneda" />
                            <input type="text" style="width: 80%; height:10px; margin: 0 auto;" wire:model="descuento_moneda" />
                        </div>
                        <div style="display:grid; grid-template-rows:1fr 1fr;  align-items:center; justify-content:center;">
                            <input type="text" style="width: 80%; height:10px; margin: 0 auto;" wire:model="precio_consignatario" />
                            <input type="text" style="width: 80%; height:10px; margin: 0 auto;" wire:model="descuento_consignatario" />
                        </div>
                        <div style="display:grid; grid-template-rows:1fr 1fr 1fr; align-items:center;">
                            <p class="font-size-small margin-0" style="font-weight:bold">Líquido/Balance</p>
                            <p class="font-size-small margin-0" style="font-weight:bold" >Suplementos</p>
                            <p class="font-size-small margin-0" style="font-weight:bold" >Gastos accesorios</p>
                        </div>
                        <div style="display:grid; grid-template-rows:1fr 1fr 1fr; align-items:center; justify-content:center;">
                            <input type="text" style="width: 80%; height:10px; margin: 0 auto;" wire:model="liquido_remitente" />
                            <input type="text" style="width: 80%; height:10px; margin: 0 auto;" wire:model="suplementos_remitente" />
                            <input type="text" style="width: 80%; height:10px; margin: 0 auto;" wire:model="gastos_remitente" />
                        </div>
                        <div style="display:grid; grid-template-rows:1fr 1fr 1fr; align-items:center; justify-content:center;">
                            <input type="text" style="width: 80%; height:10px; margin: 0 auto;" wire:model="liquido_moneda" />
                            <input type="text" style="width: 80%; height:10px; margin: 0 auto;" wire:model="suplementos_moneda" />
                            <input type="text" style="width: 80%; height:10px; margin: 0 auto;" wire:model="gastos_moneda" />
                        </div>
                        <div style="display:grid; grid-template-rows:1fr 1fr 1fr; align-items:center; justify-content:center;">
                            <input type="text" style="width: 80%; height:10px; margin: 0 auto;" wire:model="liquido_consignatario" />
                            <input type="text" style="width: 80%; height:10px; margin: 0 auto;" wire:model="suplementos_consignatario" />
                            <input type="text" style="width: 80%; height:10px; margin: 0 auto;" wire:model="gastos_consignatario" />
                        </div>
                        <div>
                            <p class="margin-0">TOTAL:</p>
                        </div>
                        <div>
                            <input type="text" style="width: 90%" wire:model="total_remitente" />
                        </div>
                        <div>
                            <input type="text" style="width: 90%" wire:model="total_moneda"/>
                        </div>
                        <div>
                            <input type="text" style="width: 90%" wire:model="total_consignatario" />
                        </div>
                    </div>
                    <div style="display: grid; grid-template-columns: 20px 1fr;  border-bottom: 1px solid;">
                        <div class="numero" style="border:0px !important;"><span>15</span></div>
                        <div style="text-align:start; display:flex; gap:5px; align-self:center; border:0px !important;" ><p class="font-size-small margin-0">Reembolso </p> <input type="text" wire:model="reembolso" /> </div>
                    </div>
                    
                </div>
                <div id="firmas" style="display:grid; grid-template-columns: 1fr 1fr 1fr;">

                    <div style="display: flex; flex-direction:column; justify-content: space-between; align-items:center;">
                        <div class="numero" style="align-self: start; margin-left:5px;"><span>22</span></div>
                        <textarea style="width: 80%; height: 50%;" wire:model="lugar_entrega_22" ></textarea>
                        <div><p class="font-size-small margin-0">Lugar de entrega de la mercancía (lugar, país)</p></div>
                    </div>
                    <div style="border: 3px solid; display: flex; flex-direction:column; justify-content: space-between; align-items:center;">
                        <div class="numero" style="align-self: start; margin-left:5px;"><span>23</span></div>
                        @if($firma_transportista == null)
                            <canvas id="signature-pad" style="border:1px solid" class="signature-pad" width=200 height=100></canvas>
                        @else
                            <img src="{{ asset('storage/'.$firma_transportista) }}" alt="Firma">
                        @endif
                        <div><p class="font-size-small margin-0">Firma y sello del transportista</p></div>

                    </div>
                    <div style="display: flex; flex-direction:column; justify-content: space-between; align-items:center; border-top: 3px solid;">
                        <div class="numero" style="align-self: start; margin-left:5px;"><span>24</span></div>
                        <div><p class="font-size-small margin-0">Recibo de la mercancía</p></div>
                        <div class="font-size-small margin-0"><span >Lugar </span> <input type="text" style="width: 80px;" wire:model="lugar" /> a <input type="text" style="width: 80px;" wire:model="fecha" /></div>
                        @if($hasImage)
                            <img src="{{ asset('storage/photos/' . $firma) }}"
                                style="max-width: 100px !important; text-align: center">
                        @endif
                        <div><p class="font-size-small margin-0">Firma y sello del consignatario</p></div>

                    </div>

                </div>
            </div>
            
        </div>
        <aside class="aside-2">
            <small>En caso de mercancías peligrosas indicar, además de la certificación reglamentaria en la última línea del cuadro: la clase, la cifra y en su caso la letra.</small>
        </aside>
    </article>
    <script src="https://cdn.jsdelivr.net/npm/signature_pad@4.0.0/dist/signature_pad.umd.min.js"></script>

    <script>
        var canvas = document.querySelector('#signature-pad');
        var signaturePad = new SignaturePad(canvas);

        
        // Botón para guardar
        document.querySelector('#save-png-button').addEventListener('click', function() {
            if (signaturePad.isEmpty()) {
                
            } else {
                
                var dataURL = signaturePad.toDataURL('image/png');
                @this.set('firma_transportista', dataURL);
            }
        });
    </script>
</div>
