<?php

namespace App\Http\Controllers;

use App\Models\CuentasContable;
use App\Models\SubCuentaContable;
use App\Models\SubCuentaHijo;
use Illuminate\Http\Request;
use Validator;

class SubCuentasHijoController extends Controller
{
    // Mostrar la lista de Sub Cuentas Hijas Contables
    public function index(Request $request)
    {
        $search = $request->get('search');
        $sort = $request->get('sort', 'id');
        $order = $request->get('order', 'asc');
        $subGrupo = $request->get('subGrupo');
        $perPage = $request->get('perPage', 10);

        $query = SubCuentaHijo::with('cuenta');

        if ($search) {
            $query->where('nombre', 'like', '%' . $search . '%')
                  ->orWhere('numero', 'like', '%' . $search . '%')
                  ->orWhere('descripcion', 'like', '%' . $search . '%')
                  ->orWhereHas('cuenta', function ($q) use ($search) {
                      $q->where('nombre', 'like', '%' . $search . '%');
                  });
        }

        if ($subGrupo) {
            $query->where('sub_cuenta_id', $subGrupo);
        }

        $response = $query->orderBy($sort, $order)->paginate($perPage);

        $subCuentas = SubCuentaContable::all();
        return view('admin.contabilidad.subCuentaHijo.index', compact('response', 'subCuentas'));
    }

    // Mostrar el formulario de creación
    public function create()
{
    $subCuentas = SubCuentaContable::all(); // Obtener todas las sub-cuentas contables
    return view('admin.contabilidad.subCuentaHijo.create', compact('subCuentas'));
}

    // Guardar una nueva sub cuenta hija
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'sub_cuenta_id' => 'required',
            'numero' => 'required|unique:sub_cuenta_hija',
            'nombre' => 'required',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        SubCuentaHijo::create($request->all());

        return redirect()->route('admin.subCuentasHijaContables.index')->with('success', 'Sub cuenta hija creada con éxito.');
    }

    // Mostrar el formulario de edición
    public function edit($id)
    {
        $subCuentaHijo = SubCuentaHijo::find($id);
        $subCuentas = SubCuentaContable::all();

        if (!$subCuentaHijo) {
            return redirect()->route('admin.subCuentasHijaContables.index')->with('error', 'Sub cuenta hija no encontrada.');
        }

        return view('admin.contabilidad.subCuentaHijo.edit', compact('subCuentaHijo', 'subCuentas'));
    }

    // Actualizar la sub cuenta hija
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'sub_cuenta_id' => 'required',
            'numero' => 'required',
            'nombre' => 'required',
            'descripcion' => 'required',
        ]);
    
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }
    
        $subCuentaHijo = SubCuentaHijo::find($id);
    
        if ($subCuentaHijo) {
            $subCuentaHijo->sub_cuenta_id = $request->input('sub_cuenta_id');
            $subCuentaHijo->numero = $request->input('numero');
            $subCuentaHijo->nombre = $request->input('nombre');
            $subCuentaHijo->descripcion = $request->input('descripcion');
            $subCuentaHijo->save();
    
            return redirect()->route('admin.subCuentasHijaContables.index')->with('success', 'Sub cuenta hija actualizada correctamente.');
        } else {
            return redirect()->route('admin.subCuentasHijaContables.index')->with('error', 'Sub cuenta hija no encontrada.');
        }
    }
    
    // Borrar sub cuenta hija
    public function destroy($id)
    {
        $subCuentaHijo = SubCuentaHijo::find($id);

        if (!$subCuentaHijo) {
            return redirect()->back()->with('error', 'Sub cuenta hija no encontrada.');
        }

        $subCuentaHijo->delete();

        return redirect()->route('admin.subCuentasHijaContables.index')->with('success', 'Sub cuenta hija eliminada con éxito.');
    }
}
