<?php

namespace App\Http\Livewire\Usuarios;

use App\Models\Rol;
use App\Models\Almacen;
use App\Models\User;
use Livewire\Component;

class IndexComponent extends Component
{
    // public $search;
    public $usuarios;
    public $almacenes;

    public function mount()
    {
        $this->almacenes = Almacen::all();
        // Carga ansiosa de la relación con Almacen
        $this->usuarios = User::with('almacen')->get();
    }


    public function render()
    {

        return view('livewire.usuarios.index-component');
    }

    public function mostrarRol($id){
        return Rol::where('id', $id)->first()->nombre;
    }

}
