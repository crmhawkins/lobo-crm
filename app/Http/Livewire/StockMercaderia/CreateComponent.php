<?php

namespace App\Http\Livewire\StockMercaderia;

use App\Models\Mercaderia;
use App\Models\StockMercaderia;
use App\Models\StockMercaderiaEntrante;
use Livewire\Component;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Jantinnerezo\LivewireAlert\LivewireAlert;

class CreateComponent extends Component
{
    use LivewireAlert;
    public $identificador;
    public $qr_id;
    public $lote_id;
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
        $this->qr_id = $this->identificador;
        $stockAsignado = Mercaderia::where('qr', $this->qr_id)->first();
        //dd($stockAsignado);
        if(isset($stockAsignado)){
            $material_id = StockMercaderiaEntrante ::where('mercaderia_id', $stockAsignado->id)->orderBy('created_at', 'desc')->first();
            if($material_id != null){
                $this->mercaderias_ordenadas[] = ['mercaderia_id' => $material_id->mercaderia_id, "cantidad" => 0];
            }else{
                $this->mercaderias_ordenadas[] = ['mercaderia_id' => $stockAsignado->id, "cantidad" => 0];
            }
        }
        $this->lote_id = "Selecciona un producto";
        $this->mercaderias = Mercaderia::all();
    }

    public function render()
    {
        return view('livewire.stock-mercaderia.create-component');
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
        $this->lote_id = $this->mercaderia_seleccionada . Carbon::now()->format('d') . Carbon::now()->format('m') . Carbon::now()->format('y');
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
                'qr_id' => 'required',
                'lote_id' => 'required',
                'estado' => 'required',
                'fecha' => 'required',
                'observaciones' => 'nullable',
            ],
            // Mensajes de error
            [
                'qr_id.required' => 'El precio del pedido es obligatorio.',
                'lote_id.required' => 'El numero de orden es obligatorio.',
                'estado.required' => 'El estado del pedido es obligatoria.',
                'fecha.required' => 'La fecha es obligatoria.',
            ]
        );

        // Guardar datos validados
        $mercaderiaSave = StockMercaderia::create($validatedData);

        foreach ($this->mercaderias_ordenadas as $mercaderias) {
            DB::table('stock_mercaderia_entrante')
                ->insert([
                    'mercaderia_id' => $mercaderias['mercaderia_id'],
                    'cantidad' => $mercaderias['cantidad'],
                    'tipo' => 'Entrante',
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ]);
        }

        // Alertas de guardado exitoso
        if ($mercaderiaSave) {
            $this->alert('success', '¡Stock entrante registrado correctamente!', [
                'position' => 'center',
                'timer' => null,
                'toast' => false,
                'showConfirmButton' => true,
                'onConfirmed' => 'confirmed',
                'confirmButtonText' => 'ok',
                'timerProgressBar' => false,
            ]);
        } else {
            $this->alert('error', '¡No se ha podido guardar la entrada del stock!', [
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
        $this->alert('warning', 'Comprueba que el stock introducido es correcto antes de guardar.', [
            'position' => 'center',
            'toast' => false,
            'showConfirmButton' => true,
            'onConfirmed' => 'submit',
            'confirmButtonText' => 'Sí',
            'showDenyButton' => true,
            'denyButtonText' => 'No',
            'timerProgressBar' => false,
        ]);
    }
    public function confirmed()
    {
        // Do something
        return redirect()->route('mercaderia.index');
        /*return redirect()->route('orden-mercaderia.index');*/
    }
}
