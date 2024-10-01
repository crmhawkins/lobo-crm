<?php

namespace App\Http\Controllers;

use App\Models\GrupoContable;
use App\Models\SubGrupoContable;
use Illuminate\Http\Request;

class SubGrupoContabilidadController extends Controller
{
    /**
     * Mostrar la lista de Sub Grupos Contables
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $search = $request->get('search');
        $sort = $request->get('sort', 'id');
        $order = $request->get('order', 'asc');
        $grupo = $request->get('grupo');
        $perPage = $request->get('perPage', 10);
    
        $query = SubGrupoContable::with('grupo');
    
        if ($search) {
            $query->where('nombre', 'like', '%' . $search . '%')
                  ->orWhere('numero', 'like', '%' . $search . '%')
                  ->orWhere('descripcion', 'like', '%' . $search . '%')
                  ->orWhereHas('grupo', function ($q) use ($search) {
                      $q->where('nombre', 'like', '%' . $search . '%');
                  });
        }
    
        if ($grupo) {
            $query->where('grupo_id', $grupo);
        }
    
        $response = $query->orderBy($sort, $order)->paginate($perPage);
        $grupos = GrupoContable::all(); // Cambia el nombre aquí
    
        return view('admin.contabilidad.subGrupoContabilidad.index', compact('response', 'grupos')); // Asegúrate de pasar la variable 'grupos'
    }
    

    /**
     *  Mostrar el formulario de creación
     *
     * @return \Illuminate\Http\Response
     */    
    public function create()
    {            
        $grupos = GrupoContable::all();
        return view('admin.contabilidad.subGrupoContabilidad.create', compact('grupos'));
    }

    /**
     * Guardar un nuevo SubGrupoContable
     *
     * @param  Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $rules = [
            'numero' => 'required',
            'nombre' => 'required',
            'descripcion' => 'required',
            'grupo_id' => 'required|exists:grupo_contable,id'
        ];

        $validatedData = $request->validate($rules);
        SubGrupoContable::create($validatedData);

        return redirect()->route('admin.subGrupoContabilidad.index')->with('status', 'El SubGrupo fue creado con éxito!');
    }

    /**
     * Mostrar el formulario de edición
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $subGrupo = SubGrupoContable::findOrFail($id);
        $grupos = GrupoContable::all();

        return view('admin.contabilidad.subGrupoContabilidad.edit', compact('subGrupo', 'grupos'));
    }

    /**
     * Actualizar un SubGrupoContable existente
     *
     * @param  Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $rules = [
            'numero' => 'required',
            'nombre' => 'required',
            'descripcion' => 'required',
            'grupo_id' => 'required|exists:grupo_contable,id'
        ];

        $validatedData = $request->validate($rules);
        $subGrupo = SubGrupoContable::findOrFail($id);
        $subGrupo->update($validatedData);

        return redirect()->route('admin.subGrupoContabilidad.index')->with('status', 'El SubGrupo fue actualizado con éxito!');
    }

    /**
     * Borrar un SubGrupoContable
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $subGrupo = SubGrupoContable::findOrFail($id);
        $subGrupo->delete();

        return redirect()->route('admin.subGrupoContabilidad.index')->with('status', 'SubGrupo Borrado.');
    }
}
