<?php

namespace App\Http\Livewire\OrdenMercaderia;

use App\Models\Mercaderia;
use App\Models\OrdenMercaderia;
use Livewire\Component;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Jantinnerezo\LivewireAlert\LivewireAlert;

class CreateComponent extends Component
{
    use LivewireAlert;
    public $numero;
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
        $this->numero = Carbon::now()->format('y') . '/' . sprintf('%04d', $this->ordenes_mercaderias->whereBetween('fecha', [Carbon::now()->startOfYear(), Carbon::now()->endOfYear()])->count() + 1);

    }

    public function render()
    {

        return view('livewire.orden-mercaderia.create-component');
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

        // Validación de datos
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

        // Guardar datos validados
        $mercaderiaSave = OrdenMercaderia::create($validatedData);

        foreach ($this->mercaderias_ordenadas as $mercaderias) {
            DB::table('mercaderias_ordenadas')
                ->insert([
                    'mercaderia_id' => $mercaderias['mercaderia_id'],
                    'orden_id' => $mercaderiaSave->id,
                    'cantidad' => $mercaderias['cantidad']
                ]);
        }

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
        return redirect()->route('orden-mercaderia.index');
    }
}
