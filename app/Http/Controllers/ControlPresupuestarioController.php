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
use App\Models\Costes;
use App\Models\Pedido;
use App\Models\User;
use App\Models\Caja;

class ControlPresupuestarioController extends Controller
{
    
    public function index()
    {
        return view('control-presupuestario.index');
    }


    public function guardarCostes(Request $request)
{
    // Validar los datos del formulario
    $validatedData = $request->validate([
        'productos' => 'required|array',
        'productos.*' => 'required|exists:productos,id',
        'costes' => 'required|array',
        'costes.*' => 'required|numeric|min:0',
        'delegaciones' => 'nullable|array',
        'año' => 'required|numeric',
        'eliminados' => 'nullable|string',
    ]);

    //dd($request);

    // Eliminar los costes cuyos IDs están en el campo "eliminados"
  

    // Guardar o actualizar cada coste
    foreach ($request->productos as $index => $productoId) {

        //comprobar si ya existe un 

        $coste = Costes::updateOrCreate(
            [
                'product_id' => $productoId,
                'year' => $request->año,
                'COD' => $request->delegaciones[$index] ?? null,
            ],
            [
                'cost' => $request->costes[$index],
            ]
        );
    }

    return redirect()->back()->with('success', 'Costes guardados correctamente.');
}

public function eliminarCoste($id)
{
    // Buscar y eliminar el coste por su ID
    $coste = Costes::find($id);

    if ($coste) {
        $coste->delete();
        return redirect()->back()->with('success', 'Coste eliminado correctamente.');
    }

    return redirect()->back()->with('error', 'No se pudo eliminar el coste.');
}

public function compras(Request $request)
{
    // Establecer la localización en español
    Carbon::setLocale('es');

    $year = $request->input('year', Carbon::now()->year); // Año actual por defecto

    // Obtener todos los productos y delegaciones
    $productos2 = Productos::all();
    $delegaciones = Delegacion::all();

    // Obtenemos todas las facturas sin paginación
    $facturas = Facturas::whereYear('created_at', $year)
        ->with(['pedido.productosPedido.producto', 'cliente.delegacion']) // Asegúrate de cargar la relación de cliente y delegación
        ->orderBy('created_at', 'asc')
        ->get();

        // Obtener los costes por año
    $costes = Costes::query()
    ->with('producto', 'delegacion')
    ->where('year', $year)
    ->get();

    $costesMap = [];
    foreach ($costes as $coste) {
        $productId = $coste->product_id;
        $delegacionCOD = $coste->COD ?? 'General';
        $costesMap[$productId][$delegacionCOD] = $coste->cost;
    }

    // Calcular las ventas por trimestre, mes, producto y delegación
    $ventasPorTrimestre = [];

    foreach ($facturas as $factura) {
        $mes = Carbon::parse($factura->created_at)->month; // Obtener el mes de la factura
        $trimestre = ceil($mes / 3); // Calcular el trimestre (1 = Q1, 2 = Q2, etc.)

        $delegacionNombre = $factura->cliente->delegacion->nombre ?? 'General'; // Obtener la delegación o 'General' si no tiene

        // Inicializar el trimestre y mes en el array si no existen
        if (!isset($ventasPorTrimestre[$trimestre])) {
            $ventasPorTrimestre[$trimestre] = [];
        }
        if (!isset($ventasPorTrimestre[$trimestre][$mes])) {
            $ventasPorTrimestre[$trimestre][$mes] = [];
        }

        // Procesar los productos del pedido para registrar las ventas por producto
        if ($factura->pedido) {

            //try catch para evitar errores en la relación
            

            foreach ($factura->pedido->productosPedido as $productoPedido) {

                try {
                    $productoNombre = $productoPedido->producto->nombre;
                    $productId = $productoPedido->producto->id;
                    $unidadesVendidas = $productoPedido->unidades;

                    // Inicializar el producto en el mes si no existe
                    if (!isset($ventasPorTrimestre[$trimestre][$mes][$productoNombre])) {
                        $ventasPorTrimestre[$trimestre][$mes][$productoNombre] = [
                            'nombre' => $productoNombre,
                            'ventasDelegaciones' => [],
                        ];
                    }
                    $delegacionCOD = $factura->cliente->delegacion->COD ?? 'General'; // Usar 'General' si la delegación no existe

                    // Obtener el coste para la delegación o el coste general si no existe
                    $costeProducto = $costesMap[$productId][$delegacionCOD] ?? $costesMap[$productId]['General'] ?? 0;

                    // Inicializar la delegación si no existe para este producto
                    if (!isset($ventasPorTrimestre[$trimestre][$mes][$productoNombre]['ventasDelegaciones'][$delegacionNombre])) {
                        $ventasPorTrimestre[$trimestre][$mes][$productoNombre]['ventasDelegaciones'][$delegacionNombre] = [
                            'unidadesVendidas' => 0,
                            'costeTotal' => 0,
                        ];
                    }

                    // Sumar las unidades vendidas y calcular el coste total
                    $ventasPorTrimestre[$trimestre][$mes][$productoNombre]['ventasDelegaciones'][$delegacionNombre]['unidadesVendidas'] += $unidadesVendidas;
                    $ventasPorTrimestre[$trimestre][$mes][$productoNombre]['ventasDelegaciones'][$delegacionNombre]['costeTotal'] += $unidadesVendidas * $costeProducto;
                } catch (\Exception $e) {
                    continue;
                }

                
            }
        }
    }

    

    // Agrupar los costes por delegación
    $costesPorDelegacion = $costes->groupBy(function ($coste) {
        return $coste->delegacion ? $coste->delegacion->nombre : 'General';
    });

    return view('control-presupuestario.compras', compact(
        'ventasPorTrimestre', 'productos2', 'delegaciones', 'costesPorDelegacion', 'year'
    ));
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



    public function logistica(Request $request)
{
    Carbon::setLocale('es');

    $year = $request->input('year', Carbon::now()->year); // Año actual por defecto

    // Obtener los pedidos con gastos de transporte
    $gastosTransporte = Pedido::whereYear('created_at', $year)
        ->where('gastos_transporte', '!=', 0)
        ->with(['cliente.delegacion'])
        ->orderBy('created_at', 'asc')
        ->get();

    // Inicializar las variables para el mapa de gastos por trimestre y delegación
    $gastosTransportePorTrimestre = [];
    $totalesPorDelegacion = [];
    $totalPorTrimestre = [];

    foreach ($gastosTransporte as $gastoTransporte) {
        $mes = Carbon::parse($gastoTransporte->created_at)->month;
        $trimestre = ceil($mes / 3); // Calcular el trimestre
        $delegacionNombre = $gastoTransporte->cliente->delegacion->nombre ?? 'General';

        // Inicializar trimestre y delegación en el array si no existe
        if (!isset($gastosTransportePorTrimestre[$trimestre])) {
            $gastosTransportePorTrimestre[$trimestre] = [];
        }
        if (!isset($gastosTransportePorTrimestre[$trimestre][$mes])) {
            $gastosTransportePorTrimestre[$trimestre][$mes] = [];
        }
        if (!isset($gastosTransportePorTrimestre[$trimestre][$mes][$delegacionNombre])) {
            $gastosTransportePorTrimestre[$trimestre][$mes][$delegacionNombre] = 0;
        }

        // Sumar los gastos de transporte por delegación en ese mes
        $gastosTransportePorTrimestre[$trimestre][$mes][$delegacionNombre] += $gastoTransporte->gastos_transporte;

        // Sumar los totales por delegación
        if (!isset($totalesPorDelegacion[$delegacionNombre])) {
            $totalesPorDelegacion[$delegacionNombre] = 0;
        }
        $totalesPorDelegacion[$delegacionNombre] += $gastoTransporte->gastos_transporte;

        // Sumar los totales del trimestre
        if (!isset($totalPorTrimestre[$trimestre])) {
            $totalPorTrimestre[$trimestre] = [];
        }
        if (!isset($totalPorTrimestre[$trimestre][$delegacionNombre])) {
            $totalPorTrimestre[$trimestre][$delegacionNombre] = 0;
        }
        $totalPorTrimestre[$trimestre][$delegacionNombre] += $gastoTransporte->gastos_transporte;
    }

    $delegaciones = Delegacion::all();

    return view('control-presupuestario.logistica', compact(
        'gastosTransportePorTrimestre', 'totalesPorDelegacion', 'totalPorTrimestre', 'delegaciones', 'year'
    ));
}
    
    
public function comerciales(Request $request)
{
    // Establecer la localización en español
    Carbon::setLocale('es');

    $year = $request->input('year', Carbon::now()->year); // Año actual por defecto

    // Obtener todos los productos cuyo precio_ud es 0
    $productosGratis = Productos::all();
    $delegaciones = Delegacion::all();

    // Obtener todos los pedidos sin paginación y dentro del año seleccionado
    $pedidos = Pedido::whereYear('created_at', $year)
        ->with(['productosPedido.producto', 'cliente.delegacion']) // Asegúrate de cargar la relación de cliente y delegación
        ->orderBy('created_at', 'asc')
        ->get();

    // Obtener los costes por año
    $costes = Costes::query()
        ->with('producto', 'delegacion')
        ->where('year', $year)
        ->get();

    // Mapa de costes por producto y delegación
    $costesMap = [];
    foreach ($costes as $coste) {
        $productId = $coste->product_id;
        $delegacionCOD = $coste->COD ?? 'General';
        $costesMap[$productId][$delegacionCOD] = $coste->cost;
    }

    // Calcular las ventas por trimestre, mes, producto y delegación solo para productos cuyo precio_ud sea 0
    $ventasPorTrimestre = [];
    foreach ($pedidos as $pedido) {
        $mes = Carbon::parse($pedido->created_at)->month; // Obtener el mes del pedido
        $trimestre = ceil($mes / 3); // Calcular el trimestre (1 = Q1, 2 = Q2, etc.)

        $delegacionNombre = $pedido->cliente->delegacion->nombre ?? 'General'; // Obtener la delegación o 'General' si no tiene
        
        // Inicializar el trimestre y mes en el array si no existen
        if (!isset($ventasPorTrimestre[$trimestre])) {
            $ventasPorTrimestre[$trimestre] = [];
        }
        if (!isset($ventasPorTrimestre[$trimestre][$mes])) {
            $ventasPorTrimestre[$trimestre][$mes] = [];
        }

        // Procesar los productos del pedido para registrar las ventas por producto
        foreach ($pedido->productosPedido as $productoPedido) {
            
            try {
                $productoNombre = $productoPedido->producto->nombre;
                $productId = $productoPedido->producto->id;
                
                // Solo tomar productos cuyo precio unitario es 0
                if ($productoPedido->precio_ud == 0) {
                    if($pedido->id == 359){
                        //dd($pedido->cliente->delegacion);
            
                    }
                    $unidadesVendidas = $productoPedido->unidades;

                    // Inicializar el producto en el mes si no existe
                    if (!isset($ventasPorTrimestre[$trimestre][$mes][$productoNombre])) {
                        $ventasPorTrimestre[$trimestre][$mes][$productoNombre] = [
                            'nombre' => $productoNombre,
                            'ventasDelegaciones' => [],
                        ];
                    }

                    // Asegurarse de inicializar las ventas por delegación
                    if (!isset($ventasPorTrimestre[$trimestre][$mes][$productoNombre]['ventasDelegaciones'][$delegacionNombre])) {
                        $ventasPorTrimestre[$trimestre][$mes][$productoNombre]['ventasDelegaciones'][$delegacionNombre] = [
                            'unidadesVendidas' => 0,
                            'costeTotal' => 0,
                        ];
                    }

                    $delegacionCOD = $pedido->cliente->delegacion->COD ?? 'General'; // Usar 'General' si la delegación no existe

                    // Obtener el coste para la delegación o el coste general si no existe
                    $costeProducto = $costesMap[$productId][$delegacionCOD] ?? $costesMap[$productId]['General'] ?? 0;
                    //dd($costesMap);

                    // Sumar las unidades vendidas y calcular el coste total
                    $ventasPorTrimestre[$trimestre][$mes][$productoNombre]['ventasDelegaciones'][$delegacionNombre]['unidadesVendidas'] += $unidadesVendidas;
                    $ventasPorTrimestre[$trimestre][$mes][$productoNombre]['ventasDelegaciones'][$delegacionNombre]['costeTotal'] += $unidadesVendidas * $costeProducto;
                }
            } catch (\Exception $e) {
                continue;
            }
        }
    }
   // dd($ventasPorTrimestre);

    // Agrupar los costes por delegación
    $costesPorDelegacion = $costes->groupBy(function ($coste) {
        return $coste->delegacion ? $coste->delegacion->nombre : 'General';
    });

    return view('control-presupuestario.comerciales', compact(
        'ventasPorTrimestre', 'productosGratis', 'delegaciones', 'costesPorDelegacion', 'year'
    ));
}


public function marketing(Request $request)
{
    // Establecer la localización en español
    Carbon::setLocale('es');

    $year = $request->input('year', Carbon::now()->year); // Año actual por defecto

    // Obtener todas las delegaciones
    $delegaciones = Delegacion::all();
    $productos2 = Productos::all();

    // Obtener las cajas del departamento de marketing (id 2), cuyas cuentas contables comiencen por '6270'
    $cajas = Caja::where('departamento', 'marketing')
        ->whereYear('fecha', $year)
        ->where('cuenta', 'like', '6270%')
        ->with(['facturas', 'delegacion']) // Relación con delegación y facturas
        ->get();

    // Organizar las cajas por trimestre, mes y delegación
    $cajaPorTrimestre = [];

    foreach ($cajas as $caja) {
        try {
            $mes = Carbon::parse($caja->fecha)->month; // Obtener el mes de la caja
            $trimestre = ceil($mes / 3); // Calcular el trimestre (1 = Q1, 2 = Q2, etc.)

            $delegacionNombre = $caja->delegacion->nombre ?? 'General'; // Obtener la delegación o 'General' si no tiene

            // Inicializar el trimestre y mes en el array si no existen
            if (!isset($cajaPorTrimestre[$trimestre])) {
                $cajaPorTrimestre[$trimestre] = [];
            }
            if (!isset($cajaPorTrimestre[$trimestre][$mes])) {
                $cajaPorTrimestre[$trimestre][$mes] = [];
            }

            // Inicializar la delegación en el array si no existe
            if (!isset($cajaPorTrimestre[$trimestre][$mes][$delegacionNombre])) {
                $cajaPorTrimestre[$trimestre][$mes][$delegacionNombre] = 0;
            }

            // Sumar el total de caja por delegación y mes
            $cajaPorTrimestre[$trimestre][$mes][$delegacionNombre] += $caja->total;
        } catch (\Exception $e) {
            continue;
        }
    }

    // Obtener los pedidos de los clientes cuyo comercial está en el departamento de marketing
    $comercialesMarketing = User::where('user_department_id', 2)->pluck('id');

    // Obtener los pedidos del año correspondiente y que pertenecen a clientes cuyo comercial está en marketing
    $pedidos = Pedido::whereYear('created_at', $year)
        ->whereHas('cliente', function($query) use ($comercialesMarketing) {
            $query->whereIn('comercial_id', $comercialesMarketing);
        })
        ->with(['productosPedido.producto', 'cliente.delegacion'])
        ->get();

    // Obtener los costes por año
    $costes = Costes::where('year', $year)
        ->with('producto', 'delegacion')
        ->get();

    // Mapa de costes por producto y delegación
    $costesMap = [];
    foreach ($costes as $coste) {
        $productId = $coste->product_id;
        $delegacionCOD = $coste->COD ?? 'General';
        $costesMap[$productId][$delegacionCOD] = $coste->cost;
    }

    // Calcular las ventas por trimestre, mes, producto y delegación
    $ventasPorTrimestre = [];

    foreach ($pedidos as $pedido) {
        $mes = Carbon::parse($pedido->created_at)->month; // Obtener el mes del pedido
        $trimestre = ceil($mes / 3); // Calcular el trimestre (1 = Q1, 2 = Q2, etc.)

        $delegacionNombre = $pedido->cliente->delegacion->nombre ?? 'General'; // Obtener la delegación o 'General' si no tiene

        // Inicializar el trimestre y mes en el array si no existen
        if (!isset($ventasPorTrimestre[$trimestre])) {
            $ventasPorTrimestre[$trimestre] = [];
        }
        if (!isset($ventasPorTrimestre[$trimestre][$mes])) {
            $ventasPorTrimestre[$trimestre][$mes] = [];
        }

        // Procesar los productos del pedido para registrar las ventas por producto
        foreach ($pedido->productosPedido as $productoPedido) {
            try{
                if ($productoPedido->precio_ud != 0) {

                    continue;

                }
                $productoNombre = $productoPedido->producto->nombre;
                $productId = $productoPedido->producto->id;
                $unidadesVendidas = $productoPedido->unidades;

                // Inicializar el producto en el mes si no existe
                if (!isset($ventasPorTrimestre[$trimestre][$mes][$productoNombre])) {
                    $ventasPorTrimestre[$trimestre][$mes][$productoNombre] = [
                        'nombre' => $productoNombre,
                        'ventasDelegaciones' => [],
                    ];
                }

                $delegacionCOD = $pedido->cliente->delegacion->COD ?? 'General'; // Usar 'General' si la delegación no existe

                // Obtener el coste para la delegación o el coste general si no existe
                $costeProducto = $costesMap[$productId][$delegacionCOD] ?? $costesMap[$productId]['General'] ?? 0;

                // Inicializar la delegación si no existe para este producto
                if (!isset($ventasPorTrimestre[$trimestre][$mes][$productoNombre]['ventasDelegaciones'][$delegacionNombre])) {
                    $ventasPorTrimestre[$trimestre][$mes][$productoNombre]['ventasDelegaciones'][$delegacionNombre] = [
                        'unidadesVendidas' => 0,
                        'costeTotal' => 0,
                    ];
                }

                // Sumar las unidades vendidas y calcular el coste total
                $ventasPorTrimestre[$trimestre][$mes][$productoNombre]['ventasDelegaciones'][$delegacionNombre]['unidadesVendidas'] += $unidadesVendidas;
                $ventasPorTrimestre[$trimestre][$mes][$productoNombre]['ventasDelegaciones'][$delegacionNombre]['costeTotal'] += $unidadesVendidas * $costeProducto;
            } catch (\Exception $e) {
                continue;
            }
        }
    }
 // Agrupar los costes por delegación
 $costesPorDelegacion = $costes->groupBy(function ($coste) {
    return $coste->delegacion ? $coste->delegacion->nombre : 'General';
});

    return view('control-presupuestario.marketing', compact('cajaPorTrimestre', 'ventasPorTrimestre', 'delegaciones', 'year' , 'costesPorDelegacion' , 'productos2'));
}



}
