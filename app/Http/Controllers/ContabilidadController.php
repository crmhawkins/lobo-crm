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
    return view('contabilidad.index', compact('cajas', 'cuentasContables', 'saldoAcumulado' ));
}
    


    /**
     * Obtener todos los números de cuentas, subcuentas y subcuentas hijas asociadas a la cuenta seleccionada.
     */
    private function getAllSubCuentasNumeros($numeroCuenta)
    {
        $numerosCuentas = [];


        $grupo = GrupoContable::where('numero', $numeroCuenta)->first();
        //dd($numeroCuenta);

        if($grupo){
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


        }else{

            $subgrupo = SubGrupoContable::where('numero', $numeroCuenta)->first();

            if($subgrupo){
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
            }else{

                $cuenta = CuentasContable::where('numero', $numeroCuenta)->first();

                if($cuenta){
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
                }else{
                    
                    $subCuenta = SubCuentaContable::where('numero', $numeroCuenta)->first();
                    if($subCuenta){
                        $numerosCuentas[] = $subCuenta->numero; // Añadir la cuenta general

                        // Buscar subcuentas hijas asociadas
                        $subCuentasHijas = SubCuentaHijo::where('sub_cuenta_id', $subCuenta->id)->get();
                        foreach ($subCuentasHijas as $subCuentaHija) {
                            $numerosCuentas[] = $subCuentaHija->numero;
                        }
                    }else{
                        $subCuentaHija = SubCuentaHijo::where('numero', $numeroCuenta)->first();
                        if($subCuentaHija){
                            $numerosCuentas[] = $subCuentaHija->numero;
                        }

                    }
                    
                
                }


            }



        }

        return $numerosCuentas;
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
