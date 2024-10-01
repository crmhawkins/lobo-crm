<?php

namespace App\Http\Controllers;

use App\Models\CuentasContable;
use App\Models\SubCuentaContable;
use App\Models\GrupoContable;
use App\Models\SubGrupoContable;
use Illuminate\Http\Request;

use Validator;

class SubCuentasContableController extends Controller
{


    /**
     * Mostrar la lista de contactos
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
{
    $search = $request->get('search');
    $sort = $request->get('sort', 'id');
    $order = $request->get('order', 'asc');
    $subGrupo = $request->get('subGrupo');
    $perPage = $request->get('perPage', 10);

    // Incluir la relación con la cuenta contable
    $query = SubCuentaContable::with('cuenta');

    if ($search) {
        // Usamos la relación cuenta y los campos de SubCuentaContable para la búsqueda
        $query->where('nombre', 'like', '%' . $search . '%')
              ->orWhere('numero', 'like', '%' . $search . '%')
              ->orWhere('descripcion', 'like', '%' . $search . '%')
              ->orWhereHas('cuenta', function ($q) use ($search) {
                  $q->where('nombre', 'like', '%' . $search . '%');
              });
    }

    if ($subGrupo) {
        $query->where('cuenta_id', $subGrupo); // Aquí verificamos la relación directa con cuenta_id
    }

    $response = $query->orderBy($sort, $order)->paginate($perPage);

    $subCuentas = CuentasContable::orderBy('numero', 'asc')->get();
    return view('admin.contabilidad.subCuentaContabilidad.index', compact('response', 'subCuentas'));
}

    public function getCuentasByDataTables(){

        $CuentasContables = CuentasContable::select('sub_grupo_id', 'numero', 'nombre', 'descripcion','id');

        return Datatables::of($CuentasContables)
                ->addColumn('subGrupo', function ($CuentasContable) {
                    if($CuentasContable->sub_grupo_id){
                        $subgrupo = SubGrupoContable::where('id', $CuentasContable->sub_grupo_id )->first();
                        return strval($subgrupo->numero .' - '.$subgrupo->nombre);
                    }
                    else{
                        return "no ";
                    }
                })


               
                ->addColumn('action', function ($CuentasContable) {
                    return '<a href="/admin/cuentas-contables/'.$CuentasContable->id.'/edit" class="btn btn-xs btn-primary"><i class="fas fa-pencil-alt"></i> Editar</a>';
                }) 
                // ->addColumn('delete', function ($CuentasContable) {
                //     $url = route('admin.cuentasContables.destroy', [ 'id'=> $CuentasContable->id]);
                //     return '<form action="'.$url.'" method="POST" enctype="multipart/form-data" data-callback="formCallback">
                //         <button type="submit" class="btn btn-danger"><i class="fas fa-times"></i></button>  
                //     </form>';
                // })

                ->addColumn('delete', function ($CuentasContable) {
                    return '<button type="button" class="btn btn-danger" onclick="deleteEntry('.$CuentasContable->id.')" ><i class="fas fa-times"></i></button>';
                })


                ->escapeColumns(null)   
                ->make();
    }
    /**
     *  Mostrar el formulario de creación
     *
     * @return \Illuminate\Http\Response
     */    
    public function create()
    {
        $cuentasContables = CuentasContable::orderBy('numero', 'asc')->get(); // Aquí recogemos las cuentas contables
        return view('admin.contabilidad.subCuentaContabilidad.create', compact('cuentasContables'));
    }
    
    public function edit($id)
    {
        $subCuenta = SubCuentaContable::find($id);  // Encontrar la sub cuenta contable a editar
        $cuentasContables = CuentasContable::orderBy('numero', 'asc')->get();  // Traer todas las cuentas contables

    
        // Pasamos las subcuentas y cuentas contables a la vista
        return view('admin.contabilidad.subCuentaContabilidad.edit', compact('subCuenta', 'cuentasContables'));
    }
    

public function store(Request $request)
{   
    $validator = Validator::make($request->all(), [
        'cuenta_id' => 'required',
        'numero' => 'required|unique:sub_cuentas_contable',
        'nombre' => 'required',
    ]);

    if ($validator->passes()) {
        SubCuentaContable::create($request->all());
        return redirect()->route('admin.subCuentasContables.index')->with('success', 'Sub cuenta creada correctamente.');
    }

    return redirect()->back()->withErrors($validator)->withInput();
}

public function destroy($id)
{
    $subCuenta = SubCuentaContable::find($id);

    if (!$subCuenta) {
        return redirect()->back()->with('error', 'La sub cuenta contable con el ID ' . $id . ' no existe.');
    }

    $subCuenta->delete();

    return redirect()->route('admin.subCuentasContables.index')->with('success', 'Sub cuenta contable eliminada correctamente.');
}

public function update(Request $request, $id)
{
    $validator = Validator::make($request->all(), [
        'cuenta_id' => 'required',
        'numero' => 'required',
        'nombre' => 'required',
    ]);

    if ($validator->fails()) {
        return redirect()->back()->withErrors($validator)->withInput();
    }

    $subCuenta = SubCuentaContable::find($id);

    if ($subCuenta) {
        $subCuenta->cuenta_id = $request->input('cuenta_id');
        $subCuenta->numero = $request->input('numero');
        $subCuenta->nombre = $request->input('nombre');
        $subCuenta->descripcion = $request->input('descripcion');
        $subCuenta->save();

        return redirect()->route('admin.subCuentasContables.index')->with('status', 'Sub cuenta actualizada con éxito.');
    } else {
        return redirect()->route('admin.subCuentasContables.index')->with('error', 'Sub cuenta no encontrada.');
    }
}

    
}
