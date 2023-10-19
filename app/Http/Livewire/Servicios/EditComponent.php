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

class EditComponent extends Component
{
    use LivewireAlert;

    public $identificador;
    public $servicioCategorias;
    public $servicioPacks;

    public $articulo_seleccionado;
    public $numero_articulos;
    public $listaArticulos;
    public $listaArticulosEliminar;
    public $articulos;
    public $articulosSelect;
    public $stock_usado;
    public $tiempoMontaje;
    public $tiempoDesmontaje;
    public $tiempoServicio;

    public $nombre;
    public $precioBase;
    public $id_pack;
    public $id_categoria;
    public $minMonitor;
    public $precioMonitor;
    public $precioMonitorNocturno;
    public $precioMonitorAnimacion;

    public function mount()
    {
        $servicio = Servicio::find($this->identificador);
        $this->servicioCategorias = ServicioCategoria::all();
        $this->servicioPacks = ServicioPack::all();
        $this->articulos = Articulos::all();
        $this->articulosSelect = $this->articulos;
        $this->nombre = $servicio->nombre;
        $this->precioBase = $servicio->precioBase;
        $this->id_pack = $servicio->id_pack;
        $this->id_categoria = $servicio->id_categoria;
        $this->minMonitor = $servicio->minMonitor;
        $this->precioMonitor = $servicio->precioMonitor;
        $this->precioMonitorNocturno = $servicio->precioMonitorNocturno;
        $this->precioMonitorAnimacion = $servicio->precioMonitorAnimacion;
        $this->tiempoMontaje = $servicio->tiempoMontaje;
        $this->tiempoDesmontaje = $servicio->tiempoDesmontaje;
        $this->tiempoServicio = $servicio->tiempoServicio;

        foreach ($servicio->articulos()->get() as $servicio) {
            $this->listaArticulos[] = [
                'id' => $servicio->id,
                'stock_usado' => $servicio->pivot->stock_usado,
                'existente' => 1
            ];
        }
        if ($this->id_categoria > 0) {
            $this->refreshArticulos();
        }
    }

    public function precioTotal()
    {
        return intval($this->minMonitor) * intval($this->precioMonitor) + floatval($this->precioBase);
    }

    public function crearPack()
    {
        return Redirect::to(route("servicios-packs.create"));
    }


    public function render()
    {
        return view('livewire.servicios.edit-component');
    }

    public function addStock()
    {
        if ($this->articulo_seleccionado != 0) {
            if($this->stock_usado > Articulos::firstWhere('id', $this->articulo_seleccionado)->stock){
                $this->alert('error', '¡La selección sobrepasa al stock disponible!', [
                    'position' => 'center',
                    'toast' => false,
                    'showConfirmButton' => true,
                    'confirmButtonText' => 'ok',
                    'timerProgressBar' => true,
                ]);
            }else{
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


    public function deleteArticulos($indice)
    {
        if ($this->listaArticulos[$indice]['existente'] == 1) {
            $this->listaArticulosEliminar[] = $this->listaArticulos[$indice];
        }
        unset($this->listaArticulos[$indice]);
        $this->listaArticulos = array_values($this->listaArticulos);
    }

    // Al hacer update en el formulario
    public function update()
    {
        // Validación de datos
        $this->validate(
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
                'precioBase.required' => 'El precio base es obligatorio.',
                'id_categoria.required' => 'El id de la categoría es obligatorio.',
                'minMonitor.required' => 'El mínimo de monitores es obligatorio.',
                'precioMonitor.required' => 'El el precio minimo por monitor es obligatorio.',
                'tiempoMontaje.required' => 'El tiempo de montaje es obligatorio.',
                'tiempoDesmontaje.required' => 'El tiempo de desmontaje es obligatorio.',
                'tiempoServicio.required' => 'La duración del servicio es obligatorio.',
            ]
        );

        // Encuentra el identificador
        $servicio = Servicio::find($this->identificador);

        // Guardar datos validados
        $servicioSave = $servicio->update([
            'nombre' => $this->nombre,
            'precioBase' => $this->precioBase,
            'id_pack' => $this->id_pack,
            'id_categoria' => $this->id_categoria,
            'minMonitor' => $this->minMonitor,
            'precioMonitor' => $this->precioMonitor,
            'tiempoMontaje' => $this->tiempoMontaje,
            'tiempoDesmontaje' => $this->tiempoDesmontaje,
            'tiempoServicio' => $this->tiempoServicio,
        ]);
        event(new \App\Events\LogEvent(Auth::user(), 30, $servicio->id));

        if(!empty($this->listaArticulosEliminar)){
            foreach ($this->listaArticulosEliminar as $articulo) {
                $servicio->articulos()->wherePivot('stock_usado',  $servicio['stock_usado'])
                    ->detach($articulo['id']);
            }
        }



        foreach ($this->listaArticulos as $servicioAttach) {
            if ($servicioAttach['existente'] == 0) {
                $servicio->articulos()->attach(
                    $servicioAttach['id'],
                    [
                        'stock_usado' => $servicioAttach['stock_usado'],
                    ]
                );
            }
        }



        if ($servicioSave) {
            $this->alert('success', '¡Servicio actualizado correctamente!', [
                'position' => 'center',
                'toast' => false,
                'showConfirmButton' => true,
                'onConfirmed' => 'confirmed',
                'confirmButtonText' => 'ok',
                'timerProgressBar' => true,
            ]);
        } else {
            $this->alert('error', '¡No se ha podido guardar la información del servicio!', [
                'position' => 'center',
                'toast' => false,
            ]);
        }

        session()->flash('message', 'Servicio actualizado correctamente.');

        $this->emit('eventUpdated');
    }
    public function refreshArticulos()
    {
        if ($this->id_categoria != null && $this->id_categoria > 0) {
            $this->articulosSelect = $this->articulos->where('id_categoria', $this->id_categoria)->all();
        } else {
            $this->articulosSelect = $this->articulos->all();
        }
    }

    // Eliminación
    public function destroy()
    {

        $this->alert('warning', '¿Seguro que desea borrar el usuario? No hay vuelta atrás', [
            'position' => 'center',
            'toast' => false,
            'showConfirmButton' => true,
            'onConfirmed' => 'confirmDelete',
            'confirmButtonText' => 'Sí',
            'showDenyButton' => true,
            'denyButtonText' => 'No',
            'timerProgressBar' => true,
        ]);
    }

    // Función para cuando se llama a la alerta
    public function getListeners()
    {
        return [
            'confirmed',
            'confirmDelete',
            'update',
            'destroy'
        ];
    }

    // Función para cuando se llama a la alerta
    public function confirmed()
    {
        // Do something
        return redirect()->route('servicios.index');
    }
    // Función para cuando se llama a la alerta
    public function confirmDelete()
    {
        $servicio = Servicio::find($this->identificador);
        event(new \App\Events\LogEvent(Auth::user(), 31, $servicio->id));
        $servicio->delete();
        return redirect()->route('servicios.index');
    }
}
