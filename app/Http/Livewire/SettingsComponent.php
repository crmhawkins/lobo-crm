<?php

namespace App\Http\Livewire;

use Illuminate\Support\Facades\Route;
use Livewire\Component;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use App\Models\Settings;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

class SettingsComponent extends Component
{
    use LivewireAlert;
    public $setting;

    public $precio_gasoil_km;
    public $saldo_inicial;
    public $file;

    public function mount()
    {
        $this->setting = Settings::where('id', 1)->first();

        $this->precio_gasoil_km = Settings::where('id', 1)->first()->precio_gasoil_km;
        $this->saldo_inicial = Settings::where('id', 1)->first()->saldo_inicial;

    }

    public function render()
    {

        return view('livewire.settings.settings-component');
    }
    protected $listeners = ['submit', 'confirmed'];

    public function submit()
    {
        // Guardar datos validados
        $presupuesosSave = $this->setting->update(['precio_gasoil_km' => $this->precio_gasoil_km]);

        event(new \App\Events\LogEvent(Auth::user(), 58, null));

        // Alertas de guardado exitoso
        if ($presupuesosSave) {
            $this->alert('success', '¡Opciones del CRM actualizadas correctamente!', [
                'position' => 'center',
                'timer' => null,
                'toast' => false,
                'showConfirmButton' => true,
                'onConfirmed' => 'confirmed',
                'confirmButtonText' => 'ok',
                'timerProgressBar' => true,
            ]);
        } else {
            $this->alert('error', '¡No se ha podido actualizar las opciones del CRM!', [
                'position' => 'center',
                'timer' => 3000,
                'toast' => false,
            ]);
        }
    }
    public function confirmed()
    {

        return redirect('home');
    }
}
