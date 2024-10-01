<?php

namespace App\Http\Controllers;

use App\Models\GrupoContable;
use Illuminate\Http\Request;

class GrupoContabilidadController extends Controller
{
    /**
     * Mostrar la lista de grupos contables.
     */
    public function index(Request $request)
    {
        $search = $request->get('search');
        $sort = $request->get('sort', 'numero');
        $order = $request->get('order', 'asc');
        $perPage = $request->get('perPage', 10);

        $query = GrupoContable::query();

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('nombre', 'like', '%' . $search . '%')
                  ->orWhere('numero', 'like', '%' . $search . '%')
                  ->orWhere('descripcion', 'like', '%' . $search . '%');
            });
        }

        $response = $query->orderBy($sort, $order)->paginate($perPage);

        return view('admin.contabilidad.grupoContabilidad.index', compact('response'));
    }

    /**
     * Mostrar el formulario de creación.
     */
    public function create()
    {
        return view('admin.contabilidad.grupoContabilidad.create');
    }

    /**
     * Almacenar un nuevo grupo contable.
     */
    public function store(Request $request)
    {
        $request->validate([
            'numero' => 'required|unique:grupo_contable,numero',
            'nombre' => 'required',
            'descripcion' => 'required',
        ]);

        GrupoContable::create($request->all());

        return redirect()->route('admin.grupoContabilidad.index')->with('status', 'El grupo fue creado con éxito!');
    }

    /**
     * Mostrar el formulario de edición.
     */
    public function edit($id)
    {
        $grupoContable = GrupoContable::findOrFail($id);
        return view('admin.contabilidad.grupoContabilidad.edit', compact('grupoContable'));
    }

    /**
     * Actualizar un grupo contable existente.
     */
    public function update(Request $request, $id)
    {
        $grupo = GrupoContable::findOrFail($id);

        $request->validate([
            'numero' => 'required|unique:grupo_contable,numero,'.$grupo->id,
            'nombre' => 'required',
            'descripcion' => 'required',
        ]);

        $grupo->update($request->all());

        return redirect()->route('admin.grupoContabilidad.index')->with('status', 'El grupo fue actualizado con éxito!');
    }

    /**
     * Eliminar un grupo contable.
     */
    public function destroy($id)
    {
        $grupo = GrupoContable::findOrFail($id);
        $grupo->delete();

        return redirect()->route('admin.grupoContabilidad.index')->with('status', 'El grupo fue eliminado con éxito.');
    }
}
