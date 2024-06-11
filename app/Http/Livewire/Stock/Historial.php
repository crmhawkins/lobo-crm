<?php

namespace App\Http\Livewire\Stock;

use Livewire\Component;
use App\Models\Stock;
use Carbon\Carbon;
use App\Models\StockEntrante;
use App\Models\StockSaliente;
use App\Models\Almacen;
use App\Models\Productos;
use App\Models\StockRegistro;
use App\Models\Pedido;

class Historial extends Component
{

    public $isEntrada = 0;
    public $allData;
    public $producto_lotes;
    public $almacen_id;
    public $producto_id;
    public $producto_seleccionado = 0;
    public $productos_lotes_salientes;
    public $almacenes;
    public $productos;
    public $stockRegistro;


    public function formatFecha($id)
    {
        return Carbon::parse(Stock::find($id)->fecha)->format('d/m/Y');
    }
    public function isPedidoMarketing($pedidoId)
    {
        $pedido = Pedido::find($pedidoId);
        if ($pedido) {

            if($pedido->departamento_id == config('app.departamentos_pedidos')['Marketing']['id']){
                return true;
            }

        }
        return false;
    }
    public function mount(){

        $this->almacenes = Almacen::all();
        $this->productos = Productos::all();
        if($this->isEntrada){
            $this->setLotes();

            $arrayProductosLotes = [];
            foreach ($this->producto_lotes as $loteIndex => $lote) {
                if($lote->stockEntrante == null) continue;
                $arrayProductosLotes[] = [
                    'lote_id' => $lote->stockEntrante->lote_id,
                    'orden_numero' => $lote->stockEntrante->orden_numero,
                    'almacen' => $this->almacen($lote->stockEntrante),
                    'producto' => $this->getProducto($lote->stockEntrante->producto_id),
                    'fecha' => Carbon::parse($lote->created_at)->format('d/m/Y'),
                    'order_date' => Carbon::parse($lote->created_at)->format('Ymd'),
                    'cantidad' => abs($lote->cantidad),
                    'cajas' => floor(abs($lote->cantidad)/ $this->getUnidadeCaja($lote->stockEntrante->producto_id) ),
                ];
    
            }

            $this->producto_lotes = $arrayProductosLotes;

        }else{

            $this->allData = collect([]);

            $stocks = Stock::with([
                'entrantes',
                'entrantes.salidas',
                'modificaciones',
                'roturas'
            ])->get();

           
            //unificar los datos para la vista
            foreach ($stocks as $stock) {
                
                //si stock entrantes esta vacio continua
                if($stock->entrantes == null) continue;
    
                foreach ($stock->entrantes as $stockEntrante) {
                    //dd($stock->entrantes->salidas);
                    //stockEntrante un bool pero no deberia dar eso
                
                    foreach ($stock->entrantes->salidas as $salida) {
                        if(count($stock->entrantes->salidas) == 0) continue;
    
                        //antes de meterlo comprueba si el id ya está en el array, y si lo esta no lo meto.
                        if($this->allData->contains('id_salida', $salida->id)) continue;
                        //dd($salida);
                        $this->allData->push([
                            'id_salida' => $salida->id,
                            'interno' => $salida->stock_entrante_id,
                            'lote_id' => $stock->entrantes->lote_id,
                            'orden_numero' => $stock->entrantes->orden_numero,
                            'almacen' => $this->getAlmacen($salida->almacen_origen_id) ?? 'Almacen no asignado',
                            'producto' => $this->getProducto($salida->producto_id),
                            'fecha' => Carbon::parse($salida->fecha_salida)->format('d/m/Y'),
                            'order_date' => Carbon::parse($salida->fecha_salida)->format('Ymd'), // No hay fecha de pedido en la tabla 'salidas
                            'cantidad' => $salida->cantidad_salida,
                            'cajas' => floor($salida->cantidad_salida / $this->getUnidadeCaja($salida->producto_id)),
                            'tipo' => $salida->pedido_id ? 'Venta' : 'Salida',
                            'created_at' => $salida->created_at,
                            'pedido_id' => $salida->pedido_id ?? '',
                        ]);
                    }
                    foreach ($stock->modificaciones as $modificacion) {
                        
                        //antes de meterlo comprueba si el id ya está en el array, y si lo esta no lo meto.
                        if($this->allData->contains('id_modificacion', $modificacion->id)) continue;
                        //si la modificacion es tipo 'Suma' no la meto
                        if($modificacion->tipo == 'Suma') continue;
                        $this->allData->push([
                            'id_modificacion' => $modificacion->id,
                            'interno' => $modificacion->stock_id,
                            'lote_id' => $stock->entrantes->lote_id,
                            'orden_numero' => $stock->entrantes->orden_numero, // No hay orden asociada a modificaciones
                            'almacen' => $modificacion->almacen_id ? $this->getAlmacen($modificacion->almacen_id) : "Almacén no asignado.", // Ajustar según tu lógica de almacenamiento
                            'producto' => $this->getProducto($stock->entrantes->producto_id),
                            'fecha' => Carbon::parse($modificacion->fecha)->format('d/m/Y'),
                            'order_date' => Carbon::parse($modificacion->fecha)->format('Ymd'), // No hay fecha de pedido en la tabla 'modificaciones
                            'cantidad' => $modificacion->cantidad,
                            'cajas' => floor($modificacion->cantidad / $this->getUnidadeCaja($stock->entrantes->producto_id)),
                            'tipo' => 'Modificación',
                            'created_at' => $modificacion->created_at,
                            'pedido_id' => '-',
                        ]);
    
                        //comprueba si el id
                    }
    
                    foreach ($stock->roturas as $rotura) {
                        //antes de meterlo comprueba si el id ya está en el array, y si lo esta no lo meto.
                        if($this->allData->contains('id_rotura', $rotura->id)) continue;
                        $this->allData->push([
                            'id_rotura' => $rotura->id,
                            'interno' => $rotura->stock_id,
                            'lote_id' => $stock->entrantes->lote_id,
                            'orden_numero' => $stock->entrantes->orden_numero, // No hay orden asociada a roturas
                            'almacen' => $rotura->almacen_id ? $this->getAlmacen($rotura->almacen_id) : "Almacén no asignado.", // Ajustar según tu lógica de almacenamiento
                            'producto' => $this->getProducto($stock->entrantes->producto_id),
                            'fecha' => Carbon::parse($rotura->fecha)->format('d/m/Y'),
                            'order_date' => Carbon::parse($rotura->fecha)->format('Ymd'), // No hay fecha de pedido en la tabla 'roturas
                            'cantidad' => $rotura->cantidad,
                            'cajas' => floor($rotura->cantidad / $this->getUnidadeCaja($stock->entrantes->producto_id)),
                            'tipo' => 'Rotura',
                            'created_at' => $rotura->created_at,
                            'pedido_id' => '-', // No hay pedido asociado a roturas
                        ]);
                    }    
                }
            }
            
            if($this->almacen_id != null && $this->almacen_id != 0){	
                $this->allData = $this->allData->where('almacen', $this->getAlmacen($this->almacen_id));
            }

            if($this->producto_id != 0){
                $this->allData = $this->allData->where('producto', $this->getProducto($this->producto_id));
            }

            // Ordenar todos los datos por created_at
            $this->allData = $this->allData->sortBy('created_at');
            $this->producto_lotes = $this->allData;

        }
         
       
    }

