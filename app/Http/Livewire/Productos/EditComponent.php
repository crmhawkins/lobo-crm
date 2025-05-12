<?php

namespace App\Http\Livewire\Productos;

use App\Models\Productos;
use App\Models\ProductoLote;
use App\Models\ProductosCategories;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Component;
use Livewire\WithFileUploads;
use Carbon\Carbon;
use App\Models\ProductoPrecioCliente;
use App\Models\Iva;
use App\Models\ProductosMarketing;
use function PHPUnit\Framework\isNull;
use App\Models\CostesProductos;

class EditComponent extends Component
{
    use LivewireAlert;
    use WithFileUploads;

    public $identificador;

    public $nombre;
    public $tipo_precio;
    public $iva;
    public $foto_rutaOld;
    public $foto_ruta;
    public $nueva_foto = 0;
    public $producto_lotes = [];
    public $unidades_por_caja;
    public $cajas_por_pallet;
    public $descripcion;
    public $materiales;
    public $medidas_botella;
    public $peso_neto_unidad;
    public $temp_conservacion;
    public $caducidad;
    public $ingredientes;
    public $alergenos;
    public $proceso_elaboracion;
    public $info_nutricional;
    public $grad_alcohol;
    public $domicilio_fabricante;
    public $stock_seguridad;
    public $precio;
    public $ivas;
    public $iva_id;
    public $is_pack = false;
    public $productosDisponibles = [];
    public $productosSeleccionados = [];
    public $searchTerm = 'Mini';
    public $searchTerm2 = 'Caja';
    public $productosMarketingDisponibles = [];
    public $productosMarketingSeleccionados = [];
    public $costes = [];
    public $nuevoCoste = 0;
    public $costesEditados = [];

    public function mount()
    {
        $product = Productos::find($this->identificador);
        $this->nombre = $product->nombre;
        $this->tipo_precio = $product->tipo_precio;
        $this->precio = $product->precio;
        $this->unidades_por_caja = $product->unidades_por_caja;
        $this->cajas_por_pallet = $product->cajas_por_pallet;
        $this->stock_seguridad = $product->stock_seguridad;
        $this->descripcion = $product->descripcion;
        $this->materiales = $product->materiales;
        $this->medidas_botella = $product->medidas_botella;
        $this->peso_neto_unidad = $product->peso_neto_unidad;
        $this->temp_conservacion = $product->temp_conservacion;
        $this->caducidad = $product->caducidad;
        $this->ingredientes = $product->ingredientes;
        $this->alergenos = $product->alergenos;
        $this->proceso_elaboracion = $product->proceso_elaboracion;
        $this->info_nutricional = $product->info_nutricional;
        $this->grad_alcohol = $product->grad_alcohol;
        $this->domicilio_fabricante = $product->domicilio_fabricante;
        $this->ivas = Iva::all();
        $this->iva_id = $product->iva_id;
        $this->is_pack = $product->is_pack;
        $this->productosSeleccionados = json_decode($product->products_id, true) ?? [];
        $this->productosDisponibles = Productos::all();
        $this->productosMarketingDisponibles = ProductosMarketing::all();
        $this->productosMarketingSeleccionados = json_decode($product->products_id_marketing, true) ?? [];
        $product->foto_ruta != null ? $this->foto_rutaOld = $product->foto_ruta : $this->foto_rutaOld = '';

        $lotes = ProductoLote::where('producto_id', $this->identificador)->get();
        foreach ($lotes as $lote) {
            $this->producto_lotes[] = ['id' => $lote->id, 'lote_id' => $lote->lote_id, 'cantidad_inicial' => $lote->cantidad_inicial, 'unidades' => $lote->cantidad_actual,  'fecha_entrada' => Carbon::parse($lote->fecha_entrada)->format('d-m-Y')];
        }

        $this->cargarCostes();

    }

