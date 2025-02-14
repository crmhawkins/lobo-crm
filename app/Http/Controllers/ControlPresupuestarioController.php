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
use App\Models\ProductosMarketing;
use App\Models\CostesMarketing;
use App\Models\CostesProductos;
use App\Models\CostesProductosMarketing;

//PDF
use PDF;

class ControlPresupuestarioController extends Controller
{
    
   



    public function index()
    {
        return view('control-presupuestario.index');
    }

    public function show()
    {
        return view('control-presupuestario.show');
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
public function guardarCostesMarketing(Request $request)
{
    // Validar los datos del formulario
    $validatedData = $request->validate([
        'productos_marketing' => 'required|array',
        'productos_marketing.*' => 'required|exists:productos_marketing,id',
        'costes_marketing' => 'required|array',
        'costes_marketing.*' => 'required|numeric|min:0',
        'delegaciones_marketing' => 'nullable|array',
        'año' => 'required|numeric',
        'eliminados' => 'nullable|string',
    ]);

    // Eliminar los costes de marketing cuyos IDs están en el campo "eliminados"
    if (!empty($validatedData['eliminados'])) {
        $idsEliminados = explode(',', $validatedData['eliminados']);
        CostesMarketing::whereIn('id', $idsEliminados)->delete();
    }

    // Guardar o actualizar cada coste de marketing
    foreach ($request->productos_marketing as $index => $productoId) {
        CostesMarketing::updateOrCreate(
            [
                'product_id' => $productoId,
                'year' => $request->año,
                'COD' => $request->delegaciones_marketing[$index] ?? null,
            ],
            [
                'cost' => $request->costes_marketing[$index],
            ]
        );
    }

    return redirect()->back()->with('success', 'Costes de marketing guardados correctamente.');
}

public function eliminarCosteMarketing($id)
{
    // Buscar y eliminar el coste por su ID
    $coste = CostesMarketing::find($id);
    if ($coste) {
        $coste->delete();
        return response()->json(['success' => 'Coste de marketing eliminado correctamente.']);
    }
    return response()->json(['error' => 'No se pudo eliminar el coste de marketing.'], 404);
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


public function analisisGlobal(Request $request)
{
    Carbon::setLocale('es');
    
    // Asignar trimestre por defecto si no está en la solicitud
    $trimestre = $request->input('trimestre', 1);  // Por defecto será el primer trimestre
    $year = $request->input('year', Carbon::now()->year);

    // Definir los meses del trimestre
    $mesesPorTrimestre = [
        1 => [1, 2, 3],  // Primer trimestre
        2 => [4, 5, 6],  // Segundo trimestre
        3 => [7, 8, 9],  // Tercer trimestre
        4 => [10, 11, 12] // Cuarto trimestre
    ];

    // Obtener los meses correspondientes al trimestre solicitado
    $meses = $mesesPorTrimestre[$trimestre] ?? [];

    // Si no hay meses definidos, detener el proceso
    if (empty($meses)) {
        return redirect()->back()->with('error', 'Trimestre inválido.');
    }

    // Obtener todas las delegaciones y agregar "General" si no existe
    $delegaciones = Delegacion::where('created_at', '!=', null)->orderBy('id')->get();
    $delegaciones = $delegaciones->concat(collect([(object)['id' => 0, 'nombre' => 'General']]));

    // Inicializar un array para almacenar las ventas, compras, márgenes y gastos por delegación y mes
    $ventasPorDelegacion = [];
    $comprasPorDelegacion = [];
    $resultadosPorDelegacion = [];  // Inicializar resultados para evitar error
    $margenBeneficioPorDelegacion = [];
    $gastosEstructuralesPorDelegacion = [];
    $gastosVariablesPorDelegacion = [];
    $gastosLogisticaPorDelegacion = [];
    $gastosTotalesPorDelegacion = [];  // Nuevo array para almacenar la suma de gastos estructurales y variables
    $margenFinalPorDelegacion = [];  // Nuevo array para el margen final después de restar todos los gastos
    $margenRealPorDelegacion = [];  // Nuevo array para almacenar el margen real
    $inversionComercialPorDelegacion = [];  // Nuevo array para almacenar la inversión comercial
    $inversionMarketingPorDelegacion = [];  // Nuevo array para almacenar la inversión marketing
    // $inversionPatrocinioPorDelegacion = [];  // Nuevo array para almacenar la inversión patrocinio
    $resultadoPorDelegacionGI = [];  // Nuevo array para almacenar el resultado (G-I)

    // Inicializar los arrays de ventas, compras y márgenes por delegación y mes
    foreach ($meses as $mes) {
        foreach ($delegaciones as $delegacion) {
            $ventasPorDelegacion[$mes][$delegacion->nombre] = 0;
            $comprasPorDelegacion[$mes][$delegacion->nombre] = 0;
            $resultadosPorDelegacion[$mes][$delegacion->nombre] = 0;  // Inicializar resultados en 0
            $margenBeneficioPorDelegacion[$mes][$delegacion->nombre] = 0;
            $gastosEstructuralesPorDelegacion[$mes][$delegacion->nombre] = 0;
            $gastosVariablesPorDelegacion[$mes][$delegacion->nombre] = 0;
            $gastosLogisticaPorDelegacion[$mes][$delegacion->nombre] = 0;
            $gastosTotalesPorDelegacion[$mes][$delegacion->nombre] = 0;  // Inicializar la suma de gastos en 0
            $margenFinalPorDelegacion[$mes][$delegacion->nombre] = 0;
            $margenRealPorDelegacion[$mes][$delegacion->nombre] = 0;  // Inicializar el margen real en 0
            $inversionComercialPorDelegacion[$mes][$delegacion->nombre] = 0;  // Inicializar la inversión comercial en 0
            $inversionMarketingPorDelegacion[$mes][$delegacion->nombre] = 0;  // Inicializar la inversión marketing en 0
            // $inversionPatrocinioPorDelegacion[$mes][$delegacion->nombre] = 0;
            $resultadoPorDelegacionGI[$mes][$delegacion->nombre] = 0;  // Inicializar resultado G-I

        }
    }

    // Obtener las facturas del año y trimestre seleccionado
    $facturas = Facturas::whereYear('created_at', $year)
        ->whereMonth('created_at', '>=', $meses[0])  // El primer mes del trimestre
        ->whereMonth('created_at', '<=', $meses[2])  // El último mes del trimestre
        ->with(['pedido.productosPedido.producto', 'cliente.delegacion'])
        ->get();

    // Obtener los costes por año
    $costes = Costes::query()
        ->with('producto', 'delegacion')
        ->where('year', $year)
        ->get();

    // Crear un mapa de costes por producto y delegación
    $costesMap = [];
    foreach ($costes as $coste) {
        $productId = $coste->product_id;
        $delegacionCOD = $coste->COD ?? 'General';
        $costesMap[$productId][$delegacionCOD] = $coste->cost;
    }

    // Recorrer las facturas y sumar las ventas y compras por delegación y mes
    foreach ($facturas as $factura) {
        $mes = Carbon::parse($factura->created_at)->month;
        $delegacionNombre = $factura->cliente->delegacion->nombre ?? 'General';

        // Sumar el total de la factura a la delegación correspondiente (ventas)
        if(isset( $ventasPorDelegacion[$mes][$delegacionNombre])){
            $ventasPorDelegacion[$mes][$delegacionNombre] += $factura->total;
        

            // Procesar los productos del pedido para calcular las compras
            if ($factura->pedido) {
                foreach ($factura->pedido->productosPedido as $productoPedido) {
                    try {
                        $productId = $productoPedido->producto->id;
                        $unidadesVendidas = $productoPedido->unidades;

                        // Obtener el coste del producto para la delegación o para "General"
                        $costeProducto = $costesMap[$productId][$factura->cliente->delegacion->COD ?? 'General'] ?? $costesMap[$productId]['General'] ?? 0;

                        // Sumar el coste total de las unidades vendidas (compras)
                        $comprasPorDelegacion[$mes][$delegacionNombre] += $unidadesVendidas * $costeProducto;
                    } catch (\Exception $e) {
                        continue;
                    }
                }
            }

            // Calcular el resultado (A-B) para cada delegación en cada mes
            $resultadosPorDelegacion[$mes][$delegacionNombre] = $ventasPorDelegacion[$mes][$delegacionNombre] - $comprasPorDelegacion[$mes][$delegacionNombre];
        }
    }

    // Calcular el margen de beneficio (Ventas - Compras) para cada delegación en cada mes
    foreach ($meses as $mes) {
        foreach ($delegaciones as $delegacion) {
            $delegacionNombre = $delegacion->nombre;
            $margenBeneficioPorDelegacion[$mes][$delegacionNombre] = $ventasPorDelegacion[$mes][$delegacionNombre] - $comprasPorDelegacion[$mes][$delegacionNombre];
        }
    }

    // Cálculo de gastos estructurales
    $gastosEstructurales = Caja::where('tipo_movimiento', 'Gasto')
        ->where(function ($query) {
            $query->where('cuenta', 'like', '1700%')
                ->orWhere('cuenta', 'like', '6290%')
                ->orWhere('cuenta', 'like', '6250%')
                ->orWhere('cuenta', 'like', '6210%')
                ->orWhere('cuenta', 'like', '6212%')
                ->orWhere('cuenta', 'like', '6293%')
                ->orWhere('cuenta', 'like', '6294%')
                ->orWhere('cuenta', 'like', '6295%')
                ->orWhere('cuenta', 'like', '4012%')
                ->orWhere('cuenta', 'like', '6400%');
        })
        ->whereYear('fecha', $year)
        ->get();

    // Sumar los gastos estructurales por delegación y mes
    foreach ($gastosEstructurales as $gasto) {
        $mes = Carbon::parse($gasto->fecha)->month;
        $delegacionNombre = $gasto->delegacion->nombre ?? 'General';
        if (in_array($mes, $meses)) {
            if(isset( $gastosEstructuralesPorDelegacion[$mes][$delegacionNombre])){
                $gastosEstructuralesPorDelegacion[$mes][$delegacionNombre] += $gasto->total;
            }
        }
    }

    // Cálculo de gastos variables
    $gastosVariables = Caja::where('tipo_movimiento', 'Gasto')
        ->where(function ($query) {
            $query->where('cuenta', 'like', '6240%')
                ->orWhere('cuenta', 'like', '6291%')
                ->orWhere('cuenta', 'like', '6391%')
                ->orWhere('cuenta', 'like', '6460%')
                ->orWhere('cuenta', 'like', '6210%');
        })
        ->whereYear('fecha', $year)
        ->get();

    // Sumar los gastos variables por delegación y mes
    foreach ($gastosVariables as $gasto) {
        $mes = Carbon::parse($gasto->fecha)->month;
        $delegacionNombre = $gasto->delegacion->nombre ?? 'General';
        if (in_array($mes, $meses)) {
            if(isset( $gastosVariablesPorDelegacion[$mes][$delegacionNombre])){
                $gastosVariablesPorDelegacion[$mes][$delegacionNombre] += $gasto->total;
            }
        }
    }

    // Sumar los gastos estructurales y variables por delegación y mes
    foreach ($meses as $mes) {
        foreach ($delegaciones as $delegacion) {
            $delegacionNombre = $delegacion->nombre;
            if(isset( $gastosTotalesPorDelegacion[$mes][$delegacionNombre])){
                $gastosTotalesPorDelegacion[$mes][$delegacionNombre] = 
                    $gastosEstructuralesPorDelegacion[$mes][$delegacionNombre] + 
                    $gastosVariablesPorDelegacion[$mes][$delegacionNombre];
            }
        }
    }

    // Cálculo de gastos de logística
    $gastosTransporte = Pedido::whereYear('created_at', $year)
        ->where('gastos_transporte', '!=', 0)
        ->with(['cliente.delegacion'])
        ->get();

    // Sumar los gastos de logística por delegación y mes
    foreach ($gastosTransporte as $gastoTransporte) {
        $mes = Carbon::parse($gastoTransporte->created_at)->month;
        $delegacionNombre = $gastoTransporte->cliente->delegacion->nombre ?? 'General';
        if (in_array($mes, $meses)) {
            $gastosLogisticaPorDelegacion[$mes][$delegacionNombre] += $gastoTransporte->gastos_transporte;
        }
    }

    // Calcular el margen final después de restar todos los gastos (C - D - E - F)
    foreach ($meses as $mes) {
        foreach ($delegaciones as $delegacion) {
            $delegacionNombre = $delegacion->nombre;
            $resultadoC = $margenBeneficioPorDelegacion[$mes][$delegacionNombre] ?? 0;
            $gastoTotalDE = $gastosTotalesPorDelegacion[$mes][$delegacionNombre] ?? 0;  // Gastos estructurales y variables sumados
            $gastoLogisticoF = $gastosLogisticaPorDelegacion[$mes][$delegacionNombre] ?? 0;

            $margenFinalPorDelegacion[$mes][$delegacionNombre] = $resultadoC - $gastoTotalDE - $gastoLogisticoF;

            // Calcular el margen real (Margen de beneficio - Total de gastos)
            $margenRealPorDelegacion[$mes][$delegacionNombre] = $margenBeneficioPorDelegacion[$mes][$delegacionNombre] - $gastosTotalesPorDelegacion[$mes][$delegacionNombre];

            // Calcular la inversión comercial (margen real * 0.65)
            $inversionComercialPorDelegacion[$mes][$delegacionNombre] = $margenFinalPorDelegacion[$mes][$delegacionNombre] * 0.65;

            //calcular inversión Marketing (magen * 0.18)
            $inversionMarketingPorDelegacion[$mes][$delegacionNombre] = $margenFinalPorDelegacion[$mes][$delegacionNombre] * 0.18;

            //calcular inversión Patrocinio (magen * 0.05)
            // $inversionPatrocinioPorDelegacion[$mes][$delegacionNombre] = $margenFinalPorDelegacion[$mes][$delegacionNombre] * 0.05;
        }
    }

    // Calcular el total por trimestre para todos los parámetros
    $totalVentasPorTrimestre = array_sum(array_map('array_sum', $ventasPorDelegacion));
    $totalComprasPorTrimestre = array_sum(array_map('array_sum', $comprasPorDelegacion));
    $totalResultadosPorTrimestre = array_sum(array_map('array_sum', $resultadosPorDelegacion));
    $totalGastosEstructuralesPorTrimestre = array_sum(array_map('array_sum', $gastosEstructuralesPorDelegacion));
    $totalGastosVariablesPorTrimestre = array_sum(array_map('array_sum', $gastosVariablesPorDelegacion));
    $totalGastosLogisticaPorTrimestre = array_sum(array_map('array_sum', $gastosLogisticaPorDelegacion));
    $totalGastosTotalesPorTrimestre = array_sum(array_map('array_sum', $gastosTotalesPorDelegacion));
    $totalMargenFinalPorTrimestre = array_sum(array_map('array_sum', $margenFinalPorDelegacion));
    $totalMargenRealPorTrimestre = array_sum(array_map('array_sum', $margenRealPorDelegacion));
    $totalInversionComercialPorTrimestre = array_sum(array_map('array_sum', $inversionComercialPorDelegacion));
    $totalInversionMarketingPorTrimestre = array_sum(array_map('array_sum', $inversionMarketingPorDelegacion));
    // $totalInversionPatrocinioPorTrimestre = array_sum(array_map('array_sum', $inversionPatrocinioPorDelegacion));
    //dd($inversionComercialPorDelegacion);

    foreach ($meses as $mes) {
        foreach ($delegaciones as $delegacion) {
            $delegacionNombre = $delegacion->nombre;

            // Sumar las inversiones
            $inversionTotalPorDelegacion[$mes][$delegacionNombre] = 
                $inversionComercialPorDelegacion[$mes][$delegacionNombre] + 
                $inversionMarketingPorDelegacion[$mes][$delegacionNombre] ; 
                // $inversionPatrocinioPorDelegacion[$mes][$delegacionNombre];

            // Calcular el resultado (G - I) => Margen Final - Total Inversiones
            $resultadoPorDelegacionGI[$mes][$delegacionNombre] = 
                $margenFinalPorDelegacion[$mes][$delegacionNombre] - $inversionTotalPorDelegacion[$mes][$delegacionNombre];
        }
    }


    return view('control-presupuestario.analisis-global', compact(
        'ventasPorDelegacion',
        'comprasPorDelegacion',
        'resultadosPorDelegacion',
        'totalVentasPorTrimestre',
        'totalComprasPorTrimestre',
        'totalResultadosPorTrimestre',
        'totalGastosEstructuralesPorTrimestre',
        'totalGastosVariablesPorTrimestre',
        'totalGastosLogisticaPorTrimestre',
        'totalGastosTotalesPorTrimestre',
        'totalMargenFinalPorTrimestre',
        'totalMargenRealPorTrimestre',
        'totalInversionComercialPorTrimestre', 
        'totalInversionMarketingPorTrimestre',
        // 'totalInversionPatrocinioPorTrimestre',
        'year',
        'trimestre',
        'delegaciones',
        'gastosEstructuralesPorDelegacion',
        'gastosVariablesPorDelegacion',
        'gastosLogisticaPorDelegacion',
        'gastosTotalesPorDelegacion',
        'margenBeneficioPorDelegacion',
        'margenFinalPorDelegacion',
        'margenRealPorDelegacion',
        'inversionComercialPorDelegacion',
        'inversionMarketingPorDelegacion',
        // 'inversionPatrocinioPorDelegacion',
        'resultadoPorDelegacionGI',
        'inversionTotalPorDelegacion',
    ));
}



public function exportarAnalisisGlobalAPDF(Request $request)
{
    Carbon::setLocale('es');
    
    // Asignar trimestre por defecto si no está en la solicitud
    $trimestre = $request->input('trimestre', 1);
    $year = $request->input('year', Carbon::now()->year);

    // Definir los meses del trimestre
    $mesesPorTrimestre = [
        1 => [1, 2, 3],
        2 => [4, 5, 6],
        3 => [7, 8, 9],
        4 => [10, 11, 12]
    ];

    // Obtener los meses correspondientes al trimestre solicitado
    $meses = $mesesPorTrimestre[$trimestre] ?? [];

    if (empty($meses)) {
        return redirect()->back()->with('error', 'Trimestre inválido.');
    }

    // Obtener todas las delegaciones y agregar "General" si no existe
    $delegaciones = Delegacion::where('created_at', '!=', null)->orderBy('id')->get();
    $delegaciones = $delegaciones->concat(collect([(object)['id' => 0, 'nombre' => 'General']]));

    // Inicializar arrays para almacenar datos
    $ventasPorDelegacion = [];
    $comprasPorDelegacion = [];
    $resultadosPorDelegacion = [];
    $margenBeneficioPorDelegacion = [];
    $gastosEstructuralesPorDelegacion = [];
    $gastosVariablesPorDelegacion = [];
    $gastosLogisticaPorDelegacion = [];
    $gastosTotalesPorDelegacion = [];
    $margenFinalPorDelegacion = [];
    $margenRealPorDelegacion = [];
    $inversionComercialPorDelegacion = [];
    $inversionMarketingPorDelegacion = [];
    $inversionPatrocinioPorDelegacion = [];
    $resultadoPorDelegacionGI = [];

    // Inicializar los arrays de ventas, compras y márgenes por delegación y mes
    foreach ($meses as $mes) {
        foreach ($delegaciones as $delegacion) {
            $ventasPorDelegacion[$mes][$delegacion->nombre] = 0;
            $comprasPorDelegacion[$mes][$delegacion->nombre] = 0;
            $resultadosPorDelegacion[$mes][$delegacion->nombre] = 0;
            $margenBeneficioPorDelegacion[$mes][$delegacion->nombre] = 0;
            $gastosEstructuralesPorDelegacion[$mes][$delegacion->nombre] = 0;
            $gastosVariablesPorDelegacion[$mes][$delegacion->nombre] = 0;
            $gastosLogisticaPorDelegacion[$mes][$delegacion->nombre] = 0;
            $gastosTotalesPorDelegacion[$mes][$delegacion->nombre] = 0;
            $margenFinalPorDelegacion[$mes][$delegacion->nombre] = 0;
            $margenRealPorDelegacion[$mes][$delegacion->nombre] = 0;
            $inversionComercialPorDelegacion[$mes][$delegacion->nombre] = 0;
            $inversionMarketingPorDelegacion[$mes][$delegacion->nombre] = 0;
            $inversionPatrocinioPorDelegacion[$mes][$delegacion->nombre] = 0;
            $resultadoPorDelegacionGI[$mes][$delegacion->nombre] = 0;
        }
    }

    // Obtener las facturas del año y trimestre seleccionado
    $facturas = Facturas::whereYear('created_at', $year)
        ->whereMonth('created_at', '>=', $meses[0])
        ->whereMonth('created_at', '<=', $meses[2])
        ->with(['pedido.productosPedido.producto', 'cliente.delegacion'])
        ->get();

    // Obtener los costes por año
    $costes = Costes::query()
        ->with('producto', 'delegacion')
        ->where('year', $year)
        ->get();

    // Crear un mapa de costes por producto y delegación
    $costesMap = [];
    foreach ($costes as $coste) {
        $productId = $coste->product_id;
        $delegacionCOD = $coste->COD ?? 'General';
        $costesMap[$productId][$delegacionCOD] = $coste->cost;
    }

    // Recorrer las facturas y sumar las ventas y compras por delegación y mes
    foreach ($facturas as $factura) {
        $mes = Carbon::parse($factura->created_at)->month;
        $delegacionNombre = $factura->cliente->delegacion->nombre ?? 'General';

        // Sumar el total de la factura a la delegación correspondiente (ventas)
        $ventasPorDelegacion[$mes][$delegacionNombre] += $factura->total;

        // Procesar los productos del pedido para calcular las compras
        if ($factura->pedido) {
            foreach ($factura->pedido->productosPedido as $productoPedido) {
                try {
                    $productId = $productoPedido->producto->id;
                    $unidadesVendidas = $productoPedido->unidades;

                    // Obtener el coste del producto para la delegación o para "General"
                    $costeProducto = $costesMap[$productId][$factura->cliente->delegacion->COD ?? 'General'] ?? $costesMap[$productId]['General'] ?? 0;

                    // Sumar el coste total de las unidades vendidas (compras)
                    $comprasPorDelegacion[$mes][$delegacionNombre] += $unidadesVendidas * $costeProducto;
                } catch (\Exception $e) {
                    continue;
                }
            }
        }

        // Calcular el resultado (A-B) para cada delegación en cada mes
        $resultadosPorDelegacion[$mes][$delegacionNombre] = $ventasPorDelegacion[$mes][$delegacionNombre] - $comprasPorDelegacion[$mes][$delegacionNombre];
    }

    // Calcular el margen de beneficio (Ventas - Compras) para cada delegación en cada mes
    foreach ($meses as $mes) {
        foreach ($delegaciones as $delegacion) {
            $delegacionNombre = $delegacion->nombre;
            $margenBeneficioPorDelegacion[$mes][$delegacionNombre] = $ventasPorDelegacion[$mes][$delegacionNombre] - $comprasPorDelegacion[$mes][$delegacionNombre];
        }
    }

    // Cálculo de gastos estructurales
    $gastosEstructurales = Caja::where('tipo_movimiento', 'Gasto')
        ->where(function ($query) {
            $query->where('cuenta', 'like', '1700%')
                ->orWhere('cuenta', 'like', '6290%')
                ->orWhere('cuenta', 'like', '6250%')
                ->orWhere('cuenta', 'like', '6210%')
                ->orWhere('cuenta', 'like', '6212%')
                ->orWhere('cuenta', 'like', '6293%')
                ->orWhere('cuenta', 'like', '6294%')
                ->orWhere('cuenta', 'like', '6295%')
                ->orWhere('cuenta', 'like', '4012%')
                ->orWhere('cuenta', 'like', '6400%');
        })
        ->whereYear('fecha', $year)
        ->get();

    // Sumar los gastos estructurales por delegación y mes
    foreach ($gastosEstructurales as $gasto) {
        $mes = Carbon::parse($gasto->fecha)->month;
        $delegacionNombre = $gasto->delegacion->nombre ?? 'General';
        if (in_array($mes, $meses)) {
            $gastosEstructuralesPorDelegacion[$mes][$delegacionNombre] += $gasto->total;
        }
    }

    // Cálculo de gastos variables
    $gastosVariables = Caja::where('tipo_movimiento', 'Gasto')
        ->where(function ($query) {
            $query->where('cuenta', 'like', '6240%')
                ->orWhere('cuenta', 'like', '6291%')
                ->orWhere('cuenta', 'like', '6391%')
                ->orWhere('cuenta', 'like', '6460%')
                ->orWhere('cuenta', 'like', '6210%');
        })
        ->whereYear('fecha', $year)
        ->get();

    // Sumar los gastos variables por delegación y mes
    foreach ($gastosVariables as $gasto) {
        $mes = Carbon::parse($gasto->fecha)->month;
        $delegacionNombre = $gasto->delegacion->nombre ?? 'General';
        if (in_array($mes, $meses)) {
            $gastosVariablesPorDelegacion[$mes][$delegacionNombre] += $gasto->total;
        }
    }

    // Sumar los gastos estructurales y variables por delegación y mes
    foreach ($meses as $mes) {
        foreach ($delegaciones as $delegacion) {
            $delegacionNombre = $delegacion->nombre;
            $gastosTotalesPorDelegacion[$mes][$delegacionNombre] = 
                $gastosEstructuralesPorDelegacion[$mes][$delegacionNombre] + 
                $gastosVariablesPorDelegacion[$mes][$delegacionNombre];
        }
    }

    // Cálculo de gastos de logística
    $gastosTransporte = Pedido::whereYear('created_at', $year)
        ->where('gastos_transporte', '!=', 0)
        ->with(['cliente.delegacion'])
        ->get();

    // Sumar los gastos de logística por delegación y mes
    foreach ($gastosTransporte as $gastoTransporte) {
        $mes = Carbon::parse($gastoTransporte->created_at)->month;
        $delegacionNombre = $gastoTransporte->cliente->delegacion->nombre ?? 'General';
        if (in_array($mes, $meses)) {
            $gastosLogisticaPorDelegacion[$mes][$delegacionNombre] += $gastoTransporte->gastos_transporte;
        }
    }

    // Calcular el margen final después de restar todos los gastos (C - D - E - F)
    foreach ($meses as $mes) {
        foreach ($delegaciones as $delegacion) {
            $delegacionNombre = $delegacion->nombre;
            $resultadoC = $margenBeneficioPorDelegacion[$mes][$delegacionNombre] ?? 0;
            $gastoTotalDE = $gastosTotalesPorDelegacion[$mes][$delegacionNombre] ?? 0;
            $gastoLogisticoF = $gastosLogisticaPorDelegacion[$mes][$delegacionNombre] ?? 0;

            $margenFinalPorDelegacion[$mes][$delegacionNombre] = $resultadoC - $gastoTotalDE - $gastoLogisticoF;

            // Calcular el margen real (Margen de beneficio - Total de gastos)
            $margenRealPorDelegacion[$mes][$delegacionNombre] = $margenBeneficioPorDelegacion[$mes][$delegacionNombre] - $gastosTotalesPorDelegacion[$mes][$delegacionNombre];

            // Calcular la inversión comercial (margen real * 0.65)
            $inversionComercialPorDelegacion[$mes][$delegacionNombre] = $margenFinalPorDelegacion[$mes][$delegacionNombre] * 0.65;

            // Calcular inversión Marketing (margen * 0.18)
            $inversionMarketingPorDelegacion[$mes][$delegacionNombre] = $margenFinalPorDelegacion[$mes][$delegacionNombre] * 0.18;

            // Calcular inversión Patrocinio (margen * 0.05)
            $inversionPatrocinioPorDelegacion[$mes][$delegacionNombre] = $margenFinalPorDelegacion[$mes][$delegacionNombre] * 0.05;
        }
    }

    // Calcular el total por trimestre para todos los parámetros
    $totalVentasPorTrimestre = array_sum(array_map('array_sum', $ventasPorDelegacion));
    $totalComprasPorTrimestre = array_sum(array_map('array_sum', $comprasPorDelegacion));
    $totalResultadosPorTrimestre = array_sum(array_map('array_sum', $resultadosPorDelegacion));
    $totalGastosEstructuralesPorTrimestre = array_sum(array_map('array_sum', $gastosEstructuralesPorDelegacion));
    $totalGastosVariablesPorTrimestre = array_sum(array_map('array_sum', $gastosVariablesPorDelegacion));
    $totalGastosLogisticaPorTrimestre = array_sum(array_map('array_sum', $gastosLogisticaPorDelegacion));
    $totalGastosTotalesPorTrimestre = array_sum(array_map('array_sum', $gastosTotalesPorDelegacion));
    $totalMargenFinalPorTrimestre = array_sum(array_map('array_sum', $margenFinalPorDelegacion));
    $totalMargenRealPorTrimestre = array_sum(array_map('array_sum', $margenRealPorDelegacion));
    $totalInversionComercialPorTrimestre = array_sum(array_map('array_sum', $inversionComercialPorDelegacion));
    $totalInversionMarketingPorTrimestre = array_sum(array_map('array_sum', $inversionMarketingPorDelegacion));
    $totalInversionPatrocinioPorTrimestre = array_sum(array_map('array_sum', $inversionPatrocinioPorDelegacion));

    foreach ($meses as $mes) {
        foreach ($delegaciones as $delegacion) {
            $delegacionNombre = $delegacion->nombre;

            // Sumar las inversiones
            $inversionTotalPorDelegacion[$mes][$delegacionNombre] = 
                $inversionComercialPorDelegacion[$mes][$delegacionNombre] + 
                $inversionMarketingPorDelegacion[$mes][$delegacionNombre] + 
                $inversionPatrocinioPorDelegacion[$mes][$delegacionNombre];

            // Calcular el resultado (G - I) => Margen Final - Total Inversiones
            $resultadoPorDelegacionGI[$mes][$delegacionNombre] = 
                $margenFinalPorDelegacion[$mes][$delegacionNombre] - $inversionTotalPorDelegacion[$mes][$delegacionNombre];
        }
    }

    // Generar el PDF
    $pdf = PDF::loadView('pdf.analisis-global', compact(
        'ventasPorDelegacion',
        'comprasPorDelegacion',
        'resultadosPorDelegacion',
        'totalVentasPorTrimestre',
        'totalComprasPorTrimestre',
        'totalResultadosPorTrimestre',
        'totalGastosEstructuralesPorTrimestre',
        'totalGastosVariablesPorTrimestre',
        'totalGastosLogisticaPorTrimestre',
        'totalGastosTotalesPorTrimestre',
        'totalMargenFinalPorTrimestre',
        'totalMargenRealPorTrimestre',
        'totalInversionComercialPorTrimestre', 
        'totalInversionMarketingPorTrimestre',
        'totalInversionPatrocinioPorTrimestre',
        'year',
        'trimestre',
        'delegaciones',
        'gastosEstructuralesPorDelegacion',
        'gastosVariablesPorDelegacion',
        'gastosLogisticaPorDelegacion',
        'gastosTotalesPorDelegacion',
        'margenBeneficioPorDelegacion',
        'margenFinalPorDelegacion',
        'margenRealPorDelegacion',
        'inversionComercialPorDelegacion',
        'inversionMarketingPorDelegacion',
        'inversionPatrocinioPorDelegacion',
        'resultadoPorDelegacionGI',
        'inversionTotalPorDelegacion',
    )) ->setPaper('a2', 'landscape');
    return $pdf->download('analisis_global.pdf');
}



public function compras(Request $request)
{
    // Establecer la localización en español
    Carbon::setLocale('es');

    $year = $request->input('year', Carbon::now()->year); // Año actual por defecto

    // Obtener todos los productos y delegaciones
    $productos2 = Productos::all();
    $delegaciones = Delegacion::where('created_at', '!=', null)->get();

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

public function exportarComprasAPDF(Request $request)
{
    // Establecer la localización en español
    Carbon::setLocale('es');

    $year = $request->input('year', Carbon::now()->year);

    // Obtener todos los productos y delegaciones
    $productos2 = Productos::all();
    $delegaciones = Delegacion::where('created_at', '!=', null)->get();

    // Obtenemos todas las facturas sin paginación
    $facturas = Facturas::whereYear('created_at', $year)
        ->with(['pedido.productosPedido.producto', 'cliente.delegacion'])
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
        $mes = Carbon::parse($factura->created_at)->month;
        $trimestre = ceil($mes / 3);
        $delegacionNombre = $factura->cliente->delegacion->nombre ?? 'General';

        if (!isset($ventasPorTrimestre[$trimestre])) {
            $ventasPorTrimestre[$trimestre] = [];
        }
        if (!isset($ventasPorTrimestre[$trimestre][$mes])) {
            $ventasPorTrimestre[$trimestre][$mes] = [];
        }

        if ($factura->pedido) {
            foreach ($factura->pedido->productosPedido as $productoPedido) {
                try {
                    $productoNombre = $productoPedido->producto->nombre;
                    $productId = $productoPedido->producto->id;
                    $unidadesVendidas = $productoPedido->unidades;

                    if (!isset($ventasPorTrimestre[$trimestre][$mes][$productoNombre])) {
                        $ventasPorTrimestre[$trimestre][$mes][$productoNombre] = [
                            'nombre' => $productoNombre,
                            'ventasDelegaciones' => [],
                        ];
                    }
                    $delegacionCOD = $factura->cliente->delegacion->COD ?? 'General';
                    $costeProducto = $costesMap[$productId][$delegacionCOD] ?? $costesMap[$productId]['General'] ?? 0;

                    if (!isset($ventasPorTrimestre[$trimestre][$mes][$productoNombre]['ventasDelegaciones'][$delegacionNombre])) {
                        $ventasPorTrimestre[$trimestre][$mes][$productoNombre]['ventasDelegaciones'][$delegacionNombre] = [
                            'unidadesVendidas' => 0,
                            'costeTotal' => 0,
                        ];
                    }

                    $ventasPorTrimestre[$trimestre][$mes][$productoNombre]['ventasDelegaciones'][$delegacionNombre]['unidadesVendidas'] += $unidadesVendidas;
                    $ventasPorTrimestre[$trimestre][$mes][$productoNombre]['ventasDelegaciones'][$delegacionNombre]['costeTotal'] += $unidadesVendidas * $costeProducto;
                } catch (\Exception $e) {
                    continue;
                }
            }
        }
    }

    $costesPorDelegacion = $costes->groupBy(function ($coste) {
        return $coste->delegacion ? $coste->delegacion->nombre : 'General';
    });

    // Generar el PDF
    $pdf = PDF::loadView('pdf.compras', compact(
        'ventasPorTrimestre', 'productos2', 'delegaciones', 'costesPorDelegacion', 'year'
    ))->setPaper('a2', 'landscape');
    return $pdf->download('compras.pdf');
}

public function ventasDelegaciones(Request $request)
{
    Carbon::setLocale('es');

    $year = $request->input('year', Carbon::now()->year);
    $delegacionId = $request->input('delegacion');
    
    // Obtener las delegaciones y agregar "No-definido" si no existe en la base de datos
    $delegaciones = Delegacion::where('created_at', '!=', null)->orderBy('id')->get();
    $delegaciones = $delegaciones->concat(collect([(object)['id' => 0, 'nombre' => 'No-definido']])); // Agregar manualmente 'No-definido'

    // Inicializar arrays para ventas por trimestre y precios
    $totalVentas = ['1T' => 0, '2T' => 0, '3T' => 0, '4T' => 0, 'anual' => 0];
    
    // Array para almacenar las ventas por delegación
    $delegacionVentas = [];

    $facturas = Facturas::whereYear('created_at', $year)
        ->with(['pedido.productosPedido.producto', 'cliente.delegacion'])
        ->orderBy('created_at', 'asc')
        ->get();

    foreach ($facturas as $factura) {
        $mes = Carbon::parse($factura->created_at)->month;
        $trimestre = ceil($mes / 3); // Calcular el trimestre

        // Asignar nombre de la delegación, o 'No-definido' si no existe
        $delegacionNombre = $factura->cliente->delegacion->nombre ?? 'No-definido';

        // Inicializar ventas para esta delegación si no existe aún
        if (!isset($delegacionVentas[$delegacionNombre])) {
            $delegacionVentas[$delegacionNombre] = ['1T' => 0, '2T' => 0, '3T' => 0, '4T' => 0];
        }

        // Sumar las ventas de la delegación en el trimestre correspondiente
        $delegacionVentas[$delegacionNombre]["{$trimestre}T"] += $factura->total;

        // Sumar las ventas generales por trimestre
        $totalVentas["{$trimestre}T"] += $factura->total;
    }

    // Calcular el total anual
    $totalVentas['anual'] = $totalVentas['1T'] + $totalVentas['2T'] + $totalVentas['3T'] + $totalVentas['4T'];

    return view('control-presupuestario.ventas-delegaciones', compact('delegaciones', 'year', 'delegacionVentas', 'totalVentas'));
}


public function exportarVentasDelegacionesAPDF(Request $request)
{
    Carbon::setLocale('es');

    $year = $request->input('year', Carbon::now()->year);
    $delegacionId = $request->input('delegacion');
    
    // Obtener las delegaciones y agregar "No-definido" si no existe en la base de datos
    $delegaciones = Delegacion::where('created_at', '!=', null)->orderBy('id')->get();
    $delegaciones = $delegaciones->concat(collect([(object)['id' => 0, 'nombre' => 'No-definido']])); // Agregar manualmente 'No-definido'

    // Inicializar arrays para ventas por trimestre y precios
    $totalVentas = ['1T' => 0, '2T' => 0, '3T' => 0, '4T' => 0, 'anual' => 0];
    
    // Array para almacenar las ventas por delegación
    $delegacionVentas = [];

    $facturas = Facturas::whereYear('created_at', $year)
        ->with(['pedido.productosPedido.producto', 'cliente.delegacion'])
        ->orderBy('created_at', 'asc')
        ->get();

    foreach ($facturas as $factura) {
        $mes = Carbon::parse($factura->created_at)->month;
        $trimestre = ceil($mes / 3); // Calcular el trimestre

        // Asignar nombre de la delegación, o 'No-definido' si no existe
        $delegacionNombre = $factura->cliente->delegacion->nombre ?? 'No-definido';

        // Inicializar ventas para esta delegación si no existe aún
        if (!isset($delegacionVentas[$delegacionNombre])) {
            $delegacionVentas[$delegacionNombre] = ['1T' => 0, '2T' => 0, '3T' => 0, '4T' => 0];
        }

        // Sumar las ventas de la delegación en el trimestre correspondiente
        $delegacionVentas[$delegacionNombre]["{$trimestre}T"] += $factura->total;

        // Sumar las ventas generales por trimestre
        $totalVentas["{$trimestre}T"] += $factura->total;
    }

    // Calcular el total anual
    $totalVentas['anual'] = $totalVentas['1T'] + $totalVentas['2T'] + $totalVentas['3T'] + $totalVentas['4T'];

    // Generar el PDF
    $pdf = PDF::loadView('pdf.ventas-delegaciones', compact('delegaciones', 'year', 'delegacionVentas', 'totalVentas'));
    return $pdf->download('ventas_delegaciones.pdf');
}

public function ventasPorProductos(Request $request)
{
    $year = $request->input('year', Carbon::now()->year);

    // Obtener las delegaciones ordenadas por ID
    $delegaciones = Delegacion::where('created_at', '!=', null)->orderBy('id')->get()->toArray();

    // Añadir "No-definido" como último objeto
    $delegaciones[] = [
        'id' => 0,
        'nombre' => 'No-definido'
    ];

    // Inicializar arrays para ventas por trimestre y precios
    $totalVentas = ['1T' => 0, '2T' => 0, '3T' => 0, '4T' => 0, 'anual' => 0];

    // Array para almacenar ventas por delegación y trimestre
    $ventasPorTrimestre = [];

    $facturas = Facturas::whereYear('created_at', $year)
        ->with(['pedido.productosPedido.producto', 'cliente.delegacion'])
        ->orderBy('created_at', 'asc')
        ->get();

    foreach ($facturas as $factura) {
        $mes = Carbon::parse($factura->created_at)->month;
        $trimestre = ceil($mes / 3);

        // Validación para manejar delegaciones nulas
        $delegacionNombre = $factura->cliente->delegacion->nombre ?? 'No-definido';

        // Inicializar el array para la delegación y trimestre si no existe
        if (!isset($ventasPorTrimestre[$delegacionNombre][$trimestre])) {
            $ventasPorTrimestre[$delegacionNombre][$trimestre] = [];
        }

        // Procesar los productos del pedido
        if ($factura->pedido) {
            foreach ($factura->pedido->productosPedido as $productoPedido) {
                $productoNombre = $productoPedido->producto->nombre ?? 'No-definido';
                $esSinCargo = ($productoPedido->precio_ud == 0);

                // Inicializar el array para el producto
                if (!isset($ventasPorTrimestre[$delegacionNombre][$trimestre][$productoNombre])) {
                    $ventasPorTrimestre[$delegacionNombre][$trimestre][$productoNombre] = [
                        'conCargo' => 0,
                        'sinCargo' => 0
                    ];
                }

                // Sumar las unidades al tipo de cargo correspondiente
                if ($esSinCargo) {
                    $ventasPorTrimestre[$delegacionNombre][$trimestre][$productoNombre]['sinCargo'] += $productoPedido->unidades;
                } else {
                    $ventasPorTrimestre[$delegacionNombre][$trimestre][$productoNombre]['conCargo'] += $productoPedido->unidades;
                }

                // Sumar ventas generales por trimestre
                $totalVentas["{$trimestre}T"] += $productoPedido->unidades;
            }
        }
    }

    // Calcular el total anual
    $totalVentas['anual'] = $totalVentas['1T'] + $totalVentas['2T'] + $totalVentas['3T'] + $totalVentas['4T'];

    return view('control-presupuestario.ventas-por-productos', compact('delegaciones', 'year', 'ventasPorTrimestre', 'totalVentas'));
}



public function exportarVentasPorProductosAPDF(Request $request)
{
    $year = $request->input('year', Carbon::now()->year);

    // Obtener las delegaciones ordenadas por ID
    $delegaciones = Delegacion::where('created_at', '!=', null)->orderBy('id')->get()->toArray();

    // Añadir "No-definido" como último objeto
    $delegaciones[] = [
        'id' => 0,
        'nombre' => 'No-definido'
    ];

    // Inicializar arrays para ventas por trimestre y precios
    $totalVentas = ['1T' => 0, '2T' => 0, '3T' => 0, '4T' => 0, 'anual' => 0];

    // Array para almacenar ventas por delegación y trimestre
    $ventasPorTrimestre = [];

    $facturas = Facturas::whereYear('created_at', $year)
        ->with(['pedido.productosPedido.producto', 'cliente.delegacion'])
        ->orderBy('created_at', 'asc')
        ->get();

    foreach ($facturas as $factura) {
        $mes = Carbon::parse($factura->created_at)->month;
        $trimestre = ceil($mes / 3);

        // Validación para manejar delegaciones nulas
        $delegacionNombre = $factura->cliente->delegacion->nombre ?? 'No-definido';

        // Inicializar el array para la delegación y trimestre si no existe
        if (!isset($ventasPorTrimestre[$delegacionNombre][$trimestre])) {
            $ventasPorTrimestre[$delegacionNombre][$trimestre] = [];
        }

        // Procesar los productos del pedido
        if ($factura->pedido) {
            foreach ($factura->pedido->productosPedido as $productoPedido) {
                $productoNombre = $productoPedido->producto->nombre ?? 'No-definido';
                $esSinCargo = ($productoPedido->precio_ud == 0);

                // Inicializar el array para el producto
                if (!isset($ventasPorTrimestre[$delegacionNombre][$trimestre][$productoNombre])) {
                    $ventasPorTrimestre[$delegacionNombre][$trimestre][$productoNombre] = [
                        'conCargo' => 0,
                        'sinCargo' => 0
                    ];
                }

                // Sumar las unidades al tipo de cargo correspondiente
                if ($esSinCargo) {
                    $ventasPorTrimestre[$delegacionNombre][$trimestre][$productoNombre]['sinCargo'] += $productoPedido->unidades;
                } else {
                    $ventasPorTrimestre[$delegacionNombre][$trimestre][$productoNombre]['conCargo'] += $productoPedido->unidades;
                }

                // Sumar ventas generales por trimestre
                $totalVentas["{$trimestre}T"] += $productoPedido->unidades;
            }
        }
    }

    // Calcular el total anual
    $totalVentas['anual'] = $totalVentas['1T'] + $totalVentas['2T'] + $totalVentas['3T'] + $totalVentas['4T'];

    // Generar el PDF
    $pdf = PDF::loadView('pdf.ventas-por-productos', compact('delegaciones', 'year', 'ventasPorTrimestre', 'totalVentas'))->setPaper('a1', 'landscape');
    return $pdf->download('ventas_por_productos.pdf');
}


public function presupuestosDelegacion(Request $request)
{
    // Establecer la localización en español
    Carbon::setLocale('es');

    $year = $request->input('year', Carbon::now()->year); // Año actual por defecto
    $delegacionId = $request->input('delegacion'); // Delegación seleccionada por defecto
    $delegacion = Delegacion::where('created_at', '!=', null)->find($delegacionId);
    // Obtener todas las delegaciones
    $delegaciones = Delegacion::where('created_at', '!=', null)->get();
    $presupuestosPorTrimestre = [];

    if(!$delegacion){
        return view('control-presupuestario.presupuestos-delegacion', compact('delegaciones', 'year' , 'presupuestosPorTrimestre' , 'delegacion'));
    }
    // Costes
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

    // Obtener las facturas de la delegación seleccionada en el año
    $facturas = Facturas::whereYear('created_at', $year)
        ->whereHas('cliente.delegacion', function ($query) use ($delegacionId) {
            $query->where('id', $delegacionId);
        })
        ->with(['pedido.productosPedido.producto', 'cliente.delegacion'])
        ->orderBy('created_at', 'asc')
        ->get();

    // Inicializar array para agrupar datos por trimestre
    $presupuestosPorTrimestre = [];
    $totalVentas = ['1T' => 0, '2T' => 0, '3T' => 0, '4T' => 0, 'anual' => 0];
    $totalCompras = ['1T' => 0, '2T' => 0, '3T' => 0, '4T' => 0, 'anual' => 0];

    foreach ($facturas as $factura) {
        $mes = Carbon::parse($factura->created_at)->month;
        $trimestre = ceil($mes / 3); // Calcular el trimestre

        // Obtener la delegación de la factura
        $delegacionNombre = $factura->cliente->delegacion->nombre ?? 'General';

        // Inicializar el trimestre y mes en el array si no existen
        if (!isset($presupuestosPorTrimestre[$trimestre])) {
            $presupuestosPorTrimestre[$trimestre] = [];
        }
        if (!isset($presupuestosPorTrimestre[$trimestre][$mes])) {
            $presupuestosPorTrimestre[$trimestre][$mes] = [];
        }

        // Procesar los productos del pedido
        if ($factura->pedido) {
            foreach ($factura->pedido->productosPedido as $productoPedido) {

                $productoNombre = $productoPedido->producto ? $productoPedido->producto->nombre : 'Producto no encontrado';
                $productId = $productoPedido->producto ? $productoPedido->producto->id : 'Producto no encontrado';
                $unidadesVendidas = $productoPedido->unidades;
                $precioTotal = $productoPedido->precio_total; // Precio total del producto

                // Inicializar el producto si no existe en el array
                if (!isset($presupuestosPorTrimestre[$trimestre][$mes][$productoNombre])) {
                    $presupuestosPorTrimestre[$trimestre][$mes][$productoNombre] = [
                        'nombre' => $productoNombre,
                        'ventasDelegaciones' => [],
                    ];
                }

                // Obtener el coste para la delegación o el coste general
                $delegacionCOD = $factura->cliente->delegacion->COD ?? 'General';
                $costeProducto = $costesMap[$productId][$delegacionCOD] ?? $costesMap[$productId]['General'] ?? 0;

                // Inicializar la delegación si no existe
                if (!isset($presupuestosPorTrimestre[$trimestre][$mes][$productoNombre]['ventasDelegaciones'][$delegacionNombre])) {
                    $presupuestosPorTrimestre[$trimestre][$mes][$productoNombre]['ventasDelegaciones'][$delegacionNombre] = [
                        'unidadesVendidas' => 0,
                        'costeTotal' => 0,
                        'precioTotal' => 0,
                    ];
                }

                // Sumar unidades, precio con IVA, y calcular el coste total
                $precioConIVA = $precioTotal + ($precioTotal * 0.21); // Precio total con IVA (21%)

                $presupuestosPorTrimestre[$trimestre][$mes][$productoNombre]['ventasDelegaciones'][$delegacionNombre]['unidadesVendidas'] += $unidadesVendidas;
                $presupuestosPorTrimestre[$trimestre][$mes][$productoNombre]['ventasDelegaciones'][$delegacionNombre]['precioTotal'] += $precioConIVA; // Precio con IVA
                $presupuestosPorTrimestre[$trimestre][$mes][$productoNombre]['ventasDelegaciones'][$delegacionNombre]['costeTotal'] += $unidadesVendidas * $costeProducto;

                // Sumar ventas y compras totales por trimestre y anual
                $totalVentas["{$trimestre}T"] += $precioConIVA;
                $totalCompras["{$trimestre}T"] += $unidadesVendidas * $costeProducto;
            }
        }
    }

    // Calcular las ventas y compras anuales
    $totalVentas['anual'] = $totalVentas['1T'] + $totalVentas['2T'] + $totalVentas['3T'] + $totalVentas['4T'];
    $totalCompras['anual'] = $totalCompras['1T'] + $totalCompras['2T'] + $totalCompras['3T'] + $totalCompras['4T'];

    // Calcular el margen de beneficio real
    $margenBeneficioReal = [
        '1T' => $totalVentas['1T'] - $totalCompras['1T'],
        '2T' => $totalVentas['2T'] - $totalCompras['2T'],
        '3T' => $totalVentas['3T'] - $totalCompras['3T'],
        '4T' => $totalVentas['4T'] - $totalCompras['4T'],
        'anual' => $totalVentas['anual'] - $totalCompras['anual']
    ];

    // Calcular el porcentaje de margen de beneficio real
    $margenPorcentajeReal = [
        '1T' => ($totalVentas['1T'] > 0) ? ($margenBeneficioReal['1T'] / $totalVentas['1T']) * 100 : 0,
        '2T' => ($totalVentas['2T'] > 0) ? ($margenBeneficioReal['2T'] / $totalVentas['2T']) * 100 : 0,
        '3T' => ($totalVentas['3T'] > 0) ? ($margenBeneficioReal['3T'] / $totalVentas['3T']) * 100 : 0,
        '4T' => ($totalVentas['4T'] > 0) ? ($margenBeneficioReal['4T'] / $totalVentas['4T']) * 100 : 0,
        'anual' => ($totalVentas['anual'] > 0) ? ($margenBeneficioReal['anual'] / $totalVentas['anual']) * 100 : 0,
    ];

    $gastosEstructurales = Caja::where('tipo_movimiento', 'Gasto')
    ->where(function ($query) {
        $query->where('cuenta', 'like', '1700%')
            ->orWhere('cuenta', 'like', '6290%')
            ->orWhere('cuenta', 'like', '6250%')
            ->orWhere('cuenta', 'like', '6210%')
            ->orWhere('cuenta', 'like', '6212%')
            ->orWhere('cuenta', 'like', '6293%')
            ->orWhere('cuenta', 'like', '6294%')
            ->orWhere('cuenta', 'like', '6295%')
            ->orWhere('cuenta', 'like', '4012%')
            ->orWhere('cuenta', 'like', '6400%');
        })
        ->whereYear('fecha', $year) // Añadir filtro por año
        ->where('delegacion_id', $delegacionId) // Filtrar por la delegación seleccionada
        ->get();
        $totalGastosEstructurales = ['1T' => 0, '2T' => 0, '3T' => 0, '4T' => 0, 'anual' => 0];
        $gastosEstructuralesPorTrimestre = [];

        foreach ($gastosEstructurales as $gasto) {
            $mesGasto = Carbon::parse($gasto->fecha)->month;
            $trimestreGasto = ceil($mesGasto / 3); // Calcular el trimestre

            //inicializar el trimestre
            if (!isset($totalGastosEstructurales[$trimestreGasto])) {
                $totalGastosEstructurales[$trimestreGasto] = [];
            }

            if (!isset($gastosEstructuralesPorTrimestre[$trimestreGasto][$gasto->cuenta])) {
                $gastosEstructuralesPorTrimestre[$trimestreGasto][$gasto->cuenta] = [
                    'total' => 0,
                    'nombre' => $gasto->proveedor->nombre
                ];
            }


                $totalGastosEstructurales["{$trimestreGasto}T"] += $gasto->total;

            // Sumar el gasto en el trimestre correspondiente
            $gastosEstructuralesPorTrimestre[$trimestreGasto][$gasto->cuenta]['total'] += $gasto->total;
        }
        $gastosEstructuralesAnuales = [];
        foreach ($gastosEstructuralesPorTrimestre as $trimestre => $cuentas) {
            foreach ($cuentas as $cuenta => $data) {
                if (!isset($gastosEstructuralesAnuales[$cuenta])) {
                    $gastosEstructuralesAnuales[$cuenta] = 0;
                }
                $gastosEstructuralesAnuales[$cuenta] += $data['total'];
            }
        }


        $totalGastosEstructurales['anual'] = $totalGastosEstructurales['1T'] + $totalGastosEstructurales['2T'] + $totalGastosEstructurales['3T'] + $totalGastosEstructurales['4T'];

        $gastosVariables = Caja::where('tipo_movimiento', 'Gasto')
        ->where(function ($query) {
            $query->where('cuenta', 'like', '6240%')
                ->orWhere('cuenta', 'like', '6291%')
                ->orWhere('cuenta', 'like', '6391%')
                ->orWhere('cuenta', 'like', '6460%')
                ->orWhere('cuenta', 'like', '6210%');
        })
        ->whereYear('fecha', $year) // Añadir filtro por año
        ->where('delegacion_id', $delegacionId) // Filtrar por la delegación seleccionada
        ->get();
        $totalGastosVariables = ['1T' => 0, '2T' => 0, '3T' => 0, '4T' => 0, 'anual' => 0];
        $gastosVariablesPorTrimestre = [];

        foreach ($gastosVariables as $gasto) {
            $mesGasto = Carbon::parse($gasto->fecha)->month;
            $trimestreGasto = ceil($mesGasto / 3); // Calcular el trimestre
    
             //inicializar el trimestre
             if (!isset($totalGastosVariables[$trimestreGasto])) {
                $totalGastosVariables[$trimestreGasto] = [];
            }

            if (!isset($gastosVariablesPorTrimestre[$trimestreGasto][$gasto->cuenta])) {
                $gastosVariablesPorTrimestre[$trimestreGasto][$gasto->cuenta] = [
                    'total' => 0,
                    'nombre' => $gasto->proveedor->nombre
                ];
            }


                $totalGastosVariables["{$trimestreGasto}T"] += $gasto->total;

            // Sumar el gasto en el trimestre correspondiente
            $gastosVariablesPorTrimestre[$trimestreGasto][$gasto->cuenta]['total'] += $gasto->total;


            // Sumar el gasto en el trimestre correspondiente
        }
        $totalGastosVariables['anual'] = $totalGastosVariables['1T'] + $totalGastosVariables['2T'] + $totalGastosVariables['3T'] + $totalGastosVariables['4T'];
        $gastosVariablesAnuales = [];
        foreach ($gastosVariablesPorTrimestre as $trimestre => $cuentas) {
            foreach ($cuentas as $cuenta => $data) {
                if (!isset($gastosVariablesAnuales[$cuenta])) {
                    $gastosVariablesAnuales[$cuenta] = 0;
                }
                $gastosVariablesAnuales[$cuenta] += $data['total'];
            }
        }
        
    


    return view('control-presupuestario.presupuestos-delegacion', compact(
        'presupuestosPorTrimestre', 'delegaciones', 'year', 'totalVentas', 'totalCompras', 'margenBeneficioReal', 'margenPorcentajeReal' , 'totalGastosEstructurales', 'gastosEstructurales',
        'totalGastosVariables', 'gastosVariables' , 'totalGastosEstructurales', 'gastosEstructurales', 'gastosEstructuralesPorTrimestre', 'gastosEstructuralesAnuales', 'gastosVariablesPorTrimestre' , 'gastosVariablesAnuales',
        'delegacion'

    ));
}



public function exportarPresupuestosDelegacionAPDF(Request $request)
{
    // Establecer la localización en español
    Carbon::setLocale('es');

    $year = $request->input('year', Carbon::now()->year);
    $delegacionId = $request->input('delegacion');
    $delegacion = Delegacion::where('created_at', '!=', null)->find($delegacionId);
    $delegaciones = Delegacion::where('created_at', '!=', null)->get();
    $presupuestosPorTrimestre = [];

    if (!$delegacion) {
        return view('control-presupuestario.presupuestos-delegacion', compact('delegaciones', 'year', 'presupuestosPorTrimestre', 'delegacion'));
    }

    // Costes
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

    // Obtener las facturas de la delegación seleccionada en el año
    $facturas = Facturas::whereYear('created_at', $year)
        ->whereHas('cliente.delegacion', function ($query) use ($delegacionId) {
            $query->where('id', $delegacionId);
        })
        ->with(['pedido.productosPedido.producto', 'cliente.delegacion'])
        ->orderBy('created_at', 'asc')
        ->get();

    // Inicializar array para agrupar datos por trimestre
    $presupuestosPorTrimestre = [];
    $totalVentas = ['1T' => 0, '2T' => 0, '3T' => 0, '4T' => 0, 'anual' => 0];
    $totalCompras = ['1T' => 0, '2T' => 0, '3T' => 0, '4T' => 0, 'anual' => 0];

    foreach ($facturas as $factura) {
        $mes = Carbon::parse($factura->created_at)->month;
        $trimestre = ceil($mes / 3);

        $delegacionNombre = $factura->cliente->delegacion->nombre ?? 'General';

        if (!isset($presupuestosPorTrimestre[$trimestre])) {
            $presupuestosPorTrimestre[$trimestre] = [];
        }
        if (!isset($presupuestosPorTrimestre[$trimestre][$mes])) {
            $presupuestosPorTrimestre[$trimestre][$mes] = [];
        }

        if ($factura->pedido) {
            foreach ($factura->pedido->productosPedido as $productoPedido) {
                $productoNombre = $productoPedido->producto ? $productoPedido->producto->nombre : 'Producto no encontrado';
                $productId = $productoPedido->producto ? $productoPedido->producto->id : 'Producto no encontrado';
                $unidadesVendidas = $productoPedido->unidades;
                $precioTotal = $productoPedido->precio_total;

                if (!isset($presupuestosPorTrimestre[$trimestre][$mes][$productoNombre])) {
                    $presupuestosPorTrimestre[$trimestre][$mes][$productoNombre] = [
                        'nombre' => $productoNombre,
                        'ventasDelegaciones' => [],
                    ];
                }

                $delegacionCOD = $factura->cliente->delegacion->COD ?? 'General';
                $costeProducto = $costesMap[$productId][$delegacionCOD] ?? $costesMap[$productId]['General'] ?? 0;

                if (!isset($presupuestosPorTrimestre[$trimestre][$mes][$productoNombre]['ventasDelegaciones'][$delegacionNombre])) {
                    $presupuestosPorTrimestre[$trimestre][$mes][$productoNombre]['ventasDelegaciones'][$delegacionNombre] = [
                        'unidadesVendidas' => 0,
                        'costeTotal' => 0,
                        'precioTotal' => 0,
                    ];
                }

                $precioConIVA = $precioTotal + ($precioTotal * 0.21);

                $presupuestosPorTrimestre[$trimestre][$mes][$productoNombre]['ventasDelegaciones'][$delegacionNombre]['unidadesVendidas'] += $unidadesVendidas;
                $presupuestosPorTrimestre[$trimestre][$mes][$productoNombre]['ventasDelegaciones'][$delegacionNombre]['precioTotal'] += $precioConIVA;
                $presupuestosPorTrimestre[$trimestre][$mes][$productoNombre]['ventasDelegaciones'][$delegacionNombre]['costeTotal'] += $unidadesVendidas * $costeProducto;

                $totalVentas["{$trimestre}T"] += $precioConIVA; 
                $totalCompras["{$trimestre}T"] += $unidadesVendidas * $costeProducto;
            }
        }
    }

    $totalVentas['anual'] = $totalVentas['1T'] + $totalVentas['2T'] + $totalVentas['3T'] + $totalVentas['4T'];
    $totalCompras['anual'] = $totalCompras['1T'] + $totalCompras['2T'] + $totalCompras['3T'] + $totalCompras['4T'];

    $margenBeneficioReal = [
        '1T' => $totalVentas['1T'] - $totalCompras['1T'],
        '2T' => $totalVentas['2T'] - $totalCompras['2T'],
        '3T' => $totalVentas['3T'] - $totalCompras['3T'],
        '4T' => $totalVentas['4T'] - $totalCompras['4T'],
        'anual' => $totalVentas['anual'] - $totalCompras['anual']
    ];

    $margenPorcentajeReal = [
        '1T' => ($totalVentas['1T'] > 0) ? ($margenBeneficioReal['1T'] / $totalVentas['1T']) * 100 : 0,
        '2T' => ($totalVentas['2T'] > 0) ? ($margenBeneficioReal['2T'] / $totalVentas['2T']) * 100 : 0,
        '3T' => ($totalVentas['3T'] > 0) ? ($margenBeneficioReal['3T'] / $totalVentas['3T']) * 100 : 0,
        '4T' => ($totalVentas['4T'] > 0) ? ($margenBeneficioReal['4T'] / $totalVentas['4T']) * 100 : 0,
        'anual' => ($totalVentas['anual'] > 0) ? ($margenBeneficioReal['anual'] / $totalVentas['anual']) * 100 : 0,
    ];

    // Añadir lógica para gastos estructurales y variables
    $gastosEstructurales = Caja::where('tipo_movimiento', 'Gasto')
        ->where(function ($query) {
            $query->where('cuenta', 'like', '1700%')
                ->orWhere('cuenta', 'like', '6290%')
                ->orWhere('cuenta', 'like', '6250%')
                ->orWhere('cuenta', 'like', '6210%')
                ->orWhere('cuenta', 'like', '6212%')
                ->orWhere('cuenta', 'like', '6293%')
                ->orWhere('cuenta', 'like', '6294%')
                ->orWhere('cuenta', 'like', '6295%')
                ->orWhere('cuenta', 'like', '4012%')
                ->orWhere('cuenta', 'like', '6400%');
        })
        ->whereYear('fecha', $year)
        ->where('delegacion_id', $delegacionId)
        ->get();

    $totalGastosEstructurales = ['1T' => 0, '2T' => 0, '3T' => 0, '4T' => 0, 'anual' => 0];
    $gastosEstructuralesPorTrimestre = [];

    foreach ($gastosEstructurales as $gasto) {
        $mesGasto = Carbon::parse($gasto->fecha)->month;
        $trimestreGasto = ceil($mesGasto / 3);

        if (!isset($totalGastosEstructurales[$trimestreGasto])) {
            $totalGastosEstructurales[$trimestreGasto] = [];
        }

        if (!isset($gastosEstructuralesPorTrimestre[$trimestreGasto][$gasto->cuenta])) {
            $gastosEstructuralesPorTrimestre[$trimestreGasto][$gasto->cuenta] = [
                'total' => 0,
                'nombre' => $gasto->proveedor->nombre
            ];
        }

        $totalGastosEstructurales["{$trimestreGasto}T"] += $gasto->total;
        $gastosEstructuralesPorTrimestre[$trimestreGasto][$gasto->cuenta]['total'] += $gasto->total;
    }

    $gastosEstructuralesAnuales = [];
    foreach ($gastosEstructuralesPorTrimestre as $trimestre => $cuentas) {
        foreach ($cuentas as $cuenta => $data) {
            if (!isset($gastosEstructuralesAnuales[$cuenta])) {
                $gastosEstructuralesAnuales[$cuenta] = 0;
            }
            $gastosEstructuralesAnuales[$cuenta] += $data['total'];
        }
    }

    $totalGastosEstructurales['anual'] = $totalGastosEstructurales['1T'] + $totalGastosEstructurales['2T'] + $totalGastosEstructurales['3T'] + $totalGastosEstructurales['4T'];

    $gastosVariables = Caja::where('tipo_movimiento', 'Gasto')
        ->where(function ($query) {
            $query->where('cuenta', 'like', '6240%')
                ->orWhere('cuenta', 'like', '6291%')
                ->orWhere('cuenta', 'like', '6391%')
                ->orWhere('cuenta', 'like', '6460%')
                ->orWhere('cuenta', 'like', '6210%');
        })
        ->whereYear('fecha', $year)
        ->where('delegacion_id', $delegacionId)
        ->get();

    $totalGastosVariables = ['1T' => 0, '2T' => 0, '3T' => 0, '4T' => 0, 'anual' => 0];
    $gastosVariablesPorTrimestre = [];

    foreach ($gastosVariables as $gasto) {
        $mesGasto = Carbon::parse($gasto->fecha)->month;
        $trimestreGasto = ceil($mesGasto / 3);

        if (!isset($totalGastosVariables[$trimestreGasto])) {
            $totalGastosVariables[$trimestreGasto] = [];
        }

        if (!isset($gastosVariablesPorTrimestre[$trimestreGasto][$gasto->cuenta])) {
            $gastosVariablesPorTrimestre[$trimestreGasto][$gasto->cuenta] = [
                'total' => 0,
                'nombre' => $gasto->proveedor->nombre
            ];
        }

        $totalGastosVariables["{$trimestreGasto}T"] += $gasto->total;
        $gastosVariablesPorTrimestre[$trimestreGasto][$gasto->cuenta]['total'] += $gasto->total;
    }

    $totalGastosVariables['anual'] = $totalGastosVariables['1T'] + $totalGastosVariables['2T'] + $totalGastosVariables['3T'] + $totalGastosVariables['4T'];
    $gastosVariablesAnuales = [];
    foreach ($gastosVariablesPorTrimestre as $trimestre => $cuentas) {
        foreach ($cuentas as $cuenta => $data) {
            if (!isset($gastosVariablesAnuales[$cuenta])) {
                $gastosVariablesAnuales[$cuenta] = 0;
            }
            $gastosVariablesAnuales[$cuenta] += $data['total'];
        }
    }

    // Asegúrate de pasar todas las variables necesarias a la vista
    $pdf = PDF::loadView('pdf.presupuestos-delegacion', compact(
        'presupuestosPorTrimestre', 'delegaciones', 'year', 'totalVentas', 'totalCompras', 'margenBeneficioReal', 'margenPorcentajeReal', 
        'totalGastosEstructurales', 'gastosEstructurales', 'gastosEstructuralesPorTrimestre', 'gastosEstructuralesAnuales',
        'totalGastosVariables', 'gastosVariables', 'gastosVariablesPorTrimestre', 'gastosVariablesAnuales', 'delegacion'
    ));
    return $pdf->download('presupuestos_delegacion.pdf');
}


public function proyeccion(Request $request)
{
    // Establecer la localización en español
    Carbon::setLocale('es');

    $year = $request->input('year', Carbon::now()->year); // Año actual por defecto
    $delegacionId = $request->input('delegacion'); // Delegación seleccionada por defecto
    $porcentaje = $request->input('porcentaje', 0); // Porcentaje  por defecto
    $delegacion = Delegacion::where('created_at', '!=', null)->find($delegacionId);
    // Obtener todas las delegaciones
    $delegaciones = Delegacion::where('created_at', '!=', null)->get();
    $presupuestosPorTrimestre = [];

    if(!$delegacion){
        return view('control-presupuestario.proyeccion', compact('delegaciones', 'year' , 'presupuestosPorTrimestre' , 'delegacion'));
    }
    // Costes
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

    // Obtener las facturas de la delegación seleccionada en el año
    $facturas = Facturas::whereYear('created_at', $year)
        ->whereHas('cliente.delegacion', function ($query) use ($delegacionId) {
            $query->where('id', $delegacionId);
        })
        ->with(['pedido.productosPedido.producto', 'cliente.delegacion'])
        ->orderBy('created_at', 'asc')
        ->get();

    // Inicializar array para agrupar datos por trimestre
    $presupuestosPorTrimestre = [];
    $totalVentas = ['1T' => 0, '2T' => 0, '3T' => 0, '4T' => 0, 'anual' => 0];
    $totalCompras = ['1T' => 0, '2T' => 0, '3T' => 0, '4T' => 0, 'anual' => 0];

    foreach ($facturas as $factura) {
        $mes = Carbon::parse($factura->created_at)->month;
        $trimestre = ceil($mes / 3); // Calcular el trimestre

        // Obtener la delegación de la factura
        $delegacionNombre = $factura->cliente->delegacion->nombre ?? 'General';

        // Inicializar el trimestre y mes en el array si no existen
        if (!isset($presupuestosPorTrimestre[$trimestre])) {
            $presupuestosPorTrimestre[$trimestre] = [];
        }
        if (!isset($presupuestosPorTrimestre[$trimestre][$mes])) {
            $presupuestosPorTrimestre[$trimestre][$mes] = [];
        }

        // Procesar los productos del pedido
        if ($factura->pedido) {
            foreach ($factura->pedido->productosPedido as $productoPedido) {
                $productoNombre = $productoPedido->producto ? $productoPedido->producto->nombre : 'Producto no encontrado';
                $productId = $productoPedido->producto ? $productoPedido->producto->id : 'Producto no encontrado';
                $unidadesVendidas = $productoPedido->unidades;
                $precioTotal = $productoPedido->precio_total; // Precio total del producto

                // Inicializar el producto si no existe en el array
                if (!isset($presupuestosPorTrimestre[$trimestre][$mes][$productoNombre])) {
                    $presupuestosPorTrimestre[$trimestre][$mes][$productoNombre] = [
                        'nombre' => $productoNombre,
                        'ventasDelegaciones' => [],
                    ];
                }

                // Obtener el coste para la delegación o el coste general
                $delegacionCOD = $factura->cliente->delegacion->COD ?? 'General';
                $costeProducto = $costesMap[$productId][$delegacionCOD] ?? $costesMap[$productId]['General'] ?? 0;

                // Inicializar la delegación si no existe
                if (!isset($presupuestosPorTrimestre[$trimestre][$mes][$productoNombre]['ventasDelegaciones'][$delegacionNombre])) {
                    $presupuestosPorTrimestre[$trimestre][$mes][$productoNombre]['ventasDelegaciones'][$delegacionNombre] = [
                        'unidadesVendidas' => 0,
                        'costeTotal' => 0,
                        'precioTotal' => 0,
                    ];
                }

                // Sumar unidades, precio con IVA, y calcular el coste total
                $precioConIVA = $precioTotal + ($precioTotal * 0.21); // Precio total con IVA (21%)

                $presupuestosPorTrimestre[$trimestre][$mes][$productoNombre]['ventasDelegaciones'][$delegacionNombre]['unidadesVendidas'] += $unidadesVendidas +  $unidadesVendidas * ($porcentaje / 100);
                $presupuestosPorTrimestre[$trimestre][$mes][$productoNombre]['ventasDelegaciones'][$delegacionNombre]['precioTotal'] += $precioConIVA + $precioConIVA * ($porcentaje / 100); // Precio con IVA
                $presupuestosPorTrimestre[$trimestre][$mes][$productoNombre]['ventasDelegaciones'][$delegacionNombre]['costeTotal'] += ($unidadesVendidas * $costeProducto) + ($unidadesVendidas * $costeProducto) * ($porcentaje / 100);

                // Sumar ventas y compras totales por trimestre y anual
                $totalVentas["{$trimestre}T"] += $precioConIVA + $precioConIVA * ($porcentaje / 100);
                $totalCompras["{$trimestre}T"] += ($unidadesVendidas * $costeProducto) + ($unidadesVendidas * $costeProducto) * ($porcentaje / 100);
            }
        }
    }

    // Calcular las ventas y compras anuales
    $totalVentas['anual'] = $totalVentas['1T'] + $totalVentas['2T'] + $totalVentas['3T'] + $totalVentas['4T'];
    $totalCompras['anual'] = $totalCompras['1T'] + $totalCompras['2T'] + $totalCompras['3T'] + $totalCompras['4T'];

    // Calcular el margen de beneficio real
    $margenBeneficioReal = [
        '1T' => $totalVentas['1T'] - $totalCompras['1T'],
        '2T' => $totalVentas['2T'] - $totalCompras['2T'],
        '3T' => $totalVentas['3T'] - $totalCompras['3T'],
        '4T' => $totalVentas['4T'] - $totalCompras['4T'],
        'anual' => $totalVentas['anual'] - $totalCompras['anual']
    ];

    // Calcular el porcentaje de margen de beneficio real
    $margenPorcentajeReal = [
        '1T' => ($totalVentas['1T'] > 0) ? ($margenBeneficioReal['1T'] / $totalVentas['1T']) * 100 : 0,
        '2T' => ($totalVentas['2T'] > 0) ? ($margenBeneficioReal['2T'] / $totalVentas['2T']) * 100 : 0,
        '3T' => ($totalVentas['3T'] > 0) ? ($margenBeneficioReal['3T'] / $totalVentas['3T']) * 100 : 0,
        '4T' => ($totalVentas['4T'] > 0) ? ($margenBeneficioReal['4T'] / $totalVentas['4T']) * 100 : 0,
        'anual' => ($totalVentas['anual'] > 0) ? ($margenBeneficioReal['anual'] / $totalVentas['anual']) * 100 : 0,
    ];

    $gastosEstructurales = Caja::where('tipo_movimiento', 'Gasto')
    ->where(function ($query) {
        $query->where('cuenta', 'like', '1700%')
            ->orWhere('cuenta', 'like', '6290%')
            ->orWhere('cuenta', 'like', '6250%')
            ->orWhere('cuenta', 'like', '6210%')
            ->orWhere('cuenta', 'like', '6212%')
            ->orWhere('cuenta', 'like', '6293%')
            ->orWhere('cuenta', 'like', '6294%')
            ->orWhere('cuenta', 'like', '6295%')
            ->orWhere('cuenta', 'like', '4012%')
            ->orWhere('cuenta', 'like', '6400%');
        })
        ->whereYear('fecha', $year) // Añadir filtro por año
        ->where('delegacion_id', $delegacionId) // Filtrar por la delegación seleccionada
        ->get();
        $totalGastosEstructurales = ['1T' => 0, '2T' => 0, '3T' => 0, '4T' => 0, 'anual' => 0];
        $gastosEstructuralesPorTrimestre = [];

        foreach ($gastosEstructurales as $gasto) {
            $mesGasto = Carbon::parse($gasto->fecha)->month;
            $trimestreGasto = ceil($mesGasto / 3); // Calcular el trimestre

            //inicializar el trimestre
            if (!isset($totalGastosEstructurales[$trimestreGasto])) {
                $totalGastosEstructurales[$trimestreGasto] = [];
            }

            if (!isset($gastosEstructuralesPorTrimestre[$trimestreGasto][$gasto->cuenta])) {
                $gastosEstructuralesPorTrimestre[$trimestreGasto][$gasto->cuenta] = [
                    'total' => 0,
                    'nombre' => $gasto->proveedor->nombre
                ];
            }


                $totalGastosEstructurales["{$trimestreGasto}T"] += $gasto->total + $gasto->total * ($porcentaje / 100);

            // Sumar el gasto en el trimestre correspondiente
            $gastosEstructuralesPorTrimestre[$trimestreGasto][$gasto->cuenta]['total'] += $gasto->total + $gasto->total * ($porcentaje / 100);
        }
        $gastosEstructuralesAnuales = [];
        foreach ($gastosEstructuralesPorTrimestre as $trimestre => $cuentas) {
            foreach ($cuentas as $cuenta => $data) {
                if (!isset($gastosEstructuralesAnuales[$cuenta])) {
                    $gastosEstructuralesAnuales[$cuenta] = 0;
                }
                $gastosEstructuralesAnuales[$cuenta] += $data['total'] + $data['total'] * ($porcentaje / 100);
            }
        }


        $totalGastosEstructurales['anual'] = $totalGastosEstructurales['1T'] + $totalGastosEstructurales['2T'] + $totalGastosEstructurales['3T'] + $totalGastosEstructurales['4T'];

        $gastosVariables = Caja::where('tipo_movimiento', 'Gasto')
        ->where(function ($query) {
            $query->where('cuenta', 'like', '6240%')
                ->orWhere('cuenta', 'like', '6291%')
                ->orWhere('cuenta', 'like', '6391%')
                ->orWhere('cuenta', 'like', '6460%')
                ->orWhere('cuenta', 'like', '6210%');
        })
        ->whereYear('fecha', $year) // Añadir filtro por año
        ->where('delegacion_id', $delegacionId) // Filtrar por la delegación seleccionada
        ->get();
        $totalGastosVariables = ['1T' => 0, '2T' => 0, '3T' => 0, '4T' => 0, 'anual' => 0];
        $gastosVariablesPorTrimestre = [];

        foreach ($gastosVariables as $gasto) {
            $mesGasto = Carbon::parse($gasto->fecha)->month;
            $trimestreGasto = ceil($mesGasto / 3); // Calcular el trimestre
    
             //inicializar el trimestre
             if (!isset($totalGastosVariables[$trimestreGasto])) {
                $totalGastosVariables[$trimestreGasto] = [];
            }

            if (!isset($gastosVariablesPorTrimestre[$trimestreGasto][$gasto->cuenta])) {
                $gastosVariablesPorTrimestre[$trimestreGasto][$gasto->cuenta] = [
                    'total' => 0,
                    'nombre' => $gasto->proveedor->nombre
                ];
            }


                $totalGastosVariables["{$trimestreGasto}T"] += $gasto->total + $gasto->total * ($porcentaje / 100);

            // Sumar el gasto en el trimestre correspondiente
            $gastosVariablesPorTrimestre[$trimestreGasto][$gasto->cuenta]['total'] += $gasto->total + $gasto->total * ($porcentaje / 100);


            // Sumar el gasto en el trimestre correspondiente
        }
        $totalGastosVariables['anual'] = $totalGastosVariables['1T'] + $totalGastosVariables['2T'] + $totalGastosVariables['3T'] + $totalGastosVariables['4T'];
        $gastosVariablesAnuales = [];
        foreach ($gastosVariablesPorTrimestre as $trimestre => $cuentas) {
            foreach ($cuentas as $cuenta => $data) {
                if (!isset($gastosVariablesAnuales[$cuenta])) {
                    $gastosVariablesAnuales[$cuenta] = 0;
                }
                $gastosVariablesAnuales[$cuenta] += $data['total'] + $data['total'] * ($porcentaje / 100);
            }
        }
        
    
       // dd($presupuestosPorTrimestre);  

       


    return view('control-presupuestario.proyeccion', compact(
        'presupuestosPorTrimestre', 'delegaciones', 'year', 'totalVentas', 'totalCompras', 'margenBeneficioReal', 'margenPorcentajeReal' , 'totalGastosEstructurales', 'gastosEstructurales',
        'totalGastosVariables', 'gastosVariables' , 'totalGastosEstructurales', 'gastosEstructurales', 'gastosEstructuralesPorTrimestre', 'gastosEstructuralesAnuales', 'gastosVariablesPorTrimestre' , 'gastosVariablesAnuales',
        'delegacion', 'porcentaje'

    ));
}

public function exportarProyeccionAPDF(Request $request)
{
    
      // Establecer la localización en español
      Carbon::setLocale('es');

      $year = $request->input('year', Carbon::now()->year); // Año actual por defecto
      $delegacionId = $request->input('delegacion'); // Delegación seleccionada por defecto
      $porcentaje = $request->input('porcentaje', 0); // Porcentaje  por defecto
      $delegacion = Delegacion::where('created_at', '!=', null)->find($delegacionId);
      // Obtener todas las delegaciones
      $delegaciones = Delegacion::where('created_at', '!=', null)->get();
      $presupuestosPorTrimestre = [];
  
      if(!$delegacion){
          return view('control-presupuestario.proyeccion', compact('delegaciones', 'year' , 'presupuestosPorTrimestre' , 'delegacion'));
      }
      // Costes
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
  
      // Obtener las facturas de la delegación seleccionada en el año
      $facturas = Facturas::whereYear('created_at', $year)
          ->whereHas('cliente.delegacion', function ($query) use ($delegacionId) {
              $query->where('id', $delegacionId);
          })
          ->with(['pedido.productosPedido.producto', 'cliente.delegacion'])
          ->orderBy('created_at', 'asc')
          ->get();
  
      // Inicializar array para agrupar datos por trimestre
      $presupuestosPorTrimestre = [];
      $totalVentas = ['1T' => 0, '2T' => 0, '3T' => 0, '4T' => 0, 'anual' => 0];
      $totalCompras = ['1T' => 0, '2T' => 0, '3T' => 0, '4T' => 0, 'anual' => 0];
  
      foreach ($facturas as $factura) {
          $mes = Carbon::parse($factura->created_at)->month;
          $trimestre = ceil($mes / 3); // Calcular el trimestre
  
          // Obtener la delegación de la factura
          $delegacionNombre = $factura->cliente->delegacion->nombre ?? 'General';
  
          // Inicializar el trimestre y mes en el array si no existen
          if (!isset($presupuestosPorTrimestre[$trimestre])) {
              $presupuestosPorTrimestre[$trimestre] = [];
          }
          if (!isset($presupuestosPorTrimestre[$trimestre][$mes])) {
              $presupuestosPorTrimestre[$trimestre][$mes] = [];
          }
  
          // Procesar los productos del pedido
          if ($factura->pedido) {
              foreach ($factura->pedido->productosPedido as $productoPedido) {
                  $productoNombre = $productoPedido->producto ? $productoPedido->producto->nombre : 'Producto no encontrado';
                  $productId = $productoPedido->producto ? $productoPedido->producto->id : 'Producto no encontrado';
                  $unidadesVendidas = $productoPedido->unidades;
                  $precioTotal = $productoPedido->precio_total; // Precio total del producto
  
                  // Inicializar el producto si no existe en el array
                  if (!isset($presupuestosPorTrimestre[$trimestre][$mes][$productoNombre])) {
                      $presupuestosPorTrimestre[$trimestre][$mes][$productoNombre] = [
                          'nombre' => $productoNombre,
                          'ventasDelegaciones' => [],
                      ];
                  }
  
                  // Obtener el coste para la delegación o el coste general
                  $delegacionCOD = $factura->cliente->delegacion->COD ?? 'General';
                  $costeProducto = $costesMap[$productId][$delegacionCOD] ?? $costesMap[$productId]['General'] ?? 0;
  
                  // Inicializar la delegación si no existe
                  if (!isset($presupuestosPorTrimestre[$trimestre][$mes][$productoNombre]['ventasDelegaciones'][$delegacionNombre])) {
                      $presupuestosPorTrimestre[$trimestre][$mes][$productoNombre]['ventasDelegaciones'][$delegacionNombre] = [
                          'unidadesVendidas' => 0,
                          'costeTotal' => 0,
                          'precioTotal' => 0,
                      ];
                  }
  
                  // Sumar unidades, precio con IVA, y calcular el coste total
                  $precioConIVA = $precioTotal + ($precioTotal * 0.21); // Precio total con IVA (21%)
  
                  $presupuestosPorTrimestre[$trimestre][$mes][$productoNombre]['ventasDelegaciones'][$delegacionNombre]['unidadesVendidas'] += $unidadesVendidas +  $unidadesVendidas * ($porcentaje / 100);
                  $presupuestosPorTrimestre[$trimestre][$mes][$productoNombre]['ventasDelegaciones'][$delegacionNombre]['precioTotal'] += $precioConIVA + $precioConIVA * ($porcentaje / 100); // Precio con IVA
                  $presupuestosPorTrimestre[$trimestre][$mes][$productoNombre]['ventasDelegaciones'][$delegacionNombre]['costeTotal'] += ($unidadesVendidas * $costeProducto) + ($unidadesVendidas * $costeProducto) * ($porcentaje / 100);
  
                  // Sumar ventas y compras totales por trimestre y anual
                  $totalVentas["{$trimestre}T"] += $precioConIVA + $precioConIVA * ($porcentaje / 100);
                  $totalCompras["{$trimestre}T"] += ($unidadesVendidas * $costeProducto) + ($unidadesVendidas * $costeProducto) * ($porcentaje / 100);
              }
          }
      }
  
      // Calcular las ventas y compras anuales
      $totalVentas['anual'] = $totalVentas['1T'] + $totalVentas['2T'] + $totalVentas['3T'] + $totalVentas['4T'];
      $totalCompras['anual'] = $totalCompras['1T'] + $totalCompras['2T'] + $totalCompras['3T'] + $totalCompras['4T'];
  
      // Calcular el margen de beneficio real
      $margenBeneficioReal = [
          '1T' => $totalVentas['1T'] - $totalCompras['1T'],
          '2T' => $totalVentas['2T'] - $totalCompras['2T'],
          '3T' => $totalVentas['3T'] - $totalCompras['3T'],
          '4T' => $totalVentas['4T'] - $totalCompras['4T'],
          'anual' => $totalVentas['anual'] - $totalCompras['anual']
      ];
  
      // Calcular el porcentaje de margen de beneficio real
      $margenPorcentajeReal = [
          '1T' => ($totalVentas['1T'] > 0) ? ($margenBeneficioReal['1T'] / $totalVentas['1T']) * 100 : 0,
          '2T' => ($totalVentas['2T'] > 0) ? ($margenBeneficioReal['2T'] / $totalVentas['2T']) * 100 : 0,
          '3T' => ($totalVentas['3T'] > 0) ? ($margenBeneficioReal['3T'] / $totalVentas['3T']) * 100 : 0,
          '4T' => ($totalVentas['4T'] > 0) ? ($margenBeneficioReal['4T'] / $totalVentas['4T']) * 100 : 0,
          'anual' => ($totalVentas['anual'] > 0) ? ($margenBeneficioReal['anual'] / $totalVentas['anual']) * 100 : 0,
      ];
  
      $gastosEstructurales = Caja::where('tipo_movimiento', 'Gasto')
      ->where(function ($query) {
          $query->where('cuenta', 'like', '1700%')
              ->orWhere('cuenta', 'like', '6290%')
              ->orWhere('cuenta', 'like', '6250%')
              ->orWhere('cuenta', 'like', '6210%')
              ->orWhere('cuenta', 'like', '6212%')
              ->orWhere('cuenta', 'like', '6293%')
              ->orWhere('cuenta', 'like', '6294%')
              ->orWhere('cuenta', 'like', '6295%')
              ->orWhere('cuenta', 'like', '4012%')
              ->orWhere('cuenta', 'like', '6400%');
          })
          ->whereYear('fecha', $year) // Añadir filtro por año
          ->where('delegacion_id', $delegacionId) // Filtrar por la delegación seleccionada
          ->get();
          $totalGastosEstructurales = ['1T' => 0, '2T' => 0, '3T' => 0, '4T' => 0, 'anual' => 0];
          $gastosEstructuralesPorTrimestre = [];
  
          foreach ($gastosEstructurales as $gasto) {
              $mesGasto = Carbon::parse($gasto->fecha)->month;
              $trimestreGasto = ceil($mesGasto / 3); // Calcular el trimestre
  
              //inicializar el trimestre
              if (!isset($totalGastosEstructurales[$trimestreGasto])) {
                  $totalGastosEstructurales[$trimestreGasto] = [];
              }
  
              if (!isset($gastosEstructuralesPorTrimestre[$trimestreGasto][$gasto->cuenta])) {
                  $gastosEstructuralesPorTrimestre[$trimestreGasto][$gasto->cuenta] = [
                      'total' => 0,
                      'nombre' => $gasto->proveedor->nombre
                  ];
              }
  
  
                  $totalGastosEstructurales["{$trimestreGasto}T"] += $gasto->total + $gasto->total * ($porcentaje / 100);
  
              // Sumar el gasto en el trimestre correspondiente
              $gastosEstructuralesPorTrimestre[$trimestreGasto][$gasto->cuenta]['total'] += $gasto->total + $gasto->total * ($porcentaje / 100);
          }
          $gastosEstructuralesAnuales = [];
          foreach ($gastosEstructuralesPorTrimestre as $trimestre => $cuentas) {
              foreach ($cuentas as $cuenta => $data) {
                  if (!isset($gastosEstructuralesAnuales[$cuenta])) {
                      $gastosEstructuralesAnuales[$cuenta] = 0;
                  }
                  $gastosEstructuralesAnuales[$cuenta] += $data['total'] + $data['total'] * ($porcentaje / 100);
              }
          }
  
  
          $totalGastosEstructurales['anual'] = $totalGastosEstructurales['1T'] + $totalGastosEstructurales['2T'] + $totalGastosEstructurales['3T'] + $totalGastosEstructurales['4T'];
  
          $gastosVariables = Caja::where('tipo_movimiento', 'Gasto')
          ->where(function ($query) {
              $query->where('cuenta', 'like', '6240%')
                  ->orWhere('cuenta', 'like', '6291%')
                  ->orWhere('cuenta', 'like', '6391%')
                  ->orWhere('cuenta', 'like', '6460%')
                  ->orWhere('cuenta', 'like', '6210%');
          })
          ->whereYear('fecha', $year) // Añadir filtro por año
          ->where('delegacion_id', $delegacionId) // Filtrar por la delegación seleccionada
          ->get();
          $totalGastosVariables = ['1T' => 0, '2T' => 0, '3T' => 0, '4T' => 0, 'anual' => 0];
          $gastosVariablesPorTrimestre = [];
  
          foreach ($gastosVariables as $gasto) {
              $mesGasto = Carbon::parse($gasto->fecha)->month;
              $trimestreGasto = ceil($mesGasto / 3); // Calcular el trimestre
      
               //inicializar el trimestre
               if (!isset($totalGastosVariables[$trimestreGasto])) {
                  $totalGastosVariables[$trimestreGasto] = [];
              }
  
              if (!isset($gastosVariablesPorTrimestre[$trimestreGasto][$gasto->cuenta])) {
                  $gastosVariablesPorTrimestre[$trimestreGasto][$gasto->cuenta] = [
                      'total' => 0,
                      'nombre' => $gasto->proveedor->nombre
                  ];
              }
  
  
                  $totalGastosVariables["{$trimestreGasto}T"] += $gasto->total + $gasto->total * ($porcentaje / 100);
  
              // Sumar el gasto en el trimestre correspondiente
              $gastosVariablesPorTrimestre[$trimestreGasto][$gasto->cuenta]['total'] += $gasto->total + $gasto->total * ($porcentaje / 100);
  
  
              // Sumar el gasto en el trimestre correspondiente
          }
          $totalGastosVariables['anual'] = $totalGastosVariables['1T'] + $totalGastosVariables['2T'] + $totalGastosVariables['3T'] + $totalGastosVariables['4T'];
          $gastosVariablesAnuales = [];
          foreach ($gastosVariablesPorTrimestre as $trimestre => $cuentas) {
              foreach ($cuentas as $cuenta => $data) {
                  if (!isset($gastosVariablesAnuales[$cuenta])) {
                      $gastosVariablesAnuales[$cuenta] = 0;
                  }
                  $gastosVariablesAnuales[$cuenta] += $data['total'] + $data['total'] * ($porcentaje / 100);
              }
          }

    

    $pdf = PDF::loadView('pdf.proyeccion', compact(
        'presupuestosPorTrimestre', 'delegaciones', 'year', 'totalVentas', 'totalCompras', 'margenBeneficioReal', 'margenPorcentajeReal' , 'totalGastosEstructurales', 'gastosEstructurales',
        'totalGastosVariables', 'gastosVariables' , 'totalGastosEstructurales', 'gastosEstructurales', 'gastosEstructuralesPorTrimestre', 'gastosEstructuralesAnuales', 'gastosVariablesPorTrimestre' , 'gastosVariablesAnuales',
        'delegacion', 'porcentaje'

    ))->setPaper('a1', 'landscape');

    return $pdf->download('proyeccion_presupuestaria.pdf');
}


public function exportarVentasAPDF(Request $request)
{
    $search = $request->input('search');
    $fechaMin = $request->input('fechaMin');
    $fechaMax = $request->input('fechaMax');

    // Obtenemos todos los productos disponibles
    $productos = Productos::all();

    // Consulta base de facturas con cliente y delegación relacionados
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

    // Obtener todas las facturas filtradas
    $facturasSinPaginacion = $query->get();

    // Calcular los totales por producto: unidades y euros
    $totalesProductos = $productos->map(function ($producto) use ($facturasSinPaginacion) {
        $totalUnidadesVendidas = 0;
        $totalEurosVendidos = 0;

        foreach ($facturasSinPaginacion as $factura) {
            if ($factura->factura_id && $factura->productosFacturas) {
                $productoFactura = $factura->productosFacturas->firstWhere('producto_id', $producto->id);
                if ($productoFactura) {
                    $totalUnidadesVendidas -= $productoFactura->cantidad;
                }
            } else if ($factura->pedido && $factura->pedido->productosPedido) {
                $productoPedido = $factura->pedido->productosPedido->firstWhere('producto_pedido_id', $producto->id);
                if ($productoPedido) {
                    $totalUnidadesVendidas += $productoPedido->unidades;
                }
            }
        }

        $producto->total_unidades_vendidas = $totalUnidadesVendidas;
        $producto->total_euros_vendidos = $totalEurosVendidos;

        return $producto;
    });

    // Calcular el total en euros de todas las facturas sumadas
    $totalEurosFacturas = $facturasSinPaginacion->reduce(function ($carry, $factura) {
        $hasIva = FacturaHelper::facturaHasIva($factura->id);
        if ($factura->factura_id && $factura->facturaNormal) {
            return $carry + ($hasIva ? $factura->total - $factura->facturaNormal->total : $factura->precio - $factura->facturaNormal->precio);
        }
        return $carry + ($hasIva ? $factura->total : $factura->precio);
    }, 0);

    // Generar el PDF
    $pdf = PDF::loadView('pdf.ventas', compact('facturasSinPaginacion', 'productos', 'totalesProductos', 'totalEurosFacturas', 'search', 'fechaMin', 'fechaMax')) ->setPaper('a1', 'landscape');
    return $pdf->download('ventas.pdf');
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

    $delegaciones = Delegacion::where('created_at', '!=', null)->get();

    return view('control-presupuestario.logistica', compact(
        'gastosTransportePorTrimestre', 'totalesPorDelegacion', 'totalPorTrimestre', 'delegaciones', 'year'
    ));
}

public function exportarLogisticaAPDF(Request $request)
{
    Carbon::setLocale('es');

    $year = $request->input('year', Carbon::now()->year);

    // Obtener los pedidos con gastos de transporte
    $gastosTransporte = Pedido::whereYear('created_at', $year)
        ->where('gastos_transporte', '!=', 0)
        ->with(['cliente.delegacion'])
        ->orderBy('created_at', 'asc')
        ->get();

    $gastosTransportePorTrimestre = [];
    $totalesPorDelegacion = [];
    $totalPorTrimestre = [];

    foreach ($gastosTransporte as $gastoTransporte) {
        $mes = Carbon::parse($gastoTransporte->created_at)->month;
        $trimestre = ceil($mes / 3);
        $delegacionNombre = $gastoTransporte->cliente->delegacion->nombre ?? 'General';

        if (!isset($gastosTransportePorTrimestre[$trimestre])) {
            $gastosTransportePorTrimestre[$trimestre] = [];
        }
        if (!isset($gastosTransportePorTrimestre[$trimestre][$mes])) {
            $gastosTransportePorTrimestre[$trimestre][$mes] = [];
        }
        if (!isset($gastosTransportePorTrimestre[$trimestre][$mes][$delegacionNombre])) {
            $gastosTransportePorTrimestre[$trimestre][$mes][$delegacionNombre] = 0;
        }

        $gastosTransportePorTrimestre[$trimestre][$mes][$delegacionNombre] += $gastoTransporte->gastos_transporte;

        if (!isset($totalesPorDelegacion[$delegacionNombre])) {
            $totalesPorDelegacion[$delegacionNombre] = 0;
        }
        $totalesPorDelegacion[$delegacionNombre] += $gastoTransporte->gastos_transporte;

        if (!isset($totalPorTrimestre[$trimestre])) {
            $totalPorTrimestre[$trimestre] = [];
        }
        if (!isset($totalPorTrimestre[$trimestre][$delegacionNombre])) {
            $totalPorTrimestre[$trimestre][$delegacionNombre] = 0;
        }
        $totalPorTrimestre[$trimestre][$delegacionNombre] += $gastoTransporte->gastos_transporte;
    }

    $delegaciones = Delegacion::where('created_at', '!=', null)->get();

    // Generar el PDF
    $pdf = PDF::loadView('pdf.logistica', compact(
        'gastosTransportePorTrimestre', 'totalesPorDelegacion', 'totalPorTrimestre', 'delegaciones', 'year'
    ))->setPaper('a2', 'landscape');
    return $pdf->download('logistica.pdf');
}
    
    
public function comerciales(Request $request)
{
    // Establecer la localización en español
    Carbon::setLocale('es');

    $year = $request->input('year', Carbon::now()->year); // Año actual por defecto

    // Obtener todos los productos cuyo precio_ud es 0
    $productosGratis = Productos::all();
    $delegaciones = Delegacion::where('created_at', '!=', null)->get();

    // Obtener todos los pedidos sin paginación y dentro del año seleccionado
    $pedidos = Pedido::whereYear('created_at', $year)
        ->with(['productosPedido.producto', 'cliente.delegacion']) // Asegúrate de cargar la relación de cliente y delegación
        ->orderBy('created_at', 'asc')
        ->get();

    // Obtener los costes por año
    $costes = CostesProductos::query()
    ->with('productos')
    ->whereYear('fecha', '<=', $year) // Costes hasta el año actual
    ->get()
    ->groupBy('producto_id'); // Agrupar por producto

    // Mapa de costes por producto y delegación
    $costesMap = [];
    foreach ($costes as $productId => $costesProducto) {
        // Asegurarse de que $costesProducto sea una colección
        if ($costesProducto instanceof \Illuminate\Support\Collection) {
            $costeActual = $costesProducto->filter(function($coste) use ($year) {
                return Carbon::parse($coste->fecha)->year <= $year;
            })->sortByDesc('fecha')->first(); // Obtener el último coste para el año
    
            if ($costeActual) {
                $costesMap[$productId]['General'] = $costeActual->coste; // Todos usan el mismo coste
            }
        }
    }

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

                // Obtener el coste para el producto
                $costeProducto = $costesMap[$productId]['General'] ?? 0;

                // Sumar las unidades vendidas y calcular el coste total
                $ventasPorTrimestre[$trimestre][$mes][$productoNombre]['ventasDelegaciones'][$delegacionNombre]['unidadesVendidas'] += $unidadesVendidas;
                $ventasPorTrimestre[$trimestre][$mes][$productoNombre]['ventasDelegaciones'][$delegacionNombre]['costeTotal'] += $unidadesVendidas * $costeProducto;
            }
        } catch (\Exception $e) {
            continue;
        }
    }
}
// dd("hola");

   // dd($ventasPorTrimestre);

    // Agrupar los costes por delegación
    $costesPorDelegacion = [];
    foreach ($delegaciones as $delegacion) {
        // Asignar directamente el array de costes a cada delegación
        $costesPorDelegacion[$delegacion->nombre] = $costes->toArray();
    }


    // Añadir la delegación "General" por si acaso
    $costesPorDelegacion['General'] = $costes->toArray();

    return view('control-presupuestario.comerciales', compact(
        'ventasPorTrimestre', 'productosGratis', 'delegaciones', 'costesPorDelegacion', 'year'
    ));
}

