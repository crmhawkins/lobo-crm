<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Caja;

class GastosController extends Controller
{
    public function buscarGastos(Request $request)
    {
        if ($request->has('id')) {
            $gasto = Caja::find($request->id);
            return response()->json($gasto);
        }
        $search = $request->get('search', '');
        $page = $request->get('page', 1);

        $gastos = Caja::where('tipo_movimiento', 'Gasto')->where('nFactura', 'like', '%'.$search.'%')
        ->paginate(10, ['*'], 'page', $page); // 10 resultados por página
        
        return response()->json([
            'data' => $gastos->items(),
            'more' => $gastos->hasMorePages(),
        ]);
    }
}
