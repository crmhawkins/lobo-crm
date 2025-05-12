<?php

namespace App\Http\Livewire\Productosmarketing;

use App\Models\ProductosMarketing;
use App\Models\CostesProductosMarketing;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;
class EditComponent extends Component
{
    use LivewireAlert;
    use WithFileUploads;

    public $identificador;
    public $nombre;
    public $peso_neto_unidad;
    public $unidades_por_caja;
    public $cajas_por_pallet;
    public $descripcion;
    public $materiales;
    public $foto_ruta;
    public $foto_rutaOld;
    public $nueva_foto = 0;
    public $producto;
    public $costes = [];
    public $nuevoCoste = 0;
    public $costesEditados = [];


    public function mount($identificador)
    {
        // Cargar el producto desde el modelo ProductosMarketing
        $producto = ProductosMarketing::find($identificador);

        if ($producto) {
            $this->producto = $producto;
            $this->identificador = $producto->id;
            $this->nombre = $producto->nombre;
            $this->peso_neto_unidad = $producto->peso_neto_unidad;
            $this->unidades_por_caja = $producto->unidades_por_caja;
            $this->cajas_por_pallet = $producto->cajas_por_pallet;
            $this->descripcion = $producto->description;
            $this->materiales = $producto->materiales;
            $this->foto_rutaOld = $producto->foto_ruta;
        }
        $this->cargarCostes();

    }

    protected function cargarCostes()
    {
        $this->costes = CostesProductosMarketing::where('producto_id', $this->identificador)
            ->orderBy('fecha', 'desc')
            ->get();

        foreach ($this->costes as $coste) {
            $this->costesEditados[$coste->id] = [
                'coste' => $coste->coste,
                'fecha' => Carbon::parse($coste->fecha)->format('Y-m-d') // Convertir a Carbon antes de formatear
            ];
        }
    }

    public function agregarCoste()
    {
        $this->validate([
            'nuevoCoste' => 'required|numeric|min:0'
        ]);

        CostesProductosMarketing::create([
            'producto_id' => $this->identificador,
            'coste' => $this->nuevoCoste,
            'fecha' => now()
        ]);

        $this->nuevoCoste = 0;
        $this->cargarCostes();
        $this->alert('success', 'Coste añadido correctamente');
    }

    public function actualizarCoste($costeId)
    {
        $this->validate([
            'costesEditados.'.$costeId.'.coste' => 'required|numeric|min:0',
            'costesEditados.'.$costeId.'.fecha' => 'required|date'
        ]);

        $coste = CostesProductosMarketing::find($costeId);
        $coste->update([
            'coste' => $this->costesEditados[$costeId]['coste'],
            'fecha' => $this->costesEditados[$costeId]['fecha']
        ]);

        $this->cargarCostes();
        $this->alert('success', 'Coste actualizado correctamente');
    }


    public function eliminarCoste($costeId)
    {
        $coste = CostesProductosMarketing::find($costeId);
        if ($coste) {
            $coste->delete();
            $this->cargarCostes();
            $this->alert('success', 'Coste eliminado correctamente');
        }
    }

    public function render()
    {
        return view('livewire.productosmarketing.edit-component');
    }

    // Actualiza el producto en la base de datos
    public function update()
    {
        $this->validate([
            'nombre' => 'required|string|max:255',
            'peso_neto_unidad' => 'nullable|numeric',
            'unidades_por_caja' => 'nullable|integer',
            'cajas_por_pallet' => 'nullable|integer',
            'descripcion' => 'nullable|string',
            'materiales' => 'nullable|string',
            'foto_ruta' => 'nullable|image|max:1024', // Limite el tamaño de la imagen a 1MB
        ]);

        $producto = ProductosMarketing::find($this->identificador);

        if ($producto) {
            $producto->nombre = $this->nombre;
            $producto->peso_neto_unidad = $this->peso_neto_unidad;
            $producto->unidades_por_caja = $this->unidades_por_caja;
            $producto->cajas_por_pallet = $this->cajas_por_pallet;
            $producto->description = $this->descripcion;
            $producto->materiales = $this->materiales;

            // Si hay una nueva foto, se almacena y se elimina la anterior
            if ($this->foto_ruta) {
                if ($this->foto_rutaOld) {
                    Storage::delete('public/photos/' . $this->foto_rutaOld);
                }
                $path = $this->foto_ruta->store('photos', 'public');
                $producto->foto_ruta = $path;
            }

            $producto->save();

            $this->alert('success', 'Producto actualizado con éxito');
        }
    }

    // Elimina el producto
    public function destroy()
    {
        $this->alert('warning', '¿Seguro que desea borrar el producto? No hay vuelta atrás', [
            'position' => 'center',
            'timer' => null,
            'toast' => false,
            'showConfirmButton' => true,
            'onConfirmed' => 'confirmDelete',
            'confirmButtonText' => 'Sí',
            'showDenyButton' => true,
            'denyButtonText' => 'No',
            'timerProgressBar' => false,
        ]);
    }

    // Función para confirmar eliminación
    public function confirmDelete()
    {
        $producto = ProductosMarketing::find($this->identificador);

        if ($producto) {
            if ($producto->foto_ruta) {
                Storage::delete('public/photos/' . $producto->foto_ruta);
            }
            $producto->delete();
            return redirect()->route('productosmarketing.index')->with('success', 'Producto eliminado con éxito.');
        }
    }



    // Maneja la subida de la nueva foto
    public function nuevaFoto()
    {
        $this->nueva_foto = 1;
    }

    // Listeners para las alertas de Livewire
    public function getListeners()
    {
        return [
            'confirmed',
            'confirmDelete',
            'update',
        ];
    }

    public function confirmed()
    {
        return redirect()->route('productosmarketing.index');
    }
}