    protected function cargarCostes()
{
    $this->costes = CostesProductos::where('producto_id', $this->identificador)
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

        CostesProductos::create([
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

        $coste = CostesProductos::find($costeId);
        $coste->update([
            'coste' => $this->costesEditados[$costeId]['coste'],
            'fecha' => $this->costesEditados[$costeId]['fecha']
        ]);

        $this->cargarCostes();
        $this->alert('success', 'Coste actualizado correctamente');
    }

    public function eliminarCoste($costeId)
    {
        $coste = CostesProductos::find($costeId);
        if ($coste) {
            $coste->delete();
            $this->cargarCostes();
            $this->alert('success', 'Coste eliminado correctamente');
        }
    }

    public function render()
    {
        return view('livewire.productos.edit-component');
    }

    // Al hacer update en el formulario
    public function update()
    {
        if (file_exists('storage/photos/' . $this->foto_rutaOld) && is_null($this->foto_ruta)) {
            $this->foto_ruta = $this->foto_rutaOld;
        }

        // Validación de datos
        $validatedData = $this->validate(
            [
                'nombre' => 'required',
                'tipo_precio' => 'nullable',
                'foto_ruta' => 'nullable',
                'unidades_por_caja' => 'nullable',
                'cajas_por_pallet' => 'nullable',
                'descripcion' => 'nullable',
                'materiales' => 'nullable',
                'medidas_botella' => 'nullable',
                'peso_neto_unidad' => 'nullable',
                'temp_conservacion' => 'nullable',
                'caducidad' => 'nullable',
                'ingredientes' => 'nullable',
                'alergenos' => 'nullable',
                'proceso_elaboracion' => 'nullable',
                'info_nutricional' => 'nullable',
                'grad_alcohol' => 'nullable',
                'domicilio_fabricante' => 'nullable',
                'stock_seguridad' => 'nullable',
                'precio' => 'nullable',
                'iva_id' => 'required',
                'is_pack' => 'boolean',
            ],
            // Mensajes de error
            [
                'nombre.required' => 'La Categoria es obligatoria.',
                'tipo_precio.required' => 'El código de producto es obligatorio.',
                'foto_ruta.required' => 'El precio es obligatorio.',
                'unidades_por_caja.required' => 'El nombre es obligatorio.',
                'cajas_por_pallet.required' => 'La descripción es obligatoria.',
            ]
        );
        if ($this->foto_ruta === $this->foto_rutaOld) {
            unset($this->foto_ruta);
        }


            if(isset($this->foto_ruta))
            {
                $name = md5($this->foto_ruta . microtime()) . '.' . $this->foto_ruta->getClientOriginalExtension();
                $this->foto_ruta->storeAs('photos', $name, 'public'); // Guarda en storage/app/public/photos
                $validatedData['foto_ruta'] = $name; // Actualiza la base de datos con el nuevo nombre de archivo
            }

        // Si is_pack es false, limpiar productosSeleccionados
        if (!$this->is_pack) {
            $this->productosSeleccionados = [];
            $this->productosMarketingSeleccionados = [];
        }

        // Convertir productos seleccionados a JSON
        $validatedData['products_id'] = json_encode($this->productosSeleccionados);
        $validatedData['products_id_marketing'] = json_encode($this->productosMarketingSeleccionados);
        $validatedData['is_pack'] = $this->is_pack;

        // Encuentra el producto identificado
        $product = Productos::find($this->identificador);

        // Guardar datos validados
        $productSave = $product->update($validatedData);



        if ($productSave) {
            $this->alert('success', '¡Producto actualizado correctamente!', [
                'position' => 'center',
                'timer' => null,
                'toast' => false,
                'showConfirmButton' => true,
                'onConfirmed' => 'confirmed',
                'confirmButtonText' => 'ok',
                'timerProgressBar' => false,
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
        if($product){
            $productos = ProductoPrecioCliente::where('producto_id', $this->identificador)->get();
            foreach ($productos as $producto) {
                $producto->delete();
            }
        }

        $product->delete();
        return redirect()->route('productos.index');
    }

    public function nuevaFoto()
    {
        $this->nueva_foto = 1;
    }

    public function agregarProducto($productoId)
    {
        if (!in_array($productoId, $this->productosSeleccionados)) {
            $this->productosSeleccionados[] = $productoId;
        }
    }

    public function agregarProductoMarketing($productoId)
    {
        if (!in_array($productoId, $this->productosMarketingSeleccionados)) {
            $this->productosMarketingSeleccionados[] = $productoId;
        }
    }

    public function eliminarProducto($productoId)
    {
        $this->productosSeleccionados = array_filter($this->productosSeleccionados, function($id) use ($productoId) {
            return $id !== $productoId;
        });
    }

    public function eliminarProductoMarketing($productoId)
    {
        $this->productosMarketingSeleccionados = array_filter($this->productosMarketingSeleccionados, function($id) use ($productoId) {
            return $id !== $productoId;
        });
    }

    public function getFilteredProductosProperty()
    {
        return $this->productosDisponibles->filter(function($producto) {
            return stripos($producto->nombre, $this->searchTerm) !== false;
        });
    }

    public function getFilteredProductosMarketingProperty()
    {
        return $this->productosMarketingDisponibles->filter(function($producto) {
            return stripos($producto->nombre, $this->searchTerm2) !== false;
        });
    }
}
