<?php

namespace App\Http\Controllers;

use App\Models\CuentasContable;
use App\Models\GrupoContable;
use App\Models\SubGrupoContable;
use Illuminate\Http\Request;
use Validator;

class CuentasContableController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->get('search');
        $sort = $request->get('sort', 'id');
        $order = $request->get('order', 'asc');
        $subGrupo = $request->get('subGrupo');
        $perPage = $request->get('perPage', 10);

        $query = CuentasContable::with('grupo');

        if ($search) {
            $query->where('nombre', 'like', '%' . $search . '%')
                  ->orWhere('numero', 'like', '%' . $search . '%')
                  ->orWhere('descripcion', 'like', '%' . $search . '%')
                  ->orWhereHas('grupo', function ($q) use ($search) {
                      $q->where('nombre', 'like', '%' . $search . '%');
                  });
        }

        if ($subGrupo) {
            $query->where('sub_grupo_id', $subGrupo);
        }

        $response = $query->orderBy($sort, $order)->paginate($perPage);

        $subgrupos = SubGrupoContable::all();
        return view('admin.contabilidad.cuentaContabilidad.index', compact('response', 'subgrupos'));
    }

    public function create()
    {
        $grupos = GrupoContable::all();
        $subgrupos = SubGrupoContable::all();

        return view('admin.contabilidad.cuentaContabilidad.create', compact('subgrupos', 'grupos'));
    }

    public function store(Request $request)
    {
        // Validamos los datos recibidos desde el formulario
        $validator = Validator::make($request->all(), [
            'sub_grupo_id' => 'required',
            'numero' => 'required|unique:sub_grupo_contable',
            'nombre' => 'required',
        ]);

        // Si la validación pasa, creamos la cuenta contable
        if ($validator->passes()) {
            CuentasContable::create($request->all());
            
            // Redirigir a la lista de cuentas contables con mensaje de éxito
            return redirect()->route('admin.cuentasContables.index')->with('success', 'Cuenta Creada.');
        }

        // Si la validación falla, redirigir de vuelta con los errores
        return redirect()->back()->withErrors($validator)->withInput();
    }

    public function edit($id)
    {
        $cuenta = CuentasContable::find($id);
        $grupo = SubGrupoContable::all();

        return view('admin.contabilidad.cuentaContabilidad.edit', compact('cuenta', 'grupo'));
    }

    public function updated(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'sub_grupo_id' => 'required',
            'numero' => 'required',
            'nombre' => 'required',
        ]);

        //dd($validator->errors());
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $cuenta = CuentasContable::find($id);

        if ($cuenta) {
            $cuenta->sub_grupo_id = $request->input('sub_grupo_id');
            $cuenta->numero = $request->input('numero');
            $cuenta->nombre = $request->input('nombre');
            $cuenta->descripcion = $request->input('descripcion');
            $cuenta->save();

            return redirect()->route('admin.cuentasContables.index')->with('status', 'Cuenta actualizada con éxito.');
        } else {
            return redirect()->route('admin.cuentasContables.index')->with('error', 'Cuenta no encontrada.');
        }
    }

    

    public function destroy($id)
    {
        $cuenta = CuentasContable::find($id);

        if (!$cuenta) {
            return redirect()->back()->with('error', 'El id: ' . $id . ' no existe.');
        }

        $cuenta->delete();

        return redirect()->route('admin.cuentasContables.index')->with('success', 'Cuenta Borrada.');
    }
}