public function exportarComercialesAPDF(Request $request)
{
    // Establecer la localización en español
    Carbon::setLocale('es');

    $year = $request->input('year', Carbon::now()->year); // Año actual por defecto

    // Obtener todos los productos cuyo precio_ud es 0
    $productosGratis = Productos::all();
    $delegaciones = Delegacion::where('created_at', '!=', null)->get();

    // Obtener todos los pedidos sin paginación y dentro del año seleccionado
    $pedidos = Pedido::whereYear('created_at', $year)
        ->with(['productosPedido.producto', 'cliente.delegacion']) // Asegúrate de cargar la relación de cliente y delegación
        ->orderBy('created_at', 'asc')
        ->get();

    // Obtener los costes por año
    $costes = CostesProductos::query()
    ->with('productos')
    ->whereYear('fecha', '<=', $year) // Costes hasta el año actual
    ->get()
    ->groupBy('producto_id'); // Agrupar por producto

    // Mapa de costes por producto y delegación
    $costesMap = [];
    foreach ($costes as $productId => $costesProducto) {
        // Asegurarse de que $costesProducto sea una colección
        if ($costesProducto instanceof \Illuminate\Support\Collection) {
            $costeActual = $costesProducto->filter(function($coste) use ($year) {
                return Carbon::parse($coste->fecha)->year <= $year;
            })->sortByDesc('fecha')->first(); // Obtener el último coste para el año
    
            if ($costeActual) {
                $costesMap[$productId]['General'] = $costeActual->coste; // Todos usan el mismo coste
            }
        }
    }

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

                // Obtener el coste para el producto
                $costeProducto = $costesMap[$productId]['General'] ?? 0;

                // Sumar las unidades vendidas y calcular el coste total
                $ventasPorTrimestre[$trimestre][$mes][$productoNombre]['ventasDelegaciones'][$delegacionNombre]['unidadesVendidas'] += $unidadesVendidas;
                $ventasPorTrimestre[$trimestre][$mes][$productoNombre]['ventasDelegaciones'][$delegacionNombre]['costeTotal'] += $unidadesVendidas * $costeProducto;
            }
        } catch (\Exception $e) {
            continue;
        }
    }
}
// dd("hola");

   // dd($ventasPorTrimestre);

    // Agrupar los costes por delegación
    $costesPorDelegacion = [];
    foreach ($delegaciones as $delegacion) {
        // Asignar directamente el array de costes a cada delegación
        $costesPorDelegacion[$delegacion->nombre] = $costes->toArray();
    }


    // Añadir la delegación "General" por si acaso
    $costesPorDelegacion['General'] = $costes->toArray();

    // Generar el PDF
    $pdf = PDF::loadView('pdf.comerciales', compact(
        'ventasPorTrimestre', 'productosGratis', 'delegaciones', 'costesPorDelegacion', 'year'
    ))->setPaper('a2', 'landscape');
    return $pdf->download('comerciales.pdf');
}


