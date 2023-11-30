<?php

namespace App\Http\Livewire\MaterialesProducto;

use App\Models\MaterialesProducto;
use App\Models\Mercaderia;
use App\Models\Productos;
use App\Models\OrdenMercaderia;
use Livewire\Component;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Jantinnerezo\LivewireAlert\LivewireAlert;

class CreateComponent extends Component
{
    use LivewireAlert;
    public $identificador;
    public $producto;
    public $nombre_producto;
    public $imagen_producto;
    public $descripcion_producto;
    public $precio = 0;
    public $estado;
    public $fecha;
    public $observaciones;
    public $mercaderia_seleccionada;
    public $unidades_mercaderia;
    public $mercaderias_ordenadas = [];
    public $mercaderias;
    public $ordenes_mercaderias;

    protected $listeners = ['refreshComponent' => '$refresh'];

    public function mount()
    {
        $this->fecha = Carbon::now()->format('Y-m-d');
        $this->estado = 0;
        $this->mercaderias = Mercaderia::all();
        $this->ordenes_mercaderias = OrdenMercaderia::all();
        $this->producto = Productos::where('id', $this->identificador)->first();
        $this->nombre_producto = $this->producto->nombre;
        $this->imagen_producto = $this->producto->foto_ruta;
        $this->descripcion_producto = $this->producto->descripcion;
    }

    public function render()
    {
        return view('livewire.materiales-producto.create-component');
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

    public function deleteArticulo($id)
    {
        unset($this->mercaderias_ordenadas[$id]);
        $this->mercaderias_ordenadas = array_values($this->mercaderias_ordenadas);
        $this->setPrecioEstimado();
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

    public function setPrecioEstimado()
    {
        $this->precio = 0;
        foreach ($this->mercaderias_ordenadas as $mercaderias) {
            $this->precio += (($this->mercaderias->where('id', $mercaderias['mercaderia_id'])->first()->precio) * $mercaderias['cantidad']);
        }
    }

    public function submit()
    {

        foreach ($this->mercaderias_ordenadas as $mercaderias) {
            MaterialesProducto::create(['mercaderia_id' => $mercaderias['mercaderia_id'], 'producto_id' => $this->identificador, 'cantidad' => $mercaderias['cantidad']]);
        }
        $mercaderiaSave = true;

        // Alertas de guardado exitoso
        if ($mercaderiaSave) {
            $this->alert('success', '¡Órden de compra registrada correctamente!', [
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

    // Función para cuando se llama a la alerta
    public function getListeners()
    {
        return [
            'confirmed',
            'submit',
            'alertaGuardar',
            'checkLote'
        ];
    }
    public function alertaGuardar()
    {
        $this->alert('warning', 'Asegúrese de que todos los datos son correctos antes de guardar.', [
            'position' => 'center',
            'toast' => false,
            'showConfirmButton' => true,
            'onConfirmed' => 'submit',
            'confirmButtonText' => 'Sí',
            'showDenyButton' => true,
            'denyButtonText' => 'No',
            'timerProgressBar' => true,
        ]);
    }
    public function confirmed()
    {
        // Do something
        return redirect()->route('materiales-producto.index');
    }
}
