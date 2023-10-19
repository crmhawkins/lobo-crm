<?php

namespace App\Http\Livewire\Productos;

use App\Models\Productos;
use App\Models\ProductoLote;
use App\Models\ProductosCategories;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Component;
use Livewire\WithFileUploads;
use Carbon\Carbon;

class EditComponent extends Component
{
    use LivewireAlert;
    use WithFileUploads;

    public $identificador;

    public $nombre;
    public $precio;
    public $iva;
    public $foto_rutaOld;
    public $foto_ruta;
    public $nueva_foto = 0;
    public $producto_lotes = [];
    public $unidades_vendidas;
    public $unidades_disponibles;
    public $unidades_reservadas;

    public function mount()
    {
        $product = Productos::find($this->identificador);
        $this->nombre = $product->nombre;
        $this->precio = $product->precio;

        $this->iva = $product->iva;
        $this->precio = $product->precio;
        $this->unidades_disponibles = $product->unidades_disponibles;
        $this->unidades_reservadas = $product->unidades_reservadas;
        $this->unidades_vendidas = $product->unidades_vendidas;

        $product->foto_ruta != null ? $this->foto_rutaOld = $product->foto_ruta : $this->foto_rutaOld = '';

        $lotes = ProductoLote::where('producto_id', $this->identificador)->get();
        foreach ($lotes as $lote) {
            $this->producto_lotes[] = ['id' => $lote->id, 'lote_id' => $lote->lote_id, 'cantidad_inicial' => $lote->cantidad_inicial, 'unidades' => $lote->cantidad_actual,  'fecha_entrada' => Carbon::parse($lote->fecha_entrada)->format('d-m-Y')];
        }
    }

    public function render()
    {
        return view('livewire.productos.edit-component');
    }

    // Al hacer update en el formulario
    public function update()
    {

        // Validación de datos
        $validatedData = $this->validate(
            [
                'nombre' => 'required',
                'precio' => 'required',
                'iva' => 'required',
                'foto_ruta' => 'required',
            ],
            // Mensajes de error
            [
                'nombre.required' => 'La Categoria es obligatoria.',
                'precio.required' => 'El código de producto es obligatorio.',
                'iva.required' => 'La descripción es obligatoria.',
                'foto_ruta.required' => 'El precio es obligatorio.',
                'unidades_disponibles.required' => 'El nombre es obligatorio.',
                'unidades_reservadas.required' => 'La descripción es obligatoria.',
                'unidades_vendidas.required' => 'El precio es obligatorio.',
            ]
        );

        if (file_exists(public_path() . 'photos/' . $this->foto_rutaOld)) {
            $this->foto_ruta = $this->foto_rutaOld;
        } else {
            $name = md5($this->foto_ruta . microtime()) . '.' . $this->foto_ruta->extension();

            $this->foto_ruta->storePubliclyAs('public', 'photos/' . $name);
            $validatedData['foto_ruta'] = $name;
        }

        // Encuentra el producto identificado
        $product = Productos::find($this->identificador);

        // Guardar datos validados
        $productSave = $product->update($validatedData);

        if ($this->foto_ruta === $this->foto_rutaOld) {
            unset($this->foto_ruta);
        }

        if ($productSave) {
            $this->alert('success', '¡Producto actualizado correctamente!', [
                'position' => 'center',
                'timer' => 3000,
                'toast' => false,
                'showConfirmButton' => true,
                'onConfirmed' => 'confirmed',
                'confirmButtonText' => 'ok',
                'timerProgressBar' => true,
            ]);
        } else {
            $this->alert('error', '¡No se ha podido guardar la información del producto!', [
                'position' => 'center',
                'timer' => 3000,
                'toast' => false,
            ]);
        }

        session()->flash('message', 'Product updated successfully.');

        $this->emit('productUpdated');
    }

    // Elimina el producto
    public function destroy()
    {
        // $product = Productos::find($this->identificador);
        // $product->delete();

        $this->alert('warning', '¿Seguro que desea borrar el producto? No hay vuelta atrás', [
            'position' => 'center',
            'timer' => 3000,
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
            'update'
        ];
    }

    // Función para cuando se llama a la alerta
    public function confirmed()
    {
        // Do something
        return redirect()->route('productos.index');
    }
    // Función para cuando se llama a la alerta
    public function confirmDelete()
    {
        $product = Productos::find($this->identificador);
        $product->delete();
        return redirect()->route('productos.index');
    }

    public function nuevaFoto()
    {
        $this->nueva_foto = 1;
    }
}
