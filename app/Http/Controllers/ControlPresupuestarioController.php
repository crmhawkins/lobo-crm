<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Facturas;
use Carbon\Carbon;
use App\Models\Productos;
use App\Models\Clientes;
use App\Models\Delegacion;
use App\Helpers\FacturaHelper;
use App\Models\Clients;

class ControlPresupuestarioController extends Controller
{
    
    public function index()
    {
        return view('control-presupuestario.index');
    }


    public function compras(Request $request)
    {
        $search = $request->input('search');
        $fechaMin = $request->input('fechaMin');
        $fechaMax = $request->input('fechaMax');
        $perPage = $request->input('perPage', 10); // Valor por defecto 25

        



    }

    

    public function ventas(Request $request)
    {
        $search = $request->input('search');
        $fechaMin = $request->input('fechaMin');
        $fechaMax = $request->input('fechaMax');
        $perPage = $request->input('perPage', 10); // Valor por defecto 25

        // Obtenemos todos los productos disponibles
        $productos = Productos::all();

        // Consulta base de facturas con cliente y delegación relacionados (para la paginación)
        $query = Facturas::query()
            ->with(['cliente.delegacion', 'pedido.productosPedido.producto', 'productosFacturas'])
            ->whereYear('created_at', Carbon::now()->year);

        // Filtrar por búsqueda
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('numero_factura', 'like', '%' . $search . '%')
                  ->orWhereHas('cliente', function($query) use ($search) {
                      $query->where('nombre', 'like', '%' . $search . '%');
                  });
            });
        }

        // Filtrar por fecha mínima y máxima
        if ($fechaMin && $fechaMax) {
            $query->whereBetween('created_at', [$fechaMin, $fechaMax]);
        } elseif ($fechaMin) {
            $query->whereDate('created_at', '>=', $fechaMin);
        } elseif ($fechaMax) {
            $query->whereDate('created_at', '<=', $fechaMax);
        }

        // Obtener todas las facturas filtradas sin paginación
        $facturasSinPaginacion = $query->get();

        // Obtener facturas paginadas para la tabla principal
        $facturas = $query->paginate($perPage);
        $facturas->map(function($factura) {
            $factura->hasIva = FacturaHelper::facturaHasIva($factura->id);
            return $factura;
        });
        // Calcular los totales por producto: unidades y euros
        $totalesProductos = $productos->map(function ($producto) use ($facturasSinPaginacion) {
            $totalUnidadesVendidas = 0;
            $totalEurosVendidos = 0;

            foreach ($facturasSinPaginacion as $factura) {
                // $hasIva = FacturaHelper::facturaHasIva($factura->id);

                // Facturas Rectificativas
                if ($factura->factura_id && $factura->productosFacturas) {
                    $productoFactura = $factura->productosFacturas->firstWhere('producto_id', $producto->id);
                    if ($productoFactura) {
                        // Descontar unidades y total
                        $totalUnidadesVendidas -= $productoFactura->cantidad;
                        // if( $hasIva ){
                        //     $totalEurosVendidos -= ($productoFactura->cantidad * $productoFactura->precio_ud) * 1.21; // Añadir IVA del 21% al total descontado
                        // }else{
                        //     $totalEurosVendidos -= ($productoFactura->cantidad * $productoFactura->precio_ud);
                        // }
                    }
                }else if ($factura->pedido && $factura->pedido->productosPedido) {
                    $productoPedido = $factura->pedido->productosPedido->firstWhere('producto_pedido_id', $producto->id);
                    if ($productoPedido) {
                        $totalUnidadesVendidas += $productoPedido->unidades;
                        // $precioSinIVA = $productoPedido->precio_total ?? ($productoPedido->precio_ud * $productoPedido->unidades);
                        // if( $hasIva ){
                        //     $precioConIVA = $precioSinIVA * 1.21; // Añadir IVA del 21%
                        // }else{
                        //     $precioConIVA = $precioSinIVA;
                        // }
                        // $totalEurosVendidos += $precioConIVA;
                    }
                }

                
            }

            // Añadir los totales al producto
            $producto->total_unidades_vendidas = $totalUnidadesVendidas;
            $producto->total_euros_vendidos = $totalEurosVendidos;

            return $producto;
        });

       // Calcular el total en euros de todas las facturas sumadas (sin paginación)
        $totalEurosFacturas = $facturasSinPaginacion->reduce(function ($carry, $factura) {
            $hasIva = FacturaHelper::facturaHasIva($factura->id);
            // Si es una factura rectificativa, restamos su total
            if ($factura->factura_id && $factura->facturaNormal) {

                if( $hasIva ){
                    return $carry + ($factura->total - $factura->facturaNormal->total) ;
                }else{
                    return $carry + ($factura->precio - $factura->facturaNormal->precio);
                }

                //$totalFactura = $factura->total - $factura->facturaNormal->total;
                // return $carry + $totalFactura;
            }

            // Si es una factura normal, sumamos su total
            if($hasIva){
                return $carry + $factura->total;

            }else{
                return $carry + $factura->precio;

            }
        }, 0);

        return view('control-presupuestario.ventas', compact('facturas', 'productos', 'totalesProductos', 'search', 'fechaMin', 'fechaMax', 'perPage' , 'totalEurosFacturas'));
    }
}