    public function getAlmacenId($lote){

        if($lote == null) return null;
        if(Stock::where('id', $lote->stock_id)->first() == null) return null;
        
        return Stock::where('id', $lote->stock_id)->first()->almacen_id;
    }


    public function setLotes()
    {

        if($this->producto_id != null){
            $this->producto_seleccionado = $this->producto_id;
        }

        if($this->almacen_id == 0){
            $this->almacen_id = null;
        }

        if($this->almacen_id == null){
            if($this->producto_seleccionado == 0){

                // $this->producto_lotes = StockEntrante::where('cantidad','>', 0)->get();
                //hay que tener en cuenta que el stock ahora tiene que tener en cuenta el registro. StockEntrante es la cantidad inicial y stockRegistro es lo que se ha hecho con ese stock
                $this->producto_lotes = StockRegistro::with('stockEntrante')
                ->where('motivo', 'Entrada') // Filtrar por motivo 'Entrada'
                ->get();
                //  dd($producto_lotes);
                //necesito que productos lotes sea un join entre stockEntrante y stockRegistro, donde se coja la cantidad de stockRegistro y las demas columnas de stockEntrante
            }else{
                

                $this->producto_lotes = StockRegistro::with(['stockEntrante' => function ($query) {
                    $query->where('producto_id', $this->producto_seleccionado); // Filtrar por producto_id
                }])
                ->where('motivo', 'Entrada') // Filtrar por motivo 'Entrada' en StockRegistro
                ->get();
            }
        }else{
            if($this->producto_seleccionado == 0){

                $this->producto_lotes = StockRegistro::with('stockEntrante')
                ->where('motivo', 'Entrada') // Filtrar por motivo 'Entrada' en StockRegistro
                ->get();

                //recorrer producto_lotes y filtrar por almacen_id
                $this->producto_lotes = $this->producto_lotes->filter(function ($value, $key) {
                    //dd($this->getAlmacenId($value->stockEntrante) , $this->almacen_id);
                    return $this->getAlmacenId($value->stockEntrante) == $this->almacen_id;
                });


            }else{
                $this->producto_lotes = StockRegistro::with(['stockEntrante' => function ($query) {
                    $query->where('producto_id', $this->producto_seleccionado); // Filtrar por producto_id
                }])
                ->where('motivo', 'Entrada') // Filtrar por motivo 'Entrada' en StockRegistro
                ->get();

                //recorrer producto_lotes y filtrar por almacen_id
                $this->producto_lotes = $this->producto_lotes->filter(function ($value, $key) {
                    return $this->getAlmacenId($value->stockEntrante) == $this->almacen_id;
                });
            }

        }
    }

    public function getUnidadeCaja($id)
    {
        $producto = Productos::find($id);
        if($producto == null){
            return 1;
        }
        return  $producto->unidades_por_caja;
    }

    public function getProducto($id)
    {
        $producto = Productos::find($id);
        if($producto == null){
            return 'Producto no encontrado';
        }
        return $producto->nombre;
    }


    public function getAlmacen($id)
    {
        $almacen = Almacen::find($id);
        if($almacen == null){
            return null;
        }
        return $almacen->almacen;
    }

    public function almacen($lote){

        $almacenId = Stock::where('id', $lote->stock_id)->first()->almacen_id;

        $almace = Almacen::find($almacenId);
        if(isset($almace)){
            return $almace->almacen;
        }else{
            return 'Almacen no asignado';
        }
        }

    public function updated($field)
    {
       if($field == 'isEntrada'){
           $this->mount();
       }
       if($field == 'almacen_id' || $field == 'producto_id'){
        $this->mount();
    }
    }

    public function render()
    {
        return view('livewire.stock.historial');
    }
}