public function marketing(Request $request)
{
    // Establecer la localización en español
    Carbon::setLocale('es');

    $year = $request->input('year', Carbon::now()->year); // Año actual por defecto

    // Obtener todas las delegaciones
    $delegaciones = Delegacion::where('created_at', '!=', null)->get();
    $productos2 = Productos::all();
    $productosMarketing = ProductosMarketing::all();

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
        ->with(['productosPedido.producto', 'productosMarketingPedido.producto', 'cliente.delegacion'])
        ->get();

    // Obtener los costes de productos normales por año
    $costesProductos = CostesProductos::whereYear('fecha', $year)
        ->with('productos')
        ->get()
        ->groupBy('producto_id');


    // Obtener los costes de productos de marketing por año
    $costesMarketing = CostesProductosMarketing::whereYear('fecha', $year)
        ->with('producto')
        ->get()
        ->groupBy('producto_id');

        // dd($costesProductos, $costesMarketing);

    // Mapa de costes por producto y delegación
    $costesMapProductos = [];
    foreach ($costesProductos as $productId => $costesProducto) {
        $costeActual = $costesProducto->sortByDesc('fecha')->first(); // Obtener el último coste para el año
        if ($costeActual) {
            // dd($costeActual);
            $costesMapProductos[$productId]['General'] = $costeActual->coste; // Todos usan el mismo coste
        }
    }

    

    $costesMapMarketing = [];
    foreach ($costesMarketing as $productId => $costesProducto) {
        $costeActual = $costesProducto->sortByDesc('fecha')->first(); // Obtener el último coste para el año
        if ($costeActual) {
            $costesMapMarketing[$productId]['General'] = $costeActual->coste; // Todos usan el mismo coste
        }
    }


    // Calcular las ventas por trimestre, mes, producto y delegación
    $ventasPorTrimestre = [];
    $ventasMarketingPorTrimestre = [];

    foreach ($pedidos as $pedido) {
        $mes = Carbon::parse($pedido->created_at)->month; // Obtener el mes del pedido
        $trimestre = ceil($mes / 3); // Calcular el trimestre (1 = Q1, 2 = Q2, etc.)

        $delegacionNombre = $pedido->cliente->delegacion->nombre ?? 'General'; // Obtener la delegación o 'General' si no tiene

        // Inicializar el trimestre y mes en el array si no existen
        if (!isset($ventasPorTrimestre[$trimestre])) {
            $ventasPorTrimestre[$trimestre] = [];
            $ventasMarketingPorTrimestre[$trimestre] = [];
        }
        if (!isset($ventasPorTrimestre[$trimestre][$mes])) {
            $ventasPorTrimestre[$trimestre][$mes] = [];
            $ventasMarketingPorTrimestre[$trimestre][$mes] = [];
        }

        // Procesar los productos del pedido para registrar las ventas por producto
        foreach ($pedido->productosPedido as $productoPedido) {
            try {
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
                $costeProducto = $costesMapProductos[$productId][$delegacionCOD] ?? $costesMapProductos[$productId]['General'] ?? 0;

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

        // Procesar los productos de marketing del pedido
        foreach ($pedido->productosMarketingPedido as $productoMarketingPedido) {
            try {
                $productoNombre = $productoMarketingPedido->producto->nombre;
                $productId = $productoMarketingPedido->producto->id;
                $unidadesVendidas = $productoMarketingPedido->unidades;

                // Inicializar el producto en el mes si no existe
                if (!isset($ventasMarketingPorTrimestre[$trimestre][$mes][$productoNombre])) {
                    $ventasMarketingPorTrimestre[$trimestre][$mes][$productoNombre] = [
                        'nombre' => $productoNombre,
                        'ventasDelegaciones' => [],
                    ];
                }

                $delegacionCOD = $pedido->cliente->delegacion->COD ?? 'General';
                $costeProducto = $costesMapMarketing[$productId][$delegacionCOD] ?? $costesMapMarketing[$productId]['General'] ?? 0;

                // Inicializar la delegación si no existe para este producto
                if (!isset($ventasMarketingPorTrimestre[$trimestre][$mes][$productoNombre]['ventasDelegaciones'][$delegacionNombre])) {
                    $ventasMarketingPorTrimestre[$trimestre][$mes][$productoNombre]['ventasDelegaciones'][$delegacionNombre] = [
                        'unidadesVendidas' => 0,
                        'costeTotal' => 0,
                    ];
                }

                // Sumar las unidades vendidas y calcular el coste total
                $ventasMarketingPorTrimestre[$trimestre][$mes][$productoNombre]['ventasDelegaciones'][$delegacionNombre]['unidadesVendidas'] += $unidadesVendidas;
                $ventasMarketingPorTrimestre[$trimestre][$mes][$productoNombre]['ventasDelegaciones'][$delegacionNombre]['costeTotal'] += $unidadesVendidas * $costeProducto;
            } catch (\Exception $e) {
                continue;
            }
        }
    }


    $costesProductosPorDelegacion = [];
    foreach ($delegaciones as $delegacion) {
        // Asignar directamente el array de costes a cada delegación
        $costesProductosPorDelegacion[$delegacion->nombre] = $costesProductos->toArray();
    }
    // Agrupar los costes por delegación
  

    $costesMarketingPorDelegacion = [];
    foreach ($delegaciones as $delegacion) {
        // Asignar directamente el array de costes a cada delegación
        $costesMarketingPorDelegacion[$delegacion->nombre] = $costesMarketing->toArray();
    }

  

    return view('control-presupuestario.marketing', compact('cajaPorTrimestre', 'ventasPorTrimestre', 'ventasMarketingPorTrimestre', 'delegaciones', 'year', 'costesProductosPorDelegacion', 'costesMarketingPorDelegacion', 'productos2', 'productosMarketing'));
}

public function exportarMarketingAPDF(Request $request)
{
    Carbon::setLocale('es');

    $year = $request->input('year', Carbon::now()->year);

    // Obtener los datos necesarios para la vista
    $delegaciones = Delegacion::where('created_at', '!=', null)->get();
    $productos2 = Productos::all();
    $cajas = Caja::where('departamento', 'marketing')
        ->whereYear('fecha', $year)
        ->where('cuenta', 'like', '6270%')
        ->with(['facturas', 'delegacion'])
        ->get();

    $cajaPorTrimestre = [];
    foreach ($cajas as $caja) {
        $mes = Carbon::parse($caja->fecha)->month;
        $trimestre = ceil($mes / 3);
        $delegacionNombre = $caja->delegacion->nombre ?? 'General';

        if (!isset($cajaPorTrimestre[$trimestre])) {
            $cajaPorTrimestre[$trimestre] = [];
        }
        if (!isset($cajaPorTrimestre[$trimestre][$mes])) {
            $cajaPorTrimestre[$trimestre][$mes] = [];
        }
        if (!isset($cajaPorTrimestre[$trimestre][$mes][$delegacionNombre])) {
            $cajaPorTrimestre[$trimestre][$mes][$delegacionNombre] = 0;
        }

        $cajaPorTrimestre[$trimestre][$mes][$delegacionNombre] += $caja->total;
    }

    $pdf = PDF::loadView('pdf.marketing', compact('cajaPorTrimestre', 'delegaciones', 'year'))->setPaper('a2', 'landscape');
    return $pdf->download('marketing.pdf');
}

public function analisisVentas(Request $request)
{
    $year = $request->input('year', Carbon::now()->year); // Obtener el año actual si no se proporciona
    $trimestre = $request->input('trimestre', 1); // Obtener el trimestre, por defecto es el 1º trimestre

    // Definir los meses para cada trimestre
    $mesesPorTrimestre = [
        1 => [1, 2, 3],   // 1er trimestre
        2 => [4, 5, 6],   // 2º trimestre
        3 => [7, 8, 9],   // 3er trimestre
        4 => [10, 11, 12] // 4º trimestre
    ];

    // Obtener los meses correspondientes al trimestre seleccionado
    $meses = $mesesPorTrimestre[$trimestre] ?? [1, 2, 3]; // Si no se selecciona trimestre, usar el 1º trimestre por defecto

    $totalGeneralVentas = 0; // Para almacenar el total de ventas general

    // Obtener todas las delegaciones, asegurándonos de incluir "General" si no existe
    $delegaciones = Delegacion::where('created_at', '!=', null)->orderBy('id')->get();
    $delegaciones = $delegaciones->concat(collect([(object)['id' => 0, 'nombre' => 'General']]));

    // Inicializar arrays para almacenar ventas por delegación y mes
    $ventasPorDelegacion = [];
    $porcentajeVentasPorDelegacion = [];
    
    // Inicializamos las ventas y totales por delegación
    foreach ($meses as $mes) {
        foreach ($delegaciones as $delegacion) {
            $ventasPorDelegacion[$mes][$delegacion->nombre] = 0;
        }
    }
    foreach ($delegaciones as $delegacion) {
        $porcentajeVentasPorDelegacion[$delegacion->nombre] = 0;
    }

    // Obtener todas las facturas filtradas por año y trimestre
    $facturas = Facturas::whereYear('created_at', $year)
        ->whereMonth('created_at', '>=', $meses[0])
        ->whereMonth('created_at', '<=', $meses[2])
        ->with(['cliente.delegacion', 'pedido.productosPedido.producto'])
        ->get();

    // Sumar las ventas por delegación y mes
    foreach ($facturas as $factura) {
        $mes = Carbon::parse($factura->created_at)->month;
        $delegacionNombre = $factura->cliente->delegacion->nombre ?? 'General';
        
        // Añadir el total de la factura a la delegación correspondiente
        if(isset( $ventasPorDelegacion[$mes][$delegacionNombre])){
            $ventasPorDelegacion[$mes][$delegacionNombre] += $factura->total;
        }
        $totalGeneralVentas += $factura->total;
    }

    // Calcular las ventas totales por delegación (suma de todos los meses)
    $totalesPorDelegacion = [];
    foreach ($delegaciones as $delegacion) {
        $totalesPorDelegacion[$delegacion->nombre] = array_sum(array_column($ventasPorDelegacion, $delegacion->nombre));
    }

    // Calcular el porcentaje de ventas por delegación
    foreach ($delegaciones as $delegacion) {
        if ($totalGeneralVentas > 0) {
            $porcentajeVentasPorDelegacion[$delegacion->nombre] = ($totalesPorDelegacion[$delegacion->nombre] / $totalGeneralVentas) * 100;
        } else {
            $porcentajeVentasPorDelegacion[$delegacion->nombre] = 0;
        }
    }

    // -------------------
    // Lógica adicional para el análisis de ventas por producto
    // -------------------

    // Obtener todos los productos
    $productos = Productos::all();

    // Inicializar arrays para almacenar las ventas por producto, delegación y mes
    $ventasPorProducto = [];

    // Inicializar las ventas para cada producto, delegación y mes
    foreach ($productos as $producto) {
        foreach ($meses as $mes) {
            foreach ($delegaciones as $delegacion) {
                $ventasPorProducto[$producto->id][$mes][$delegacion->nombre] = 0;
            }
        }
    }

    // Sumar las ventas por producto, delegación y mes
    foreach ($facturas as $factura) {
        $mes = Carbon::parse($factura->created_at)->month;
        $delegacionNombre = $factura->cliente->delegacion->nombre ?? 'General';

        if ($factura->pedido) {
            foreach ($factura->pedido->productosPedido as $productoPedido) {
                // Verificar que el producto esté presente antes de acceder a sus propiedades
                if ($productoPedido->producto) {
                    $productoId = $productoPedido->producto->id;
                    $unidadesVendidas = $productoPedido->unidades;

                    // Añadir el número de unidades vendidas al producto correspondiente, delegación y mes
                    $ventasPorProducto[$productoId][$mes][$delegacionNombre] += $unidadesVendidas;
                }
            }
        }
    }

    // Calcular los totales por producto, delegación y mes
    $totalesPorProducto = [];
    foreach ($productos as $producto) {
        foreach ($delegaciones as $delegacion) {
            $totalesPorProducto[$producto->id][$delegacion->nombre] = array_sum(array_column($ventasPorProducto[$producto->id], $delegacion->nombre));
        }
    }

    // -------------------
    // Renderizar la vista
    // -------------------
    return view('control-presupuestario.analisis-ventas', compact(
        'year',
        'trimestre',
        'ventasPorDelegacion', 
        'totalesPorDelegacion', 
        'porcentajeVentasPorDelegacion', 
        'meses', 
        'delegaciones', 
        'totalGeneralVentas',
        'productos', // Añadir los productos al compact
        'ventasPorProducto', // Añadir las ventas por producto al compact
        'totalesPorProducto' // Añadir los totales por producto al compact
    ));
}


public function exportarAnalisisVentasAPDF(Request $request)
{
    $year = $request->input('year', Carbon::now()->year);
    $trimestre = $request->input('trimestre', 1);

    // Definir los meses para cada trimestre
    $mesesPorTrimestre = [
        1 => [1, 2, 3],
        2 => [4, 5, 6],
        3 => [7, 8, 9],
        4 => [10, 11, 12]
    ];

    // Obtener los meses correspondientes al trimestre seleccionado
    $meses = $mesesPorTrimestre[$trimestre] ?? [1, 2, 3];

    // Obtener todas las delegaciones
    $delegaciones = Delegacion::where('created_at', '!=', null)->orderBy('id')->get();
    $delegaciones = $delegaciones->concat(collect([(object)['id' => 0, 'nombre' => 'General']]));

    // Inicializar arrays para almacenar ventas por delegación y mes
    $ventasPorDelegacion = [];
    $porcentajeVentasPorDelegacion = [];
    $totalGeneralVentas = 0;

    // Inicializar las ventas y totales por delegación
    foreach ($meses as $mes) {
        foreach ($delegaciones as $delegacion) {
            $ventasPorDelegacion[$mes][$delegacion->nombre] = 0;
        }
    }
    foreach ($delegaciones as $delegacion) {
        $porcentajeVentasPorDelegacion[$delegacion->nombre] = 0;
    }

    // Obtener todas las facturas filtradas por año y trimestre
    $facturas = Facturas::whereYear('created_at', $year)
        ->whereMonth('created_at', '>=', $meses[0])
        ->whereMonth('created_at', '<=', $meses[2])
        ->with(['cliente.delegacion', 'pedido.productosPedido.producto'])
        ->get();

    // Sumar las ventas por delegación y mes
    foreach ($facturas as $factura) {
        $mes = Carbon::parse($factura->created_at)->month;
        $delegacionNombre = $factura->cliente->delegacion->nombre ?? 'General';
        
        $ventasPorDelegacion[$mes][$delegacionNombre] += $factura->total;
        $totalGeneralVentas += $factura->total;
    }

    // Calcular las ventas totales por delegación
    $totalesPorDelegacion = [];
    foreach ($delegaciones as $delegacion) {
        $totalesPorDelegacion[$delegacion->nombre] = array_sum(array_column($ventasPorDelegacion, $delegacion->nombre));
    }

    // Calcular el porcentaje de ventas por delegación
    foreach ($delegaciones as $delegacion) {
        if ($totalGeneralVentas > 0) {
            $porcentajeVentasPorDelegacion[$delegacion->nombre] = ($totalesPorDelegacion[$delegacion->nombre] / $totalGeneralVentas) * 100;
        } else {
            $porcentajeVentasPorDelegacion[$delegacion->nombre] = 0;
        }
    }

    // Obtener todos los productos
    $productos = Productos::all();

    // Inicializar arrays para almacenar las ventas por producto, delegación y mes
    $ventasPorProducto = [];

    // Inicializar las ventas para cada producto, delegación y mes
    foreach ($productos as $producto) {
        foreach ($meses as $mes) {
            foreach ($delegaciones as $delegacion) {
                $ventasPorProducto[$producto->id][$mes][$delegacion->nombre] = 0;
            }
        }
    }

    // Sumar las ventas por producto, delegación y mes
    foreach ($facturas as $factura) {
        $mes = Carbon::parse($factura->created_at)->month;
        $delegacionNombre = $factura->cliente->delegacion->nombre ?? 'General';

        if ($factura->pedido) {
            foreach ($factura->pedido->productosPedido as $productoPedido) {
                if ($productoPedido->producto) {
                    $productoId = $productoPedido->producto->id;
                    $unidadesVendidas = $productoPedido->unidades;

                    $ventasPorProducto[$productoId][$mes][$delegacionNombre] += $unidadesVendidas;
                }
            }
        }
    }

    // Calcular los totales por producto, delegación y mes
    $totalesPorProducto = [];
    foreach ($productos as $producto) {
        foreach ($delegaciones as $delegacion) {
            $totalesPorProducto[$producto->id][$delegacion->nombre] = array_sum(array_column($ventasPorProducto[$producto->id], $delegacion->nombre));
        }
    }

    // Generar el PDF
    $pdf = PDF::loadView('pdf.analisis-ventas', compact(
        'year',
        'trimestre',
        'ventasPorDelegacion', 
        'totalesPorDelegacion', 
        'porcentajeVentasPorDelegacion', 
        'meses', 
        'delegaciones', 
        'totalGeneralVentas',
        'productos',
        'ventasPorProducto',
        'totalesPorProducto'
    ));

    return $pdf->download('analisis_ventas.pdf');
}







public function patrocinios(Request $request)
{
    // Establecer la localización en español
    Carbon::setLocale('es');

    $year = $request->input('year', Carbon::now()->year); // Año actual por defecto

    // Obtener todas las delegaciones
    $delegaciones = Delegacion::where('created_at', '!=', null)->get();
    $productos2 = Productos::all();
    // Obtener los registros de caja del departamento "patrocinios"
    $cajasPatrocinios = Caja::where('departamento', 'patrocinios')
        ->whereYear('fecha', $year)
        ->with('delegacion') // Relación con delegación
        ->get();

    // Inicializar el array para organizar las cajas por trimestre, mes y delegación
    $cajaPorTrimestre = [];

    foreach ($cajasPatrocinios as $caja) {
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
    }

    // Obtener los costes por año
    $costes = Costes::where('year', $year)
        ->with('producto', 'delegacion')
        ->get();


// Agrupar los costes por delegación
 $costesPorDelegacion = $costes->groupBy(function ($coste) {
    return $coste->delegacion ? $coste->delegacion->nombre : 'General';
});
    return view('control-presupuestario.patrocinios', compact('cajaPorTrimestre', 'delegaciones', 'year' , 'costesPorDelegacion' , 'productos2'));
}

public function exportarPatrociniosAPDF(Request $request)
{
    Carbon::setLocale('es');

    $year = $request->input('year', Carbon::now()->year);

    // Obtener los datos necesarios para la vista
    $delegaciones = Delegacion::where('created_at', '!=', null)->get();
    $productos2 = Productos::all();
    $cajasPatrocinios = Caja::where('departamento', 'patrocinios')
        ->whereYear('fecha', $year)
        ->with('delegacion')
        ->get();

    $cajaPorTrimestre = [];
    foreach ($cajasPatrocinios as $caja) {
        $mes = Carbon::parse($caja->fecha)->month;
        $trimestre = ceil($mes / 3);
        $delegacionNombre = $caja->delegacion->nombre ?? 'General';

        if (!isset($cajaPorTrimestre[$trimestre])) {
            $cajaPorTrimestre[$trimestre] = [];
        }
        if (!isset($cajaPorTrimestre[$trimestre][$mes])) {
            $cajaPorTrimestre[$trimestre][$mes] = [];
        }
        if (!isset($cajaPorTrimestre[$trimestre][$mes][$delegacionNombre])) {
            $cajaPorTrimestre[$trimestre][$mes][$delegacionNombre] = 0;
        }

        $cajaPorTrimestre[$trimestre][$mes][$delegacionNombre] += $caja->total;
    }

    $pdf = PDF::loadView('pdf.patrocinios', compact('cajaPorTrimestre', 'delegaciones', 'year'))->setPaper('a2', 'landscape');
    return $pdf->download('patrocinios.pdf');
}

public function gastos(Request $request)
{
    Carbon::setLocale('es');

    $year = $request->input('year', Carbon::now()->year); // Año actual por defecto

    // Obtener todas las delegaciones
    $delegaciones = Delegacion::where('created_at', '!=', null)->get();

    // Obtener los gastos de los departamentos de administración y RRHH
    $gastos = Caja::whereIn('departamento', ['administracion', 'rrhh'])
        ->whereYear('fecha', $year)
        ->where('tipo_movimiento', 'Gasto')
        ->with('delegacion', 'proveedor') // Asegúrate de cargar la relación con proveedor
        ->get();

    // Inicializar el array para organizar los gastos por trimestre, mes, delegación y proveedor
    $gastosPorTrimestre = [];
    $proveedores = []; // Inicializar el array de proveedores

    foreach ($gastos as $gasto) {
        $mes = Carbon::parse($gasto->fecha)->month; // Obtener el mes del gasto
        $trimestre = ceil($mes / 3); // Calcular el trimestre (1 = Q1, 2 = Q2, etc.)
        $proveedorNombre = $gasto->proveedor->nombre ?? 'Proveedor desconocido'; // Obtener el nombre del proveedor
        $delegacionNombre = $gasto->delegacion->nombre ?? 'General'; // Obtener el nombre de la delegación

        // Añadir el proveedor al array de proveedores si no existe
        if (!in_array($proveedorNombre, $proveedores)) {
            $proveedores[] = $proveedorNombre;
        }

        // Inicializar el trimestre, mes y delegación en el array si no existen
        if (!isset($gastosPorTrimestre[$trimestre])) {
            $gastosPorTrimestre[$trimestre] = [];
        }
        if (!isset($gastosPorTrimestre[$trimestre][$mes])) {
            $gastosPorTrimestre[$trimestre][$mes] = [];
        }
        if (!isset($gastosPorTrimestre[$trimestre][$mes][$delegacionNombre])) {
            $gastosPorTrimestre[$trimestre][$mes][$delegacionNombre] = [];
        }

        // Inicializar el proveedor en el array si no existe
        if (!isset($gastosPorTrimestre[$trimestre][$mes][$delegacionNombre][$proveedorNombre])) {
            $gastosPorTrimestre[$trimestre][$mes][$delegacionNombre][$proveedorNombre] = 0;
        }

        // Sumar el total de gastos por proveedor y mes
        $gastosPorTrimestre[$trimestre][$mes][$delegacionNombre][$proveedorNombre] += $gasto->total;
    }

    return view('control-presupuestario.gastos', compact('gastosPorTrimestre', 'delegaciones', 'year', 'proveedores'));
}

public function exportarGastosAPDF(Request $request)
{
    Carbon::setLocale('es');

    $year = $request->input('year', Carbon::now()->year);

    $delegaciones = Delegacion::where('created_at', '!=', null)->get();
    $gastos = Caja::whereIn('departamento', ['administracion', 'rrhh'])
        ->whereYear('fecha', $year)
        ->where('tipo_movimiento', 'Gasto')
        ->with('delegacion', 'proveedor')
        ->get();

    $gastosPorTrimestre = [];
    $proveedores = [];

    foreach ($gastos as $gasto) {
        $mes = Carbon::parse($gasto->fecha)->month;
        $trimestre = ceil($mes / 3);
        $proveedorNombre = $gasto->proveedor->nombre ?? 'Proveedor desconocido';
        $delegacionNombre = $gasto->delegacion->nombre ?? 'General';

        if (!in_array($proveedorNombre, $proveedores)) {
            $proveedores[] = $proveedorNombre;
        }

        if (!isset($gastosPorTrimestre[$trimestre])) {
            $gastosPorTrimestre[$trimestre] = [];
        }
        if (!isset($gastosPorTrimestre[$trimestre][$mes])) {
            $gastosPorTrimestre[$trimestre][$mes] = [];
        }
        if (!isset($gastosPorTrimestre[$trimestre][$mes][$delegacionNombre])) {
            $gastosPorTrimestre[$trimestre][$mes][$delegacionNombre] = [];
        }

        if (!isset($gastosPorTrimestre[$trimestre][$mes][$delegacionNombre][$proveedorNombre])) {
            $gastosPorTrimestre[$trimestre][$mes][$delegacionNombre][$proveedorNombre] = 0;
        }

        $gastosPorTrimestre[$trimestre][$mes][$delegacionNombre][$proveedorNombre] += $gasto->total;
    }

    $pdf = PDF::loadView('control-presupuestario.gastos-pdf', compact('gastosPorTrimestre', 'delegaciones', 'year', 'proveedores'))
              ->setPaper('a3', 'landscape'); // Usar A4 para cada sección

    return $pdf->download('gastos.pdf');
}


}
