<?php

namespace App\Http\Livewire\Servicios;

use Illuminate\Support\Facades\Redirect;
use App\Models\Servicio;
use App\Models\Articulos;
use App\Models\ServicioCategoria;
use App\Models\ServicioPack;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;

class CreateComponent extends Component
{

    use LivewireAlert;

    public $nombre;
    public $precioBase;
    public $id_pack;
    public $id_categoria;
    public $minMonitor;
    public $precioMonitor;
    public $servicioCategorias;
    public $servicioPacks;
    public $stock_usado;
    public $articulo_seleccionado;
    public $numero_articulos;
    public $listaArticulos;
    public $articulos;
    public $articulosSelect;
    public $tiempoMontaje;
    public $tiempoDesmontaje;
    public $tiempoServicio;
    public $precioMonitorNocturno;
    public $precioMonitorAnimacion;

    public function mount()
    {
        $this->servicioCategorias = ServicioCategoria::all();
        $this->servicioPacks = ServicioPack::all();
        $this->articulos = Articulos::all();
        $this->articulosSelect = $this->articulos->all();
    }

    public function precioTotal()
    {
        return intval($this->minMonitor) * intval($this->precioMonitor) + floatval($this->precioBase);
    }

    public function crearCategoria()
    {
        return Redirect::to(route("servicios-categorias.create"));
    }

    public function crearPack()
    {
        return Redirect::to(route("servicios-packs.create"));
    }

    public function render()
    {
        return view('livewire.servicios.create-component');
    }

    public function addStock()
    {
        if ($this->articulo_seleccionado != 0) {
            if ($this->stock_usado > Articulos::firstWhere('id', $this->articulo_seleccionado)->stock) {
                $this->alert('error', '¡La selección sobrepasa al stock disponible!', [
                    'position' => 'center',
                    'toast' => false,
                    'showConfirmButton' => true,
                    'confirmButtonText' => 'ok',
                    'timerProgressBar' => true,
                ]);
            } else {
                $this->listaArticulos[] = [
                    'id' => $this->articulo_seleccionado,
                    'stock_usado' => $this->stock_usado,
                    'existente' => 0
                ];
                $this->articulo_seleccionado = 0;
                $this->stock_usado = 0;
            }
        } else {
            $this->alert('error', '¡Selecciona un artículo!', [
                'position' => 'center',
                'toast' => false,
                'showConfirmButton' => true,
                'confirmButtonText' => 'ok',
                'timerProgressBar' => true,
            ]);
        }
    }

    public function deleteStock($indice)
    {
        unset($this->listaArticulos[$indice]);
        $this->listaArticulos = array_values($this->listaArticulos);
    }
    // Al hacer submit en el formulario
    public function submit()
    {

        // Validación de datos
        $validatedData = $this->validate(
            [
                'nombre' => 'required',
                'precioBase' => 'required',
                'id_pack' => 'nullable',
                'id_categoria' => 'required',
                'minMonitor' => 'required',
                'precioMonitor' => 'required',
                'tiempoMontaje' => 'required',
                'tiempoDesmontaje' => 'required',
                'tiempoServicio' => 'required',


            ],
            // Mensajes de error
            [
                'nombre.required' => 'El nombre es obligatorio.',
                'precioBase.required' => 'El precio base son obligatorio.',
                'id_categoria.required' => 'El campo categoria es obligatorio.',
                'minMonitor.required' => 'El numero minimo de monitores es obligatorio.',
                'precioMonitor.required' => 'El precio minimo por monitor es obligatorio.',
                'tiempoMontaje.required' => 'El tiempo de montaje es obligatorio.',
                'tiempoDesmontaje.required' => 'El tiempo de desmontaje es obligatorio.',
                'tiempoServicio.required' => 'La duración del servicio es obligatorio.',

            ]
        );

        // Guardar datos validados
        $usuariosSave = Servicio::create($validatedData);
        event(new \App\Events\LogEvent(Auth::user(), 29, $usuariosSave->id));
        foreach ($this->listaArticulos as $servicio) {
            $usuariosSave->articulos()->attach($servicio['id'], ['stock_usado' => $servicio['stock_usado']]);
        }
        // Alertas de guardado exitoso
        if ($usuariosSave) {
            $this->alert('success', 'Servicio registrado correctamente!', [
                'position' => 'center',
                'toast' => false,
                'showConfirmButton' => true,
                'onConfirmed' => 'confirmed',
                'confirmButtonText' => 'ok',
                'timerProgressBar' => true,
            ]);
        } else {
            $this->alert('error', '¡No se ha podido guardar la información del usuario!', [
                'position' => 'center',
                'toast' => false,
            ]);
        }
    }

    // Función para cuando se llama a la alerta
    public function getListeners()
    {
        return [
            'confirmed',
            'submit'
        ];
    }
    public function refreshArticulos()
    {
        if ($this->id_categoria != null && $this->id_categoria > 0) {
            $this->articulosSelect = $this->articulos->where('id_categoria', $this->id_categoria)->all();
        } else {
            $this->articulosSelect = $this->articulos->all();
        }
    }

    // Función para cuando se llama a la alerta
    public function confirmed()
    {
        // Do something
        return redirect()->route('servicios.index');
    }
}
