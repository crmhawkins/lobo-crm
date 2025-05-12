<?php

namespace App\Http\Livewire\Productos;

use App\Models\Productos;
use App\Models\ProductosCategories;
use App\Models\Iva;
use App\Models\ProductosMarketing;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Component;
use Livewire\WithFileUploads;

class CreateComponent extends Component
{

    use LivewireAlert;
    use WithFileUploads;

    public $nombre;
    public $tipo_precio=1;
    public $foto_ruta;
    public $unidades_por_caja = 0;
    public $cajas_por_pallet = 0;
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
    public $iva_id = 1;
    public $is_pack = false;
    public $products_id = [];
    public $products_id_marketing = [];
    public $productosDisponibles = [];
    public $productosSeleccionados = [];

    public $productosMarketingDisponibles = [];
    public $productosMarketingSeleccionados = [];
    public $searchTerm = 'Mini';
    public $searchTerm2 = 'Caja';


    public function mount()
    {
        $this->ivas = Iva::all();
        $this->productosDisponibles = Productos::all();
        $this->productosMarketingDisponibles = ProductosMarketing::all();
    }

    public function render()
    {
        return view('livewire.productos.create-component');
    }

    // Al hacer submit en el formulario
    public function submit()
    {

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
                'is_pack' => 'nullable',
                'products_id' => 'nullable',
                'products_id_marketing' => 'nullable',
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

        // Convertir productos seleccionados a JSON
        $validatedData['products_id'] = json_encode($this->productosSeleccionados);
        $validatedData['products_id_marketing'] = json_encode($this->productosMarketingSeleccionados);

        if(isset($this->foto_ruta))
        {

            $name = md5($this->foto_ruta . microtime()) . '.' . $this->foto_ruta->extension();

            $this->foto_ruta->storePubliclyAs('public', 'photos/' . $name);

            $validatedData['foto_ruta'] = $name;
        }

        // Guardar datos validados
        $productosSave = Productos::create($validatedData);

        // Alertas de guardado exitoso
        if ($productosSave) {
            $this->alert('success', '¡Producto registrado correctamente!', [
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
        return redirect()->route('productos.index');
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
