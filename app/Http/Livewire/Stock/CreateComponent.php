<?php

namespace App\Http\Livewire\Stock;

use App\Models\ProductoLote;
use App\Models\Productos;
use App\Models\ProductosCategories;

use Illuminate\Support\Carbon;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Component;
use Livewire\WithFileUploads;

class CreateComponent extends Component
{
    use LivewireAlert;
    use WithFileUploads;

    public $identificador;
    public $producto;
    public $nombre;
    public $cantidad_inicial;
    public $cantidad_actual;
    public $lote_id;
    public $producto_id;
    public $fecha_entrada;
    public $estado = 1;

    public function mount()
    {
        $this->producto = Productos::find($this->identificador);
        $this->nombre = $this->producto->nombre;
        $this->fecha_entrada = Carbon::now()->format('Y-m-d');
    }

    public function render()
    {
        return view('livewire.stock.create-component');
    }

    // Al hacer submit en el formulario
    public function submit()
    {
        $this->cantidad_actual = $this->cantidad_inicial;
        $this->producto_id = $this->identificador;
        // Validación de datos
        $validatedData = $this->validate(
            [
                'lote_id' => 'required',
                'producto_id' => 'required',
                'cantidad_actual' => 'required',
                'cantidad_inicial' => 'required',
                'fecha_entrada' => 'required',
                'estado' => 'required',
            ],
            // Mensajes de error
            [
                'lote_id.required' => 'La identificación del lote es obligatoria.',
                'producto_id.required' => 'El ID de producto es obligatorio.',
                'cantidad_actual.required' => 'La cantidad de unidades del producto es obligatoria.',
                'cantidad_inicial.required' => 'La cantidad de unidades del producto es obligatoria.',
                'fecha_entrada.required' => 'La fecha de entrada del lote es obligatorio.',
                'estado.required' => 'El estado del lote es obligatoria.',
            ]
        );
            // Guardar datos validados
        $productosSave = ProductoLote::create($validatedData);

        // Alertas de guardado exitoso
        if ($productosSave) {
            $this->alert('success', 'Lote de producto registrado correctamente!', [
                'position' => 'center',
                'timer' => 3000,
                'toast' => false,
                'showConfirmButton' => true,
                'onConfirmed' => 'confirmed',
                'confirmButtonText' => 'ok',
                'timerProgressBar' => true,
            ]);
        } else {
            $this->alert('error', '¡No se ha podido guardar el lote de producto!', [
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
            'submit'
        ];
    }

    // Función para cuando se llama a la alerta
    public function confirmed()
    {
        // Do something
        return redirect()->route('stock.index');
    }
}
