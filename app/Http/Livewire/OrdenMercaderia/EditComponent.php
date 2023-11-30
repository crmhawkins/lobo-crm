<?php

namespace App\Http\Livewire\OrdenMercaderia;

use App\Models\Mercaderia;
use App\Models\OrdenMercaderia;
use Livewire\Component;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Jantinnerezo\LivewireAlert\LivewireAlert;

class EditComponent extends Component
{
    use LivewireAlert;
    public $ordenes_mercaderias;
    public $orden;
    public $identificador;
    public $numero;
    public $precio = 0;
    public $estado;
    public $fecha;
    public $observaciones;
    public $mercaderia_seleccionada;
    public $unidades_mercaderia;
    public $mercaderias_ordenadas = [];
    public $mercaderias_ordenadas_borrar = [];
    public $mercaderias;

    public function mount()
    {
        $this->ordenes_mercaderias = OrdenMercaderia::all();
        $this->orden = OrdenMercaderia::find($this->identificador);
        $this->fecha = $this->orden->fecha;
        $this->precio = $this->orden->precio;
        $this->estado = $this->orden->estado;
        $this->mercaderias = Mercaderia::all();
        $this->ordenes_mercaderias = OrdenMercaderia::all();
        $this->numero = $this->orden->numero;
        $this->observaciones = $this->orden->observaciones;
        $mercaderias_orden = DB::table('mercaderias_ordenadas')->where('orden_id', $this->identificador)->get();
        foreach ($mercaderias_orden as $mercaderia) {
            $this->mercaderias_ordenadas[] = [
                'id' => $mercaderia->id,
                'mercaderia_id' => $mercaderia->mercaderia_id,
                'cantidad' => $mercaderia->cantidad,
                'borrar' => 0,
            ];
        }
    }
    public function confirmed()
    {
        // Do something
        return redirect()->route('orden-mercaderia.index');
    }
    public function render()
    {

        return view('livewire.orden-mercaderia.edit-component');
    }

    public function getMercaderiaNombre()
    {
        $mercaderia = Mercaderia::find($this->mercaderia_seleccionada);
        if ($mercaderia != null && $mercaderia->nombre != null) {
            return $mercaderia->nombre;
        }
    }

    public function getNombreTabla($id)
    {
        $nombre_producto = $this->mercaderias->where('id', $id)->first()->nombre;
        return $nombre_producto;
    }

    public function getPrecioIndividual($id)
    {
        $nombre_producto = $this->mercaderias->where('id', $this->mercaderias_ordenadas[$id]['mercaderia_id'])->first()->precio;
        return $nombre_producto * $this->mercaderias_ordenadas[$id]['cantidad'];
    }


    public function addMercaderia($id)
    {
        $mercaderia_existe = false;
        $mercaderia_id = $id;
        foreach ($this->mercaderias_ordenadas as $mercaderias) {
            if ($mercaderias['mercaderia_id'] == $id) {
                $mercaderia_existe = true;
                $mercaderia_id = $mercaderias['mercaderia_id'];
            }
        }
        if ($mercaderia_existe == true) {
            $mercaderia = array_search($mercaderia_id, array_column($this->mercaderias_ordenadas, 'mercaderia_id'));
            $this->mercaderias_ordenadas[$mercaderia]['cantidad'] = $this->mercaderias_ordenadas[$mercaderia]['cantidad'] + $this->unidades_mercaderia;
        } else {
            $this->mercaderias_ordenadas[] = ['mercaderia_id' => $id, "cantidad" => $this->unidades_mercaderia];
        }

        $this->mercaderia_seleccionada = 0;
        $this->unidades_mercaderia = 0;
        $this->setPrecioEstimado();
        $this->emit('refreshComponent');
    }
    public function alertaGuardar()
    {
        $this->alert('info', 'Asegúrese de que todos los datos son correctos antes de guardar.', [
            'position' => 'center',
            'toast' => false,
            'showConfirmButton' => true,
            'onConfirmed' => 'update',
            'confirmButtonText' => 'Sí',
            'showDenyButton' => true,
            'denyButtonText' => 'No',
            'timerProgressBar' => true,
        ]);
    }
    public function setPrecioEstimado()
    {
        $this->precio = 0;
        foreach ($this->mercaderias_ordenadas as $mercaderias) {
            $this->precio += (($this->mercaderias->where('id', $mercaderias['mercaderia_id'])->first()->precio) * $mercaderias['cantidad']);
        }
    }
    public function deleteArticulo($id)
    {
        if (isset($this->mercaderias_ordenadas[$id]['id'])) {
            $this->mercaderias_ordenadas_borrar[] = $this->mercaderias_ordenadas[$id];
        }
        unset($this->mercaderias_ordenadas[$id]);
        $this->mercaderias_ordenadas = array_values($this->mercaderias_ordenadas);
    }
    public function update()
    {

        $validatedData = $this->validate(
            [
                'numero' => 'required',
                'precio' => 'required',
                'estado' => 'required',
                'fecha' => 'required',
                'observaciones' => 'nullable',
            ],
            // Mensajes de error
            [
                'precio.required' => 'El precio del pedido es obligatorio.',
                'numero.required' => 'El numero de orden es obligatorio.',
                'estado.required' => 'El estado del pedido es obligatoria.',
                'fecha.required' => 'La fecha es obligatoria.',
            ]
        );


        $mercaderia = OrdenMercaderia::find($this->identificador);
        // Guardar datos validados
        $mercaderiaSave = $mercaderia->update($validatedData);

        foreach ($this->mercaderias_ordenadas as $mercaderias) {
            if (!isset($mercaderias['id'])) {
                DB::table('mercaderias_ordenadas')->insert([
                    'mercaderia_id' => $mercaderias['mercaderia_id'],
                    'orden_id' => $mercaderia->id,
                    'cantidad' => $mercaderias['cantidad']
                ]);
            } else {
                DB::table('mercaderias_ordenadas')->where('id', $mercaderias['id'])->limit(1)->update([
                    'mercaderia_id' => $mercaderias['mercaderia_id'],
                    'orden_id' => $mercaderia->id,
                    'cantidad' => $mercaderias['cantidad']
                ]);
            }
        }
        foreach ($this->mercaderias_ordenadas_borrar as $mercaderias) {
            if (isset($mercaderias['id'])) {
                DB::table('mercaderias_ordenadas')->where('id', $mercaderias['id'])->limit(1)->delete();
            }
        }

        // Alertas de guardado exitoso
        if ($mercaderiaSave) {
            $this->alert('success', '¡Pedido registrado correctamente!', [
                'position' => 'center',
                'timer' => 3000,
                'toast' => false,
                'showConfirmButton' => true,
                'onConfirmed' => 'confirmed',
                'confirmButtonText' => 'ok',
                'timerProgressBar' => true,
            ]);
        } else {
            $this->alert('error', '¡No se ha podido guardar la información del pedido!', [
                'position' => 'center',
                'timer' => 3000,
                'toast' => false,
            ]);
        }
    }
    public function getListeners()
    {
        return [
            'confirmed',
            'update',
            'alertaGuardar',
            'aceptarPedido',
            'rechazarPedido',
            'alertaAlmacen',
            'updateAlmacen',
            'checkLote'
        ];
    }
}
