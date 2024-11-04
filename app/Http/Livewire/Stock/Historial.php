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
use App\Models\User;
use App\Models\Delegacion;
use App\Models\Clients;

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
    public $tipo; //otro filtro
    public $mes; //otro filtro
    public $anio; // filtro para año 
    public $comerciales = [];
    public $comercial_id = 0;
    public $delegaciones = [];
    public $delegacion_id = -1;

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

    public function mount()
{
    $this->almacenes = Almacen::all();
    $this->productos = Productos::all();
    $this->mes = $this->mes ?? Carbon::now()->month;
    $this->anio = $this->anio ?? Carbon::now()->year;
    $this->comerciales = User::whereIn('role', [2, 3])->get();
    $this->delegaciones = Delegacion::all();


    if($this->isEntrada){
        //dd('Entrada');
        $this->setLotes();
        // //dd($this->producto_lotes);
         $arrayProductosLotes = [];
         $this->producto_lotes = $this->producto_lotes->sortBy('created_at');
        foreach ($this->producto_lotes as $loteIndex => $lote) {
            if ($lote->created_at == null) continue;
            if($lote->created_at->year != $this->anio) continue;
            if($lote->created_at->month != $this->mes) continue;
            if($lote->stockEntrante == null) {
                //dd($lote);
                $qr = stock::where('id', $lote->stock_id)->first() ?  stock::where('id', $lote->stock_id)->first()->qr_id : 'No asignado';
                $arrayProductosLotes[] = [
                    'lote_id' => $lote->lote_id,
                    'orden_numero' => $lote->orden_numero,
                    'almacen' => stock::where('id', $lote->stock_id)->first() ? $this->getAlmacen(stock::where('id', $lote->stock_id)->first()->almacen_id) : 'Almacen no asignado',
                    'producto' => $this->getProducto($lote->producto_id),
                    'fecha' => Carbon::parse($lote->created_at)->format('d/m/Y'),
                    'order_date' => Carbon::parse($lote->created_at)->format('Ymd'),
                    'cantidad' => abs($lote->cantidad),
                    'cajas' => floor(abs($lote->cantidad)/ $this->getUnidadeCaja($lote->producto_id) ),
                    'qr' => $qr,
                ];
            }else{
                $qr = stock::where('id', $lote->stockEntrante->stock_id)->first() ?  stock::where('id', $lote->stockEntrante->stock_id)->first()->qr_id : 'No asignado';
                $arrayProductosLotes[] = [
                    'lote_id' => $lote->stockEntrante->lote_id,
                    'orden_numero' => $lote->stockEntrante->orden_numero,
                    'almacen' => $this->almacen($lote->stockEntrante),
                    'producto' => $this->getProducto($lote->stockEntrante->producto_id),
                    'fecha' => Carbon::parse($lote->created_at)->format('d/m/Y'),
                    'order_date' => Carbon::parse($lote->created_at)->format('Ymd'),
                    'cantidad' => abs($lote->cantidad),
                    'cajas' => floor(abs($lote->cantidad)/ $this->getUnidadeCaja($lote->stockEntrante->producto_id) ),
                    'qr' => $qr,
                ];
            }
            

        }

        $this->producto_lotes = $arrayProductosLotes;

        // $query = StockRegistro::with('stockEntrante')->where('motivo', 'Entrada');
        // if ($this->producto_id != 0) {
        //     $query->whereHas('stockEntrante', function ($q) {
        //         $q->where('producto_id', $this->producto_id);
        //     });
        // }

        // if ($this->almacen_id != 0) {
        //     $query->whereHas('stockEntrante', function ($q) {
        //         $q->where('almacen_id', $this->almacen_id);
        //     });
        // }

        // // Aplicar filtro por mes y año
        // $query->whereYear('created_at', $this->anio);
        // $this->mes = 10;
        // if ($this->mes != 0) {
        //     $query->whereMonth('created_at', $this->mes);
        // }
        // //dd($query->get());

        // $this->producto_lotes = $query->get();

        // $this->filters();

    } else {

        // $this->allData = collect([]);

        // $stocks = Stock::with([
        //     'entrantes',
            
        // ])->get();

        

        // // Unificar los datos para la vista
        // foreach ($stocks as $stock) {

        //     // Si stock entrantes esta vacío, continúa
        //     if($stock->entrantes == null) continue;

        //     foreach ($stock->entrantes as $stockEntrante) {
        //         foreach ($stock->entrantes->salidas as $salida) {
        //             if(count($stock->entrantes->salidas) == 0) continue;

        //             // Antes de meterlo, comprueba si el id ya está en el array, y si lo está, no lo meto.
        //             if($this->allData->contains('id_salida', $salida->id)) continue;
        //             $this->allData->push([
        //                 'id_salida' => $salida->id,
        //                 'interno' => $salida->stock_entrante_id,
        //                 'lote_id' => $stock->entrantes->lote_id,
        //                 'orden_numero' => $stock->entrantes->orden_numero,
        //                 'almacen' => $this->getAlmacen($salida->almacen_origen_id) ?? 'Almacen no asignado',
        //                 'producto' => $this->getProducto($salida->producto_id),
        //                 'fecha' => Carbon::parse($salida->fecha_salida)->format('d/m/Y'),
        //                 'order_date' => Carbon::parse($salida->fecha_salida)->format('Ymd'),
        //                 'cantidad' => $salida->cantidad_salida,
        //                 'cajas' => floor($salida->cantidad_salida / $this->getUnidadeCaja($salida->producto_id)),
        //                 'tipo' => $salida->pedido_id ? 'Venta' : 'Salida',
        //                 'created_at' => $salida->created_at,
        //                 'pedido_id' => $salida->pedido_id ?? '',
        //                 'qr' => $stock->qr_id,
        //             ]);
        //         }

        //         foreach ($stock->modificaciones as $modificacion) {

        //             // Antes de meterlo, comprueba si el id ya está en el array, y si lo está, no lo meto.
        //             if($this->allData->contains('id_modificacion', $modificacion->id)) continue;

        //             // Si la modificación es tipo 'Suma', no la meto
        //             if($modificacion->tipo == 'Suma') continue;

        //             $this->allData->push([
        //                 'id_modificacion' => $modificacion->id,
        //                 'interno' => $modificacion->stock_id,
        //                 'lote_id' => $stock->entrantes->lote_id,
        //                 'orden_numero' => $stock->entrantes->orden_numero,
        //                 'almacen' => $modificacion->almacen_id ? $this->getAlmacen($modificacion->almacen_id) : "Almacén no asignado.",
        //                 'producto' => $this->getProducto($stock->entrantes->producto_id),
        //                 'fecha' => Carbon::parse($modificacion->fecha)->format('d/m/Y'),
        //                 'order_date' => Carbon::parse($modificacion->fecha)->format('Ymd'),
        //                 'cantidad' => $modificacion->cantidad,
        //                 'cajas' => floor($modificacion->cantidad / $this->getUnidadeCaja($stock->entrantes->producto_id)),
        //                 'tipo' => 'Modificación',
        //                 'created_at' => $modificacion->created_at,
        //                 'pedido_id' => '-',
        //                 'qr' => $stock->qr_id,
        //             ]);
        //         }

        //         foreach ($stock->roturas as $rotura) {

        //             // Antes de meterlo, comprueba si el id ya está en el array, y si lo está, no lo meto.
        //             if($this->allData->contains('id_rotura', $rotura->id)) continue;

        //             $this->allData->push([
        //                 'id_rotura' => $rotura->id,
        //                 'interno' => $rotura->stock_id,
        //                 'lote_id' => $stock->entrantes->lote_id,
        //                 'orden_numero' => $stock->entrantes->orden_numero,
        //                 'almacen' => $rotura->almacen_id ? $this->getAlmacen($rotura->almacen_id) : "Almacén no asignado.",
        //                 'producto' => $this->getProducto($stock->entrantes->producto_id),
        //                 'fecha' => Carbon::parse($rotura->fecha)->format('d/m/Y'),
        //                 'order_date' => Carbon::parse($rotura->fecha)->format('Ymd'),
        //                 'cantidad' => $rotura->cantidad,
        //                 'cajas' => floor($rotura->cantidad / $this->getUnidadeCaja($stock->entrantes->producto_id)),
        //                 'tipo' => 'Rotura',
        //                 'created_at' => $rotura->created_at,
        //                 'pedido_id' => '-',
        //                 'qr' => $stock->qr_id,
        //             ]);
        //         }
        //     }
        // }
      
        // // Ordenar todos los datos por created_at
        // $this->allData = $this->allData->sortBy('created_at');
        // $this->producto_lotes = $this->allData;
        // $this->filters();        

        $query = Stock::with(['entrantes', 'entrantes.salidas', 'modificaciones', 'roturas']);
        //$this->mes = 1;
        // Aplicar filtro de mes y año para las salidas
        $query->whereHas('entrantes.salidas', function ($q) {
            $q->whereYear('fecha_salida', $this->anio);
            if ($this->mes != 0) {
                $q->whereMonth('fecha_salida', $this->mes);
            }
        });


        if ($this->producto_id != 0) {
            $query->whereHas('entrantes.salidas', function ($q) {
                $q->where('producto_id', $this->producto_id);
            });
        }

        if ($this->almacen_id != 0) {
            $query->whereHas('entrantes.salidas', function ($q) {
                $q->where('almacen_origen_id', $this->almacen_id);
            });
        }

        // Obtener los resultados y procesar la data unificada
        $stocks = $query->get();
        //dd($stocks);
        $this->allData = collect([]);

        foreach ($stocks as $stock) {

            // Si stock entrantes esta vacío, continúa
            if($stock->entrantes == null) continue;

            foreach ($stock->entrantes as $stockEntrante) {
                foreach ($stock->entrantes->salidas as $salida) {
                    if(count($stock->entrantes->salidas) == 0) continue;

                    // Antes de meterlo, comprueba si el id ya está en el array, y si lo está, no lo meto.
                    if($this->allData->contains('id_salida', $salida->id)) continue;
                    $this->allData->push([
                        'id_salida' => $salida->id,
                        'interno' => $salida->stock_entrante_id,
                        'lote_id' => $stock->entrantes->lote_id,
                        'orden_numero' => $stock->entrantes->orden_numero,
                        'almacen' => $this->getAlmacen($salida->almacen_origen_id) ?? 'Almacen no asignado',
                        'producto' => $this->getProducto($salida->producto_id),
                        'fecha' => Carbon::parse($salida->fecha_salida)->format('d/m/Y'),
                        'order_date' => Carbon::parse($salida->fecha_salida)->format('Ymd'),
                        'cantidad' => $salida->cantidad_salida,
                        'cajas' => floor($salida->cantidad_salida / $this->getUnidadeCaja($salida->producto_id)),
                        'tipo' => $salida->pedido_id ? 'Venta' : 'Salida',
                        'created_at' => $salida->created_at,
                        'pedido_id' => $salida->pedido_id ?? '',
                        'qr' => $stock->qr_id,
                    ]);
                }

                foreach ($stock->modificaciones as $modificacion) {

                    // Antes de meterlo, comprueba si el id ya está en el array, y si lo está, no lo meto.
                    if($this->allData->contains('id_modificacion', $modificacion->id)) continue;

                    // Si la modificación es tipo 'Suma', no la meto
                    if($modificacion->tipo == 'Suma') continue;

                    $this->allData->push([
                        'id_modificacion' => $modificacion->id,
                        'interno' => $modificacion->stock_id,
                        'lote_id' => $stock->entrantes->lote_id,
                        'orden_numero' => $stock->entrantes->orden_numero,
                        'almacen' => $modificacion->almacen_id ? $this->getAlmacen($modificacion->almacen_id) : "Almacén no asignado.",
                        'producto' => $this->getProducto($stock->entrantes->producto_id),
                        'fecha' => Carbon::parse($modificacion->fecha)->format('d/m/Y'),
                        'order_date' => Carbon::parse($modificacion->fecha)->format('Ymd'),
                        'cantidad' => $modificacion->cantidad,
                        'cajas' => floor($modificacion->cantidad / $this->getUnidadeCaja($stock->entrantes->producto_id)),
                        'tipo' => 'Modificación',
                        'created_at' => $modificacion->created_at,
                        'pedido_id' => '-',
                        'qr' => $stock->qr_id,
                    ]);
                }

                foreach ($stock->roturas as $rotura) {

                    // Antes de meterlo, comprueba si el id ya está en el array, y si lo está, no lo meto.
                    if($this->allData->contains('id_rotura', $rotura->id)) continue;

                    $this->allData->push([
                        'id_rotura' => $rotura->id,
                        'interno' => $rotura->stock_id,
                        'lote_id' => $stock->entrantes->lote_id,
                        'orden_numero' => $stock->entrantes->orden_numero,
                        'almacen' => $rotura->almacen_id ? $this->getAlmacen($rotura->almacen_id) : "Almacén no asignado.",
                        'producto' => $this->getProducto($stock->entrantes->producto_id),
                        'fecha' => Carbon::parse($rotura->fecha)->format('d/m/Y'),
                        'order_date' => Carbon::parse($rotura->fecha)->format('Ymd'),
                        'cantidad' => $rotura->cantidad,
                        'cajas' => floor($rotura->cantidad / $this->getUnidadeCaja($stock->entrantes->producto_id)),
                        'tipo' => 'Rotura',
                        'created_at' => $rotura->created_at,
                        'pedido_id' => '-',
                        'qr' => $stock->qr_id,
                    ]);
                }
            }
        }

        $this->allData = $this->allData->sortBy('created_at');
        $this->producto_lotes = $this->allData;

    }
    
}


    public function filters()
    {
        $datos = $this->allData;

        if($this->almacen_id != null && $this->almacen_id != 0){
            $datos =  $datos->where('almacen', $this->getAlmacen($this->almacen_id));
        }

        if($this->tipo != null && $this->tipo != 0){
            $datos =  $datos->where('tipo', $this->tipo);
        }

        if($this->producto_id != 0){
            $datos =  $datos->where('producto', $this->getProducto($this->producto_id));
        }

        if($this->comercial_id != 0 && $this->comercial_id != null ) {

            $datos =  $datos->filter(function ($item) {
                $pedido = Pedido::find($item['pedido_id']);
                if($pedido == null) return false;
                $cliente_id = $pedido->cliente_id;
                $cliente = Clients::find($cliente_id);
                if($cliente == null) return false;
                $comercial_id = $cliente->comercial_id;
                return $comercial_id == $this->comercial_id;
            });
        }

        if($this->delegacion_id != -1 && $this->delegacion_id != null ) {

            $datos =  $datos->filter(function ($item) {
                $pedido = Pedido::find($item['pedido_id']);
                if($pedido == null) return false;
                $cliente_id = $pedido->cliente_id;
                $cliente = Clients::find($cliente_id);
                if($cliente == null) return false;
                $delegacion = $cliente->delegacion_COD;
                if($delegacion == null) return false;
                if($delegacion == $this->delegacion_id)
                //dd($cliente , $cliente->delegacion_COD, $this->delegacion_id);

                return $delegacion == $this->delegacion_id;
            });
        }   

        // Filtrar por mes y año si mes no es 0 (Todos)
        if($this->mes != 0) {
            $datos =  $datos->filter(function ($item) {
                $itemDate = Carbon::createFromFormat('Ymd', $item['order_date']);
                return $itemDate->month == $this->mes && $itemDate->year == $this->anio;
            });
        } else {
            // Filtrar solo por año
            $datos =  $datos->filter(function ($item) {
                $itemDate = Carbon::createFromFormat('Ymd', $item['order_date']);
                return $itemDate->year == $this->anio;
            });
        }

        $datos =  $datos->sortBy('created_at');
        $this->producto_lotes =  $datos;



    }


    public function getAlmacenId($lote)
    {
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

                $this->producto_lotes = StockRegistro::with('stockEntrante')
                ->where('motivo', 'Entrada')
                ->whereYear('created_at', $this->anio)
                ->whereMonth('created_at', $this->mes)
                ->get();

                //a productos lotes debo añadirle los stockEntrantes que no esten en StockRegistro
                $stocks = StockEntrante::whereNotIn('id', StockRegistro::where('motivo', 'Entrada')->pluck('stock_entrante_id'))->get();
                foreach ($stocks as $stock) {
                    $this->producto_lotes->push($stock);
                }

            } else {

                $this->producto_lotes = StockRegistro::with(['stockEntrante' => function ($query) {
                    $query->where('producto_id', $this->producto_seleccionado);
                }])
                ->where('motivo', 'Entrada')
                ->whereYear('created_at', $this->anio)
                ->whereMonth('created_at', $this->mes)
                ->get();

                //a productos lotes debo añadirle los stockEntrantes que no esten en StockRegistro
                $stocks = StockEntrante::whereNotIn('id', StockRegistro::where('motivo', 'Entrada')->pluck('stock_entrante_id'))->where('producto_id', $this->producto_seleccionado)->where('producto_id', $this->producto_seleccionado)->get();
                foreach ($stocks as $stock) {
                    $this->producto_lotes->push($stock);
                }
            
            }
        } else {
            if($this->producto_seleccionado == 0){

                $this->producto_lotes = StockRegistro::with('stockEntrante')
                ->where('motivo', 'Entrada')
                ->whereYear('created_at', $this->anio)
                ->whereMonth('created_at', $this->mes)
                ->get();



                $this->producto_lotes = $this->producto_lotes->filter(function ($value, $key) {
                    return $this->getAlmacenId($value->stockEntrante) == $this->almacen_id;
                });

                //a productos lotes debo añadirle los stockEntrantes que no esten en StockRegistro
                $stocks = StockEntrante::whereNotIn('id', StockRegistro::where('motivo', 'Entrada')->pluck('stock_entrante_id'))->get();

                foreach ($stocks as $stock) {
                    if($this->getAlmacenId($stock) == $this->almacen_id){
                        $this->producto_lotes->push($stock);
                    }
                }

            } else {
                $this->producto_lotes = StockRegistro::with(['stockEntrante' => function ($query) {
                    $query->where('producto_id', $this->producto_seleccionado);
                }])
                ->where('motivo', 'Entrada')
                ->whereYear('created_at', $this->anio)
                ->whereMonth('created_at', $this->mes)
                ->get();

                $this->producto_lotes = $this->producto_lotes->filter(function ($value, $key) {
                    return $this->getAlmacenId($value->stockEntrante) == $this->almacen_id;
                });

                //a productos lotes debo añadirle los stockEntrantes que no esten en StockRegistro
                $stocks = StockEntrante::whereNotIn('id', StockRegistro::where('motivo', 'Entrada')->pluck('stock_entrante_id'))->where('producto_id', $this->producto_seleccionado)->get();
                foreach ($stocks as $stock) {
                    if($this->getAlmacenId($stock) == $this->almacen_id){
                        $this->producto_lotes->push($stock);
                    }
                }
            }
        }
    }

    public function getUnidadeCaja($id)
    {
        $producto = Productos::find($id);
        if($producto == null){
            return 1;
        }
        return $producto->unidades_por_caja;
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

    public function almacen($lote)
    {
        $almacenId = Stock::where('id', $lote->stock_id)->first()->almacen_id;

        $almace = Almacen::find($almacenId);
        if(isset($almace)){
            return $almace->almacen;
        } else {
            return 'Almacen no asignado';
        }
    }

    public function updated($field)
    {
        if($field == 'almacen_id' || $field == 'producto_id' || $field == 'tipo' || $field == 'mes' || $field == 'anio' || $field == 'comercial_id' || $field == 'delegacion_id'){
            $this->mount();
            if(!$this->isEntrada){
                $this->filters();
            }
        }

        if($field == 'isEntrada' && !($this->isEntrada)){
            $this->mount();
            $this->filters();
        }else if($field == 'isEntrada' && $this->isEntrada){
            $this->mount();
            $this->filters();

        }

    }

    public function render()
    {
        return view('livewire.stock.historial');
    }
}
