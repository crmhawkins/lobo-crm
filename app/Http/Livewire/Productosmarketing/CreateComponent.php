<?php

namespace App\Http\Livewire\Productosmarketing;

use App\Models\ProductosMarketing;
use Livewire\Component;
use Livewire\WithFileUploads;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Illuminate\Support\Facades\Storage;

class CreateComponent extends Component
{
    use LivewireAlert;
    use WithFileUploads;

    public $nombre;
    public $descripcion;
    public $materiales;
    public $peso_neto_unidad;
    public $unidades_por_caja;
    public $cajas_por_pallet;
    public $foto_ruta;

    public function mount()
    {
        // Inicializar valores si es necesario
    }

    public function render()
    {
        return view('livewire.productosmarketing.create-component');
    }

    // Al hacer submit en el formulario
    public function submit()
    {
        // Validación de datos
        $validatedData = $this->validate(
            [
                'nombre' => 'required|string|max:255',
                'descripcion' => 'nullable|string|max:1000',
                'materiales' => 'nullable|string|max:1000',
                'peso_neto_unidad' => 'nullable|numeric|min:0',
                'unidades_por_caja' => 'nullable|integer|min:1',
                'cajas_por_pallet' => 'nullable|integer|min:1',
                'foto_ruta' => 'nullable|image|max:2048', // Max 2MB de imagen
            ],
            // Mensajes de error personalizados
            [
                'nombre.nullable' => 'El nombre es obligatorio.',
                
            ]
        );

        // Si se ha subido una imagen, guardarla
        if ($this->foto_ruta) {
            $path = $this->foto_ruta->store('productos', 'public');
            $validatedData['foto_ruta'] = $path;
        }

        // Crear el nuevo producto en la base de datos
        ProductosMarketing::create([
            'nombre' => $this->nombre,
            'description' => $this->descripcion,
            'materiales' => $this->materiales,
            'peso_neto_unidad' => $this->peso_neto_unidad,
            'unidades_por_caja' => $this->unidades_por_caja,
            'cajas_por_pallet' => $this->cajas_por_pallet,
            'foto_ruta' => $validatedData['foto_ruta'] ?? null,
        ]);

        // Enviar mensaje de éxito
        $this->alert('success', 'Producto creado correctamente', [
            'position' => 'center',
            'timer' => 3000,
            'toast' => false,
        ]);

        // Redirigir a la lista de productos
        return redirect()->route('productosmarketing.index');
    }

    // Listeners para la alerta de confirmación
    public function getListeners()
    {
        return [
            'confirmed',
            'submit'
        ];
    }

    // Acción después de la confirmación
    public function confirmed()
    {
        // Redirigir tras la confirmación
        return redirect()->route('productos.index');
    }
}
