<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Facturas;
use App\Models\Clients;
use App\Models\Productos;
use App\Models\Iva;
use App\Models\Pedido;
use App\Models\StockMercaderiaEntrante;
use App\Models\StockMercaderia;
use App\Models\Mercaderia;
use Illuminate\Support\Facades\DB;

class Test extends Controller
{
     /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $response = '';

        $facturas = Facturas::all();
        $clientes = Clients::all();

        //modificar la fecha de vencimiento de las facturas para que coincidan con la fecha de vencimiento por defecto de los clientes, hay que 
        //coger la fecha_emision de la factura y coger el vencimiento_factura_pref que son los dias a sumar a la fecha de emision, de ahí sacar la fecha de vencimiento

        //por cada factura debemos bucar al cliente asociado por su cliente_id y coger el vencimiento_factura_pref del cliente

        foreach ($facturas as $factura) {
            $cliente = Clients::find($factura->cliente_id);
            $factura->fecha_vencimiento = date('Y-m-d', strtotime($factura->fecha_emision . ' + ' . $cliente->vencimiento_factura_pref . ' days'));
            //dd($factura->fecha_vencimiento , $factura->fecha_emision, $cliente->vencimiento_factura_pref);
            $factura->save();
        }



        //dd($facturas);


        return 'test ok';
    }


    public function ivaAProductos()
    {
        $response = '';

        $ivas = Iva::all();
        $productos = Productos::all();

        $iva = Iva::where('iva', 21)->first();

        if (!$iva) {
            $iva = new Iva();
            $iva->name = '21%';
            $iva->iva = 21;
            $iva->save();
        }

        foreach ($productos as $producto) {
            $producto->iva_id = $iva->id;
            $producto->save();
        } 

        return 'test ok';
    }

    // public function cambiarQrsMercaderia(){
    //     $response = '';

    //     $mercaderias = Mercaderia::all();

    //     foreach ($mercaderias as $mercaderia) {
    //         $stockMercaderias = StockMercaderia::where('qr_id', $mercaderia->qr)->first();
    //     }

    //     return 'test ok';
    // }

    public function calcularIvayTotalFacturas(){
        $facturas = Facturas::all();

        foreach ($facturas as $factura) {
            // if (isset($factura->descuento)) {
            //     $iva = ($factura->precio * (1 + (- ($factura->descuento) / 100))) * 0.21;
            //     $totalesIva = ($factura->precio * (1 + (- ($factura->descuento) / 100))) * 1.21;
            // } else {
            //     $importe = $factura->precio;
            //     $iva = $factura->precio * 0.21;
            //     $totalesIva = $factura->precio * 1.21;
            // }

            $iva = $factura->iva_total_pedido;
            $totalesIva = $factura->precio + $factura->iva_total_pedido;

            $factura = Facturas::find($factura->id);
            $factura->iva = $iva;
            $factura->total = $totalesIva;
            $factura->save();
        }

        return 'test ok';

    }


    public function fixPedidos(){
        $pedidos = Pedido::all();

        foreach ($pedidos as $pedido) {
            if($pedido->id == 107){
                //dd($pedido->subtotal);
            };


            //actualizar campo subtotal, iva_total y descuento_total
            //si el pedido tiene descuento, calcular el descuento y restarlo al subtotal

            //iva total dependiendo del iva de los productos
            //Hay que coger los productos asociados al pedido y coger el iva de cada producto y sumarlos
            $productos = DB::table('productos_pedido')->where('pedido_id', $pedido->id)->get();
            $iva_total = 0;
            
            
            if($productos){
                $subtotal = 0;
                foreach ($productos as $p) {
                    $producto = Productos::find($p->producto_pedido_id);
                    
                    if(isset($producto) && $producto->iva_id){
                        
                        $subtotal += $p->precio_ud * $p->unidades;

                        $iva = Iva::find($producto->iva_id);
                        if($iva){
                            //dd($iva);
                            if($pedido->descuento == 1){
                                $iva_total += (($p->precio_ud * $p->unidades) * (1 - ($pedido->porcentaje_descuento / 100))) * ($iva->iva / 100);
                            }else{
                                $iva_total += (($p->precio_ud * $p->unidades)) * ($iva->iva / 100);
                            }
                        }else{
                            if($pedido->descuento == 1){
                                $iva_total += (($p->precio_ud * $p->unidades) * (1 - ($pedido->porcentaje_descuento / 100))) * 0.21;
                            }else{
                                $iva_total += (($p->precio_ud * $p->unidades)) * 0.21;
                            }
                        }
                    }
                }
                $pedido->subtotal = $subtotal;
                


            }else{
                $iva_total = $pedido->precio * 0.21;
                $pedido->subtotal = $pedido->precio;
            }

            if ($pedido->descuento) {
                $pedido->descuento_total = $pedido->subtotal * ($pedido->porcentaje_descuento / 100);
            }else{
                $pedido->descuento_total = 0;
               
            }
            
            $pedido->iva_total = $iva_total;
            
            $pedido->iva_total = number_format($pedido->iva_total, 2, '.', '');
            $pedido->descuento_total = number_format($pedido->descuento_total, 2, '.', '');
            $pedido->subtotal = number_format($pedido->subtotal, 2, '.', '');

            

            //comprobar si hay factura asociada
            
                $pedido->save();
                $facturas = Facturas::where('pedido_id', $pedido->id)->get();
                if(isset($facturas)){
                    foreach ($facturas as $f) {
                        
                        $f->subtotal_pedido = $pedido->subtotal;
                        $f->iva_total_pedido = $pedido->iva_total;
                        $f->descuento_total_pedido = $pedido->descuento_total;
                        if($f->numero_factura == 'F240019'){
                            //dd($pedido, $f);
                        }
                        $f->save();
                    }
                }
          

        }
        
        return 'test ok';
    }



}
