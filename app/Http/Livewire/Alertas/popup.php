<?php

namespace App\Http\Livewire\Alertas;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use App\Models\Alertas;
use App\Models\User;

class popup extends Component
{
    public $alertas = [];

    public function mount()
    {
        if(Auth::user()->role == 'admin') {
            $this->alertas = Alertas::orderBy('id', 'desc')->where('popup', true)->get();
        } else {
            $this->alertas = Alertas::orderBy('id', 'desc')->where('popup', true)->where('user_id', Auth::user()->id)->get();
        }

    }

    public function getNombreUsuario($id)
    {
        $user = User::find($id);
        return $user->name . ' ' . $user->apellidos;
    }


   public function render()
   {
    return view('livewire.alertas.popup');
   }
}
