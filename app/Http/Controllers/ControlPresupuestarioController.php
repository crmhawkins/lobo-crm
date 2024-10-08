<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Facturas;
use Carbon\Carbon;
use App\Models\Productos;
use App\Models\Clientes;
use App\Models\Delegacion;

class ControlPresupuestarioController extends Controller
{
    
    public function index()
    {
        return view('control-presupuestario.index');
    }

    public function getDelegacion($clienteId){
        $cliente = Clientes::find($clienteId);
        if($cliente){
            $delegacion = $cliente->delegacion_COD;
            if($delegacion){
                $delegacion = Delegacion::where('COD', $delegacion)->first();
                if($delegacion){
                    return $delegacion->nombre;
                }
            }
        }

        return "No definido";
    }

    public function ventas(Request $request)
    {
        $search = $request->input('search');
        $fechaMin = $request->input('fechaMin');
        $fechaMax = $request->input('fechaMax');
        $perPage = $request->input('perPage', 25); // Valor por defecto 25

        // Obtenemos todos los productos disponibles
        $productos = Productos::all();

        // Consulta base de facturas con cliente y delegación relacionados (para la paginación)
        $query = Facturas::query()
            ->with(['cliente.delegacion', 'pedido.productosPedido.producto'])
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

        // Calcular los totales por producto: unidades y euros
        $totalesProductos = $productos->map(function($producto) use ($facturasSinPaginacion) {
            $totalUnidadesVendidas = 0;
            $totalEurosVendidos = 0;

            foreach ($facturasSinPaginacion as $factura) {
                if ($factura->pedido && $factura->pedido->productosPedido) {
                    // Buscar el producto en el pedido y sumar las unidades y el precio total
                    $productoPedido = $factura->pedido->productosPedido->firstWhere('producto_pedido_id', $producto->id);
                    if ($productoPedido) {
                        $totalUnidadesVendidas += $productoPedido->unidades;
                        
                        // Añadir el precio del producto con IVA
                        $precioSinIVA = $productoPedido->precio_total ?? ($productoPedido->precio_ud * $productoPedido->unidades);
                        $precioConIVA = $precioSinIVA * 1.21; // Añadir IVA del 21%
                        $totalEurosVendidos += $precioConIVA;
                    }
                }
            }

            // Añadir los totales al producto
            $producto->total_unidades_vendidas = $totalUnidadesVendidas;
            $producto->total_euros_vendidos = $totalEurosVendidos;

            return $producto;
        });

        // Calcular el total en euros de todas las facturas sumadas (sin paginación)
        $totalEurosFacturas = $facturasSinPaginacion->sum('total');

        return view('control-presupuestario.ventas', compact('facturas', 'productos', 'totalesProductos', 'totalEurosFacturas', 'search', 'fechaMin', 'fechaMax', 'perPage'));
    }
}
