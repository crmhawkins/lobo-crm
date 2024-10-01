<?php

namespace App\Http\Livewire\Proveedores;

use App\Models\Proveedores;
use Livewire\Component;
use App\Models\SubCuentaHijo;
use App\Models\SubCuentaContable;

class IndexComponent extends Component
{
    // public $search;
    public $proveedores;

    public function mount()
    {
        $this->proveedores = Proveedores::all();
    }

    public function crearCuentasContables4100(){
        $clientes = Proveedores::where('cuenta_contable', 'LIKE', '4100%')
        ->orderBy('cuenta_contable', 'asc')
        ->get();
        foreach ($clientes as $cliente) {
            $subcuenta = SubCuentaContable::where('numero', 4100)->first();
            //dd($subcuenta);

            if($subcuenta != null){
                $subcuenta = SubCuentaHijo::create([
                    'sub_cuenta_id' => $subcuenta->id,
                    'numero' => $cliente->cuenta_contable,
                    'nombre' => $cliente->nombre,
                    'descripcion' => 'Proveedor',
                ]);
            }

           
        }
        
        //dd($clientes);

    }

    public function crearCuentasContables6250(){
        $clientes = Proveedores::where('cuenta_contable', 'LIKE', '6250%')
        ->orderBy('cuenta_contable', 'asc')
        ->get();
        foreach ($clientes as $cliente) {
            $subcuenta = SubCuentaContable::where('numero', 6250)->first();
            //dd($subcuenta);

            if($subcuenta != null){
                $subcuenta = SubCuentaHijo::create([
                    'sub_cuenta_id' => $subcuenta->id,
                    'numero' => $cliente->cuenta_contable,
                    'nombre' => $cliente->nombre,
                    'descripcion' => 'Proveedor',
                ]);
            }

           
        }
    }

    public function crearCuentasContables6210(){
        $clientes = Proveedores::where('cuenta_contable', 'LIKE', '6210%')
        ->orderBy('cuenta_contable', 'asc')
        ->get();
        foreach ($clientes as $cliente) {
            $subcuenta = SubCuentaContable::where('numero', 6210)->first();
            //dd($subcuenta);

            if($subcuenta != null){
                $subcuenta = SubCuentaHijo::create([
                    'sub_cuenta_id' => $subcuenta->id,
                    'numero' => $cliente->cuenta_contable,
                    'nombre' => $cliente->nombre,
                    'descripcion' => 'Proveedor',
                ]);
            }

           
        }
    }

    public function crearCuentasContables6212(){
        $clientes = Proveedores::where('cuenta_contable', 'LIKE', '6212%')
        ->orderBy('cuenta_contable', 'asc')
        ->get();
        foreach ($clientes as $cliente) {
            $subcuenta = SubCuentaContable::where('numero', 6212)->first();
            //dd($subcuenta);

            if($subcuenta != null){
                $subcuenta = SubCuentaHijo::create([
                    'sub_cuenta_id' => $subcuenta->id,
                    'numero' => $cliente->cuenta_contable,
                    'nombre' => $cliente->nombre,
                    'descripcion' => 'Proveedor',
                ]);
            }

           
        }
    }

    public function crearCuentasContables6293(){
        $clientes = Proveedores::where('cuenta_contable', 'LIKE', '6293%')
        ->orderBy('cuenta_contable', 'asc')
        ->get();
        foreach ($clientes as $cliente) {
            $subcuenta = SubCuentaContable::where('numero', 6293)->first();
            //dd($subcuenta);

            if($subcuenta != null){
                $subcuenta = SubCuentaHijo::create([
                    'sub_cuenta_id' => $subcuenta->id,
                    'numero' => $cliente->cuenta_contable,
                    'nombre' => $cliente->nombre,
                    'descripcion' => 'Proveedor',
                ]);
            }

           
        }
    }

    public function crearCuentasContables($numero){
        $clientes = Proveedores::where('cuenta_contable', 'LIKE', $numero.'%')
        ->orderBy('cuenta_contable', 'asc')
        ->get();
        foreach ($clientes as $cliente) {
            $subcuenta = SubCuentaContable::where('numero', $numero)->first();
            //dd($subcuenta);

            if($subcuenta != null){
                $subcuenta = SubCuentaHijo::create([
                    'sub_cuenta_id' => $subcuenta->id,
                    'numero' => $cliente->cuenta_contable,
                    'nombre' => $cliente->nombre,
                    'descripcion' => 'Proveedor',
                ]);
            }

           
        }
    }

    public function render()
    {

        return view('livewire.proveedores.index-component');
    }

}
