<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Caja;
use App\Models\Proveedores;

use App\Helpers\GlobalFunctions;
use App\Models\CuentasContable;
use App\Models\SubCuentaContable;
use App\Models\SubCuentaHijo;
use App\Models\GrupoContable;
use App\Models\SubGrupoContable;

class ContabilidadController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        // Cargar las cuentas contables utilizando el helper
        $cuentasContables = GlobalFunctions::loadCuentasContables();

        // Determinar el número de transacciones por página (por defecto 10)
        $perPage = $request->input('per_page', 10);

        // Crear una consulta base
        $query = Caja::with(['proveedor', 'facturas.cliente'])
            ->whereNotNull('asientoContable') // Filtrar solo cajas con asiento contable
            ->orderBy('asientoContable', 'asc'); // Ordenar por asiento contable

        // Aplicar filtro de cuenta contable
        if ($request->filled('cuentaContable_id')) {
            $cuentaContableId = $request->cuentaContable_id;

            // Buscar todas las cuentas, subcuentas, y subcuentas hijas que comiencen con el número seleccionado
            $subCuentasNumeros = $this->getAllSubCuentasNumeros($cuentaContableId);

            // Aplicar filtro en base a las cuentas encontradas
            $query->where(function ($query) use ($subCuentasNumeros) {
                $query->whereHas('proveedor.cuentaContable', function ($query) use ($subCuentasNumeros) {
                    $query->whereIn('numero', $subCuentasNumeros);
                })->orWhereHas('facturas.cliente.cuentaContable', function ($query) use ($subCuentasNumeros) {
                    $query->whereIn('numero', $subCuentasNumeros);
                })
                    // Nueva condición para filtrar cuando gasto_id no sea null y buscar en gasto.proveedor.cuentaContable
                    ->orWhereHas('gasto.proveedor.cuentaContable', function ($query) use ($subCuentasNumeros) {
                        $query->whereIn('numero', $subCuentasNumeros);
                    });
            });
        }

        // Aplicar filtro de fechas
        if ($request->filled('fecha_desde')) {
            $query->where('fecha', '>=', $request->fecha_desde);
        }
        if ($request->filled('fecha_hasta')) {
            $query->where('fecha', '<=', $request->fecha_hasta);
        }

        // Calcular el número de página actual
        $currentPage = $request->input('page', 1);

        // Clonar la consulta antes de aplicar la paginación para calcular el saldo acumulado
        $saldoAcumulado = 0;

        if ($currentPage > 1) {
            $primerAsientoEnPagina = $query->clone()
                ->skip(($currentPage - 1) * $perPage)
                ->first()
                ->asientoContable ?? null;
            if ($primerAsientoEnPagina) {
                // Calcular el saldo acumulado antes del asientoContable de la primera transacción visible
                $saldoAcumulado = Caja::where('asientoContable', '<', $primerAsientoEnPagina)
                    ->whereNotNull('asientoContable')
                    ->selectRaw('SUM(CASE WHEN tipo_movimiento = "Ingreso" THEN importe ELSE -total END) as saldo_acumulado')
                    ->value('saldo_acumulado') ?? 0;
            }
        }

        // Obtener las transacciones paginadas (transacciones actuales de la página)
        $cajas = $query->paginate($perPage);


        //sumar todos los ingresos y sumar todos los gatos y ver el beneficio
        // $ingresos = 0;
        // $gastos = 0;
        // $cajitas = Caja::all();
        // foreach ($cajitas as $caja) {
        //     if($caja->tipo_movimiento == 'Ingreso'){
        //         $ingresos += $caja->importe;
        //     }else{
        //         $gastos += $caja->pagado ? $caja->pagado : $caja->total;
        //     }
        // }

        // $beneficio = $ingresos - $gastos;
        // Retornar la vista con los datos filtrados y el saldo acumulado
        return view('contabilidad.index', compact('cajas', 'cuentasContables', 'saldoAcumulado'));
    }

    public function getLibroDiario(Request $request)
    {
        // Obtener el año actual por defecto
        $year = $request->input('year', date('Y'));
        $month = $request->input('month', null);
        $cuentaContableId = $request->input('cuentaContable_id', null);

        // Crear una consulta base
        $query = Caja::whereNotNull('asientoContable')
            ->with(['proveedor', 'facturas.cliente'])
            ->whereYear('fecha', $year);

        // Aplicar filtro de mes si está presente
        if ($month) {
            $query->whereMonth('fecha', $month);
        }

        // Aplicar filtro de cuenta contable si está presente
        if ($cuentaContableId) {
            $query->where(function ($query) use ($cuentaContableId) {
                $query->whereHas('proveedor.cuentaContable', function ($query) use ($cuentaContableId) {
                    $query->where('numero', 'like', $cuentaContableId . '%');
                })->orWhereHas('facturas.cliente.cuentaContable', function ($query) use ($cuentaContableId) {
                    $query->where('numero', 'like', $cuentaContableId . '%');
                });
            });
        }

        // Obtener las transacciones filtradas
        $transacciones = $query->get();

        // Inicializar un array para almacenar los totales por cuenta contable
        $totalesPorCuenta = [];
        $totalDebe = 0;
        $totalHaber = 0;

        // Filtrar y agrupar transacciones para excluir aquellas sin cuenta contable
        foreach ($transacciones as $transaccion) {
            $cuentaContable = null;
            $nombreCuenta = null;

            if ($transaccion->tipo_movimiento == 'Ingreso' && !empty($transaccion->facturas) && !empty($transaccion->facturas[0]->cliente)) {
                $cuentaContable = $transaccion->facturas[0]->cliente->cuenta_contable;
                $nombreCuenta = $transaccion->facturas[0]->cliente->nombre;
            } else if ($transaccion->tipo_movimiento == 'Gasto' && !empty($transaccion->proveedor)) {
                $cuentaContable = $transaccion->proveedor->cuenta_contable;
                $nombreCuenta = $transaccion->proveedor->nombre;
            }

            if ($cuentaContable) {
                if (!isset($totalesPorCuenta[$cuentaContable])) {
                    $totalesPorCuenta[$cuentaContable] = [
                        'nombre' => $nombreCuenta,
                        'Debe' => 0,
                        'Haber' => 0
                    ];
                }

                if ($transaccion->tipo_movimiento == 'Ingreso') {
                    $totalesPorCuenta[$cuentaContable]['Haber'] += $transaccion->importe;
                    $totalHaber += $transaccion->importe;
                } else {
                    $totalesPorCuenta[$cuentaContable]['Debe'] += $transaccion->total;
                    $totalDebe += $transaccion->total;
                }
            }
        }

        // Convertir el array a una colección para paginar
        $totalesPorCuentaCollection = collect($totalesPorCuenta);

        // Paginación manual
        $perPage = 10;
        $currentPage = request()->input('page', 1);
        $paginatedTotalesPorCuenta = new \Illuminate\Pagination\LengthAwarePaginator(
            $totalesPorCuentaCollection->forPage($currentPage, $perPage),
            $totalesPorCuentaCollection->count(),
            $perPage,
            $currentPage,
            ['path' => request()->url(), 'query' => request()->query()]
        );

        // Pasar los totales por cuenta y los totales generales a la vista
        return view('contabilidad.libroDiario', compact('paginatedTotalesPorCuenta', 'year', 'month', 'cuentaContableId', 'totalDebe', 'totalHaber'));
    }



    /**
     * Obtener todos los números de cuentas, subcuentas y subcuentas hijas asociadas a la cuenta seleccionada.
     */
    private function getAllSubCuentasNumeros($numeroCuenta)
    {
        $numerosCuentas = [];


        $grupo = GrupoContable::where('numero', $numeroCuenta)->first();
        //dd($numeroCuenta);

        if ($grupo) {
            $numerosCuentas[] = $grupo->numero; // Añadir la cuenta general

            $subGrupos = SubGrupoContable::where('grupo_id', $grupo->id)->get();


            foreach ($subGrupos as $subGrupo) {
                $numerosCuentas[] = $subGrupo->numero;

                // Buscar subcuentas asociadas
                $cuentas = CuentasContable::where('sub_grupo_id', $subGrupo->id)->get();
                foreach ($cuentas as $cuenta) {
                    $numerosCuentas[] = $cuenta->numero;

                    // Buscar subcuentas asociadas
                    $subCuentas = SubCuentaContable::where('cuenta_id', $cuenta->id)->get();
                    foreach ($subCuentas as $subCuenta) {
                        $numerosCuentas[] = $subCuenta->numero;

                        // Buscar subcuentas hijas asociadas a cada subcuenta
                        $subCuentasHijas = SubCuentaHijo::where('sub_cuenta_id', $subCuenta->id)->get();
                        foreach ($subCuentasHijas as $subCuentaHija) {
                            $numerosCuentas[] = $subCuentaHija->numero;
                        }
                    }
                }
            }
        } else {

            $subgrupo = SubGrupoContable::where('numero', $numeroCuenta)->first();

            if ($subgrupo) {
                $numerosCuentas[] = $subgrupo->numero; // Añadir la cuenta general

                // Buscar subcuentas asociadas
                $cuentas = CuentasContable::where('sub_grupo_id', $subgrupo->id)->get();
                foreach ($cuentas as $cuenta) {
                    $numerosCuentas[] = $cuenta->numero;

                    // Buscar subcuentas asociadas
                    $subCuentas = SubCuentaContable::where('cuenta_id', $cuenta->id)->get();
                    foreach ($subCuentas as $subCuenta) {
                        $numerosCuentas[] = $subCuenta->numero;

                        // Buscar subcuentas hijas asociadas a cada subcuenta
                        $subCuentasHijas = SubCuentaHijo::where('sub_cuenta_id', $subCuenta->id)->get();
                        foreach ($subCuentasHijas as $subCuentaHija) {
                            $numerosCuentas[] = $subCuentaHija->numero;
                        }
                    }
                }
            } else {

                $cuenta = CuentasContable::where('numero', $numeroCuenta)->first();

                if ($cuenta) {
                    $numerosCuentas[] = $cuenta->numero; // Añadir la cuenta general

                    // Buscar subcuentas asociadas
                    $subCuentas = SubCuentaContable::where('cuenta_id', $cuenta->id)->get();
                    foreach ($subCuentas as $subCuenta) {
                        $numerosCuentas[] = $subCuenta->numero;

                        // Buscar subcuentas hijas asociadas a cada subcuenta
                        $subCuentasHijas = SubCuentaHijo::where('sub_cuenta_id', $subCuenta->id)->get();
                        foreach ($subCuentasHijas as $subCuentaHija) {
                            $numerosCuentas[] = $subCuentaHija->numero;
                        }
                    }
                } else {

                    $subCuenta = SubCuentaContable::where('numero', $numeroCuenta)->first();
                    if ($subCuenta) {
                        $numerosCuentas[] = $subCuenta->numero; // Añadir la cuenta general

                        // Buscar subcuentas hijas asociadas
                        $subCuentasHijas = SubCuentaHijo::where('sub_cuenta_id', $subCuenta->id)->get();
                        foreach ($subCuentasHijas as $subCuentaHija) {
                            $numerosCuentas[] = $subCuentaHija->numero;
                        }
                    } else {
                        $subCuentaHija = SubCuentaHijo::where('numero', $numeroCuenta)->first();
                        if ($subCuentaHija) {
                            $numerosCuentas[] = $subCuentaHija->numero;
                        }
                    }
                }
            }
        }

        return $numerosCuentas;
    }

    private function calcularTotalPorCuentas($cajas, $reglasCuentas)
    {
        $total = 0;
        
        foreach ($cajas as $caja) {
            $cuentaContable = null;
            $totalCaja = $caja->total;

            // Verificar si la cuenta contable viene del proveedor o del cliente
            if ($caja->proveedor) {
                $cuentaContable = $caja->proveedor->cuenta_contable;
            } elseif ($caja->facturas->isNotEmpty() && $caja->facturas[0]->cliente) {
                $cuentaContable = $caja->facturas[0]->cliente->cuenta_contable;
            }

            // Aplicar las reglas según la cuenta contable
            if ($cuentaContable) {
                foreach ($reglasCuentas as $prefijo => $operacion) {
                    if (strpos($cuentaContable, $prefijo) === 0) {
                        $total += ($operacion === '+' ? $totalCaja : -$totalCaja);
                        break;
                    }
                }
            }
        }

        return $total;
    }

    public function perdidasYGanancias(Request $request){
        // dd('perdidasYGanancias');

       $year = $request->input('year' , date('Y'));

        $cajasNegocios = Caja::whereNotNull('asientoContable')
            ->whereYear('fecha', $year)  // Filtro por año
            ->where(function($query) {
                $query->whereHas('proveedor', function($q) {
                    $q->where('cuenta_contable', 'LIKE', '70%')
                      ->orWhere('cuenta_contable', 'LIKE', '71%')
                      ->orWhere('cuenta_contable', 'LIKE', '6930%')
                      ->orWhere('cuenta_contable', 'LIKE', '7930%')
                      ->orWhere('cuenta_contable', 'LIKE', '73%')
                      ->orWhere('cuenta_contable', 'LIKE', '600%')
                      ->orWhere('cuenta_contable', 'LIKE', '601%')
                      ->orWhere('cuenta_contable', 'LIKE', '602%')
                      ->orWhere('cuenta_contable', 'LIKE', '606%')
                      ->orWhere('cuenta_contable', 'LIKE', '607%')
                      ->orWhere('cuenta_contable', 'LIKE', '608%')
                      ->orWhere('cuenta_contable', 'LIKE', '609%')
                      ->orWhere('cuenta_contable', 'LIKE', '61%')
                      ->orWhere('cuenta_contable', 'LIKE', '6931%')
                      ->orWhere('cuenta_contable', 'LIKE', '6932%')
                      ->orWhere('cuenta_contable', 'LIKE', '6933%')
                      ->orWhere('cuenta_contable', 'LIKE', '7931%')
                      ->orWhere('cuenta_contable', 'LIKE', '7932%')
                      ->orWhere('cuenta_contable', 'LIKE', '7933%')
                      ->orWhere('cuenta_contable', 'LIKE', '740%')
                      ->orWhere('cuenta_contable', 'LIKE', '747%')
                      ->orWhere('cuenta_contable', 'LIKE', '75%')
                      ->orWhere('cuenta_contable', 'LIKE', '64%')
                      ->orWhere('cuenta_contable', 'LIKE', '62%')
                      ->orWhere('cuenta_contable', 'LIKE', '631%')
                      ->orWhere('cuenta_contable', 'LIKE', '634%')
                      ->orWhere('cuenta_contable', 'LIKE', '636%')
                      ->orWhere('cuenta_contable', 'LIKE', '639%')
                      ->orWhere('cuenta_contable', 'LIKE', '65%')
                      ->orWhere('cuenta_contable', 'LIKE', '694%')
                      ->orWhere('cuenta_contable', 'LIKE', '695%')
                      ->orWhere('cuenta_contable', 'LIKE', '794%')
                      ->orWhere('cuenta_contable', 'LIKE', '7954%')
                      ->orWhere('cuenta_contable', 'LIKE', '68%')
                      ->orWhere('cuenta_contable', 'LIKE', '746%')
                      ->orWhere('cuenta_contable', 'LIKE', '7951%')
                      ->orWhere('cuenta_contable', 'LIKE', '7952%')
                      ->orWhere('cuenta_contable', 'LIKE', '7955%')
                      ->orWhere('cuenta_contable', 'LIKE', '670%')
                      ->orWhere('cuenta_contable', 'LIKE', '671%')
                      ->orWhere('cuenta_contable', 'LIKE', '672%')
                      ->orWhere('cuenta_contable', 'LIKE', '690%')
                      ->orWhere('cuenta_contable', 'LIKE', '691%')
                      ->orWhere('cuenta_contable', 'LIKE', '692%')
                      ->orWhere('cuenta_contable', 'LIKE', '770%')
                      ->orWhere('cuenta_contable', 'LIKE', '771%')
                      ->orWhere('cuenta_contable', 'LIKE', '772%')
                      ->orWhere('cuenta_contable', 'LIKE', '790%')
                      ->orWhere('cuenta_contable', 'LIKE', '791%')
                      ->orWhere('cuenta_contable', 'LIKE', '792%')
                      ->orWhere('cuenta_contable', 'LIKE', '760%')
                      ->orWhere('cuenta_contable', 'LIKE', '761%')
                      ->orWhere('cuenta_contable', 'LIKE', '762%')
                      ->orWhere('cuenta_contable', 'LIKE', '769%')
                      ->orWhere('cuenta_contable', 'LIKE', '660%')
                      ->orWhere('cuenta_contable', 'LIKE', '661%')
                      ->orWhere('cuenta_contable', 'LIKE', '662%')
                      ->orWhere('cuenta_contable', 'LIKE', '664%')
                      ->orWhere('cuenta_contable', 'LIKE', '665%')
                      ->orWhere('cuenta_contable', 'LIKE', '669%')
                      ->orWhere('cuenta_contable', 'LIKE', '663%')
                      ->orWhere('cuenta_contable', 'LIKE', '763%')
                      ->orWhere('cuenta_contable', 'LIKE', '668%')
                      ->orWhere('cuenta_contable', 'LIKE', '768%')
                      ->orWhere('cuenta_contable', 'LIKE', '666%')
                      ->orWhere('cuenta_contable', 'LIKE', '667%')
                      ->orWhere('cuenta_contable', 'LIKE', '673%')
                      ->orWhere('cuenta_contable', 'LIKE', '675%')
                      ->orWhere('cuenta_contable', 'LIKE', '696%')
                      ->orWhere('cuenta_contable', 'LIKE', '697%')
                      ->orWhere('cuenta_contable', 'LIKE', '698%')
                      ->orWhere('cuenta_contable', 'LIKE', '699%')
                      ->orWhere('cuenta_contable', 'LIKE', '766%')
                      ->orWhere('cuenta_contable', 'LIKE', '773%')
                      ->orWhere('cuenta_contable', 'LIKE', '775%')
                      ->orWhere('cuenta_contable', 'LIKE', '796%')
                      ->orWhere('cuenta_contable', 'LIKE', '797%')
                      ->orWhere('cuenta_contable', 'LIKE', '798%')
                      ->orWhere('cuenta_contable', 'LIKE', '799%')
                      ->orWhere('cuenta_contable', 'LIKE', '6300%')
                      ->orWhere('cuenta_contable', 'LIKE', '6301%')
                      ->orWhere('cuenta_contable', 'LIKE', '633%')
                      ->orWhere('cuenta_contable', 'LIKE', '638%');


                })
                ->orWhereHas('facturas.cliente', function($q) {
                    $q->where('cuenta_contable', 'LIKE', '70%')
                      ->orWhere('cuenta_contable', 'LIKE', '71%')
                      ->orWhere('cuenta_contable', 'LIKE', '6930%')
                      ->orWhere('cuenta_contable', 'LIKE', '7930%')
                      ->orWhere('cuenta_contable', 'LIKE', '73%')
                      ->orWhere('cuenta_contable', 'LIKE', '600%')
                      ->orWhere('cuenta_contable', 'LIKE', '601%')
                      ->orWhere('cuenta_contable', 'LIKE', '602%')
                      ->orWhere('cuenta_contable', 'LIKE', '606%')
                      ->orWhere('cuenta_contable', 'LIKE', '607%')
                      ->orWhere('cuenta_contable', 'LIKE', '608%')
                      ->orWhere('cuenta_contable', 'LIKE', '609%')
                      ->orWhere('cuenta_contable', 'LIKE', '61%')
                      ->orWhere('cuenta_contable', 'LIKE', '6931%')
                      ->orWhere('cuenta_contable', 'LIKE', '6932%')
                      ->orWhere('cuenta_contable', 'LIKE', '6933%')
                      ->orWhere('cuenta_contable', 'LIKE', '7931%')
                      ->orWhere('cuenta_contable', 'LIKE', '7932%')
                      ->orWhere('cuenta_contable', 'LIKE', '7933%')
                      ->orWhere('cuenta_contable', 'LIKE', '740%')
                      ->orWhere('cuenta_contable', 'LIKE', '747%')
                      ->orWhere('cuenta_contable', 'LIKE', '75%')
                      ->orWhere('cuenta_contable', 'LIKE', '64%')
                      ->orWhere('cuenta_contable', 'LIKE', '62%')
                      ->orWhere('cuenta_contable', 'LIKE', '631%')
                      ->orWhere('cuenta_contable', 'LIKE', '634%')
                      ->orWhere('cuenta_contable', 'LIKE', '636%')
                      ->orWhere('cuenta_contable', 'LIKE', '639%')
                      ->orWhere('cuenta_contable', 'LIKE', '65%')
                      ->orWhere('cuenta_contable', 'LIKE', '694%')
                      ->orWhere('cuenta_contable', 'LIKE', '695%')
                      ->orWhere('cuenta_contable', 'LIKE', '794%')
                      ->orWhere('cuenta_contable', 'LIKE', '7954%')
                      ->orWhere('cuenta_contable', 'LIKE', '68%')
                      ->orWhere('cuenta_contable', 'LIKE', '746%')
                      ->orWhere('cuenta_contable', 'LIKE', '7951%')
                      ->orWhere('cuenta_contable', 'LIKE', '7952%')
                      ->orWhere('cuenta_contable', 'LIKE', '7955%')
                      ->orWhere('cuenta_contable', 'LIKE', '670%')
                      ->orWhere('cuenta_contable', 'LIKE', '671%')
                      ->orWhere('cuenta_contable', 'LIKE', '672%')
                      ->orWhere('cuenta_contable', 'LIKE', '690%')
                      ->orWhere('cuenta_contable', 'LIKE', '691%')
                      ->orWhere('cuenta_contable', 'LIKE', '692%')
                      ->orWhere('cuenta_contable', 'LIKE', '770%')
                      ->orWhere('cuenta_contable', 'LIKE', '771%')
                      ->orWhere('cuenta_contable', 'LIKE', '772%')
                      ->orWhere('cuenta_contable', 'LIKE', '790%')
                      ->orWhere('cuenta_contable', 'LIKE', '791%')
                      ->orWhere('cuenta_contable', 'LIKE', '792%')
                      ->orWhere('cuenta_contable', 'LIKE', '760%')
                      ->orWhere('cuenta_contable', 'LIKE', '761%')
                      ->orWhere('cuenta_contable', 'LIKE', '762%')
                      ->orWhere('cuenta_contable', 'LIKE', '769%')
                      ->orWhere('cuenta_contable', 'LIKE', '660%')
                      ->orWhere('cuenta_contable', 'LIKE', '661%')
                      ->orWhere('cuenta_contable', 'LIKE', '662%')
                      ->orWhere('cuenta_contable', 'LIKE', '664%')
                      ->orWhere('cuenta_contable', 'LIKE', '665%')
                      ->orWhere('cuenta_contable', 'LIKE', '669%')
                      ->orWhere('cuenta_contable', 'LIKE', '663%')
                      ->orWhere('cuenta_contable', 'LIKE', '763%')
                      ->orWhere('cuenta_contable', 'LIKE', '668%')
                      ->orWhere('cuenta_contable', 'LIKE', '768%')
                      ->orWhere('cuenta_contable', 'LIKE', '666%')
                      ->orWhere('cuenta_contable', 'LIKE', '667%')
                      ->orWhere('cuenta_contable', 'LIKE', '673%')
                      ->orWhere('cuenta_contable', 'LIKE', '675%')
                      ->orWhere('cuenta_contable', 'LIKE', '696%')
                      ->orWhere('cuenta_contable', 'LIKE', '697%')
                      ->orWhere('cuenta_contable', 'LIKE', '698%')
                      ->orWhere('cuenta_contable', 'LIKE', '699%')
                      ->orWhere('cuenta_contable', 'LIKE', '766%')
                      ->orWhere('cuenta_contable', 'LIKE', '773%')
                      ->orWhere('cuenta_contable', 'LIKE', '775%')
                      ->orWhere('cuenta_contable', 'LIKE', '796%')
                      ->orWhere('cuenta_contable', 'LIKE', '797%')
                      ->orWhere('cuenta_contable', 'LIKE', '798%')
                      ->orWhere('cuenta_contable', 'LIKE', '799%')
                      ->orWhere('cuenta_contable', 'LIKE', '6300%')
                      ->orWhere('cuenta_contable', 'LIKE', '6301%')
                      ->orWhere('cuenta_contable', 'LIKE', '633%')
                      ->orWhere('cuenta_contable', 'LIKE', '638%');



                });
            })
            ->with(['proveedor', 'facturas.cliente'])
            ->get();

        // Reglas para negocios
        $reglasNegocios = [
            '700' => '+', '701' => '+', '702' => '+', '703' => '+', '704' => '+', '705' => '+',
            '706' => '-', '707' => '-', '708' => '-', '709' => '-'
        ];

        // Reglas para variación de productos
        $reglasVariacionProductos = [
            '6930' => '-',
            '71' => '+',
            '7930' => '+'
        ];
        $reglasTrabajosRealizados = [
            '73' => '+'
        ];

        $reglasAprovisionamientos = [
            '600' => '-',
            '601' => '-',
            '602' => '-',
            '606' => '+',
            '607' => '-',
            '608' => '+',
            '609' => '+',
            '61' => '+',
            '6931' => '-',
            '6932' => '-',
            '6933' => '-',
            '7931' => '+',
            '7932' => '+',
            '7933' => '+'
        ];

        $reglasIngresosExplotacion = [
            '740' => '+',
            '747' => '+',
            '75' => '+'
        ];

        $reglasGastoPersonal = [
            '64' => '-'
        ];


        $reglasOtrosGastosExplotacion = [
            '62' => '-',
            '631' => '-',
            '634' => '-',
            '636' => '+',
            '639' => '+',
            '65' => '-',
            '694' => '-',
            '695' => '-',
            '794' => '+',
            '7954' => '+'

        ];

        $reglasInmovilizado = [
            '68' => '-'
        ];

        $reglasSubvencionesNoFinancieras = [
            '746' => '+'
        ];
        $reglasExcesodeProvisiones = [
            '7951' => '+',
            '7952' => '+',
            '7955' => '+'
        ];

        $reglasDeterioroInmovilizado = [
            '670' => '-',
            '671' => '-',
            '672' => '-',
            '690' => '-',
            '691' => '-',
            '692' => '-',
            '770' => '+',
            '771' => '+',
            '772' => '+',
            '790' => '+',
            '791' => '+',
            '792' => '+'
        ];


        $reglasIngresosFinancieros = [
            '760' => '+',
            '761' => '+',
            '762' => '+',
            '769' => '+'
        ];



        $reglasGastosFinancieros = [
            '660' => '-',
            '661' => '-',
            '662' => '-',
            '664' => '-',
            '665' => '-',
            '669' => '-'
        ];


        $reglasVariacionInstrumentosfinancieros = [
            '663' => '-',
            '763' => '+'
        ];


        $reglasDiferenciasCambio = [
            '668' => '-',
            '768' => '+'
        ];

        $reglasDeterioroEnajenaciones = [
            '666' => '-',
            '667' => '-',
            '673' => '-',
            '675' => '-',
            '696' => '-',
            '697' => '-',
            '698' => '-',
            '699' => '-',
            '766' => '+',
            '773' => '+',
            '775' => '+',
            '796' => '+',
            '797' => '+',
            '798' => '+',
            '799' => '+'
        ];


        $reglasImpuestosBeneficio =[
            '6300' => '-',
            '6301' => '-',
            '633' => '-',
            '638' => '+'
        ];



        $totalNegocios = $this->calcularTotalPorCuentas($cajasNegocios, $reglasNegocios);

        $variacionProductos = $this->calcularTotalPorCuentas($cajasNegocios, $reglasVariacionProductos);
        $trabajosRealizados = $this->calcularTotalPorCuentas($cajasNegocios, $reglasTrabajosRealizados);
        $aprovisionamientos = $this->calcularTotalPorCuentas($cajasNegocios, $reglasAprovisionamientos);
        $ingresosExplotacion = $this->calcularTotalPorCuentas($cajasNegocios, $reglasIngresosExplotacion);
        $gastoPersonal = $this->calcularTotalPorCuentas($cajasNegocios, $reglasGastoPersonal);
        $otrosGastosExplotacion = $this->calcularTotalPorCuentas($cajasNegocios, $reglasOtrosGastosExplotacion);
        $inmovilizado = $this->calcularTotalPorCuentas($cajasNegocios, $reglasInmovilizado);
        $subvencionesNoFinancieras = $this->calcularTotalPorCuentas($cajasNegocios, $reglasSubvencionesNoFinancieras);
        $excesodeProvisiones = $this->calcularTotalPorCuentas($cajasNegocios, $reglasExcesodeProvisiones);
        $deterioroInmovilizado = $this->calcularTotalPorCuentas($cajasNegocios, $reglasDeterioroInmovilizado);



        $totalPrimero = $totalNegocios + $variacionProductos + $trabajosRealizados + $aprovisionamientos + $ingresosExplotacion + $gastoPersonal + $otrosGastosExplotacion + $inmovilizado + $subvencionesNoFinancieras + $excesodeProvisiones + $deterioroInmovilizado;



        $ingresosFinancieros = $this->calcularTotalPorCuentas($cajasNegocios, $reglasIngresosFinancieros);
        $gastosFinancieros = $this->calcularTotalPorCuentas($cajasNegocios, $reglasGastosFinancieros);
        $variacionInstrumentosFinancieros = $this->calcularTotalPorCuentas($cajasNegocios, $reglasVariacionInstrumentosfinancieros);
        $diferenciasCambio = $this->calcularTotalPorCuentas($cajasNegocios, $reglasDiferenciasCambio);
        $deterioroEnajenaciones = $this->calcularTotalPorCuentas($cajasNegocios, $reglasDeterioroEnajenaciones);

        $totalResultadosFinancieros = $ingresosFinancieros + $gastosFinancieros + $variacionInstrumentosFinancieros + $diferenciasCambio + $deterioroEnajenaciones;



        $impuestosBeneficio = $this->calcularTotalPorCuentas($cajasNegocios, $reglasImpuestosBeneficio);

        $ResultadoEjercicio = $totalPrimero + $totalResultadosFinancieros + $impuestosBeneficio;
        return view('contabilidad.pedidasganancias', compact('totalNegocios', 'variacionProductos', 'trabajosRealizados', 'aprovisionamientos', 'ingresosExplotacion', 'gastoPersonal', 'otrosGastosExplotacion', 'inmovilizado', 'subvencionesNoFinancieras', 'excesodeProvisiones', 'deterioroInmovilizado', 'totalPrimero', 'ingresosFinancieros' , 'gastosFinancieros', 'variacionInstrumentosFinancieros', 'diferenciasCambio', 'deterioroEnajenaciones', 'totalResultadosFinancieros', 'ResultadoEjercicio' , 'impuestosBeneficio' , 'year'));   





    }



    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('contabilidad.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }




    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        return view('contabilidad.edit', compact('id'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
