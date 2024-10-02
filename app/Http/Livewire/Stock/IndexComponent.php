<?php

namespace App\Http\Livewire\Stock;

use App\Models\ProductoLote;
use Carbon\Carbon;
use Livewire\Component;
use App\Models\Productos;
use App\Models\StockEntrante;
use App\Models\Stock;
use App\Models\Almacen;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Barryvdh\DomPDF\Facade\Pdf;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use App\Models\StockSaliente;
use App\Models\StockRegistro;

class IndexComponent extends Component

{
    use LivewireAlert;
    public $productos;
    public $almacen_id;
    public $almacenes;
    public $producto_seleccionado = 0 ;
    public $producto_lotes;
    public $productos_lotes_salientes;

    public function mount()
    {
        // Recuperar filtros desde la sesión si existen
        $this->almacen_id = session('stock_filtro_almacen_id', auth()->user()->almacen_id);
        $this->producto_seleccionado = session('stock_filtro_producto_seleccionado', 0);
    
        $this->almacenes = Almacen::all();
        $this->productos = Productos::all();
    
        $this->setLotes();
    }
    public function render()
    {
        return view('livewire.stock.index-component', [
            'productos' => $this->productos,
        ]);
    }
    public function getProducto($id)
    {
        $producto = $this->productos->find($id);
        if($producto == null){
            return 'Producto no encontrado';
        }
        return $this->productos->find($id)->nombre;
    }

    public function setLotes()
    {
        // Guardar los filtros en la sesión
        session([
            'stock_filtro_almacen_id' => $this->almacen_id,
            'stock_filtro_producto_seleccionado' => $this->producto_seleccionado,
        ]);

        if ($this->almacen_id == null) {
            if ($this->producto_seleccionado == 0) {

                $this->producto_lotes = StockEntrante::where('cantidad', '>', 0)->get();

                foreach ($this->producto_lotes as $lote) {
                    $stockRegistro = StockRegistro::where('stock_entrante_id', $lote->id)->sum('cantidad');

                    if (($lote->cantidad - $stockRegistro) <= 0) {
                        $this->producto_lotes = $this->producto_lotes->where('id', '!=', $lote->id);
                    } else {
                        $lote->cantidad = $lote->cantidad - $stockRegistro;
                    }
                }

                $this->productos_lotes_salientes = StockSaliente::where('cantidad_salida', '>', 0)->get();

            } else {
                $this->producto_lotes = StockEntrante::where('producto_id', $this->producto_seleccionado)
                    ->where('cantidad', '>', 0)
                    ->get();

                foreach ($this->producto_lotes as $lote) {
                    $stockRegistro = StockRegistro::where('stock_entrante_id', $lote->id)->sum('cantidad');

                    if (($lote->cantidad - $stockRegistro) <= 0) {
                        $this->producto_lotes = $this->producto_lotes->where('id', '!=', $lote->id);
                    } else {
                        $lote->cantidad = $lote->cantidad - $stockRegistro;
                    }
                }

                $this->productos_lotes_salientes = StockSaliente::where('producto_id', $this->producto_seleccionado)
                    ->where('cantidad_salida', '>', 0)->get();
            }
        } else {
            if ($this->producto_seleccionado == 0) {
                $entradas_almacen = Stock::where('almacen_id', $this->almacen_id)->get()->pluck('id');
                $this->producto_lotes = StockEntrante::whereIn('stock_id', $entradas_almacen)
                    ->where('cantidad', '>', 0)
                    ->get();

                foreach ($this->producto_lotes as $lote) {
                    $stockRegistro = StockRegistro::where('stock_entrante_id', $lote->id)->sum('cantidad');

                    if (($lote->cantidad - $stockRegistro) <= 0) {
                        $this->producto_lotes = $this->producto_lotes->where('id', '!=', $lote->id);
                    } else {
                        $lote->cantidad = $lote->cantidad - $stockRegistro;
                    }
                }

                $this->productos_lotes_salientes = StockSaliente::whereIn('stock_entrante_id', $this->producto_lotes->pluck('id'))
                    ->where('cantidad_salida', '>', 0)->get();

            } else {
                $entradas_almacen = Stock::where('almacen_id', $this->almacen_id)->get()->pluck('id');
                $this->producto_lotes = StockEntrante::where('producto_id', $this->producto_seleccionado)
                    ->whereIn('stock_id', $entradas_almacen)
                    ->where('cantidad', '>', 0)
                    ->get();

                foreach ($this->producto_lotes as $lote) {
                    $stockRegistro = StockRegistro::where('stock_entrante_id', $lote->id)->sum('cantidad');

                    if (($lote->cantidad - $stockRegistro) <= 0) {
                        $this->producto_lotes = $this->producto_lotes->where('id', '!=', $lote->id);
                    } else {
                        $lote->cantidad = $lote->cantidad - $stockRegistro;
                    }
                }

                $this->productos_lotes_salientes = StockSaliente::where('producto_id', $this->producto_seleccionado)
                    ->whereIn('stock_entrante_id', $this->producto_lotes->pluck('id'))
                    ->where('cantidad_salida', '>', 0)->get();
            }
        }
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
    public function qrAsignado($lote){
        $id = $lote['id'];
        $stock = StockEntrante::where('id', $id)->first();
        if(isset( $stock)){
            $stock_id = $stock->stock_id;
            $codigo = Stock::where('id', $stock_id)->orderBy('created_at', 'desc')->first()->qr_id;
        }
        if(isset($codigo) && $codigo != ''){
            return true;
        }else{
            return false;
        }

    }


    public function asignarQr($datos){
        $qr_id = $datos['qrData'];
        $lote = $datos['lote'];
        $id = $lote['id'];
        $qrenuso = Stock::where('qr_id', $qr_id)->first();
        if (isset($qrenuso)){
            $this->alert('error', '¡El qr ya esta asignado!', [
                'position' => 'center',
                'timer' => 3000,
                'toast' => false,
            ]);
            return;
        }
        $stock = StockEntrante::where('id', $id)->first();
        if(isset( $stock)){
            $stock_id = $stock->stock_id;
            $Stockqr = Stock::where('id', $stock_id)->orderBy('created_at', 'desc')->first();
        }
        if(isset($Stockqr)){
            $Qrasignado = $Stockqr->update([
                'qr_id' => $qr_id,
            ]);       }
        if($Qrasignado){
            $this->alert('success', '¡Qr signado correctamente!', [
                'position' => 'center',
                'timer' => 3000,
                'toast' => false,
                'showConfirmButton' => true,
                'confirmButtonText' => 'ok',
                'timerProgressBar' => true,
            ]);
        } else {
            $this->alert('error', '¡No se ha podido asignar el qr!', [
                'position' => 'center',
                'timer' => 3000,
                'toast' => false,
            ]);
        }

    }


    public function imprimirSaliente(){
    
        $arrayProductosLotes = [];
        //dd($this->productos_lotes_salientes);
        
        $allData = collect([]);

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
                    if($allData->contains('id_salida', $salida->id)) continue;
                    //dd($salida);
                    $allData->push([
                        'id_salida' => $salida->id,
                        'interno' => $salida->stock_entrante_id,
                        'lote_id' => $stock->entrantes->lote_id,
                        'orden_numero' => $stock->entrantes->orden_numero,
                        'almacen' => $this->getAlmacen($salida->almacen_origen_id) ?? 'Almacen no asignado',
                        'producto' => $this->getProducto($salida->producto_id),
                        'fecha' => Carbon::parse($salida->fecha_salida)->format('d/m/Y'),
                        'cantidad' => $salida->cantidad_salida,
                        'cajas' => floor($salida->cantidad_salida / $this->getUnidadeCaja($salida->producto_id)),
                        'tipo' => $salida->pedido_id ? 'Venta' : 'Salida',
                        'created_at' => $salida->created_at,
                        'pedido_id' => $salida->pedido_id ?? '',
                    ]);
                }
                foreach ($stock->modificaciones as $modificacion) {
                    //dd($stock->entrantes);


                    //antes de meterlo comprueba si el id ya está en el array, y si lo esta no lo meto.
                    if($allData->contains('id_modificacion', $modificacion->id)) continue;
                    //si la modificacion es tipo 'Suma' no la meto
                    if($modificacion->tipo == 'Suma') continue;
                    $allData->push([
                        'id_modificacion' => $modificacion->id,
                        'interno' => $modificacion->stock_id,
                        'lote_id' => $stock->entrantes->lote_id,
                        'orden_numero' => $stock->entrantes->orden_numero, // No hay orden asociada a modificaciones
                        'almacen' => $modificacion->almacen_id ? $this->getAlmacen($modificacion->almacen_id) : "Almacén no asignado.", // Ajustar según tu lógica de almacenamiento
                        'producto' => $this->getProducto($stock->entrantes->producto_id),
                        'fecha' => Carbon::parse($modificacion->fecha)->format('d/m/Y'),
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
                    if($allData->contains('id_rotura', $rotura->id)) continue;
                    $allData->push([
                        'id_rotura' => $rotura->id,
                        'interno' => $rotura->stock_id,
                        'lote_id' => $stock->entrantes->lote_id,
                        'orden_numero' => $stock->entrantes->orden_numero, // No hay orden asociada a roturas
                        'almacen' => $rotura->almacen_id ? $this->getAlmacen($rotura->almacen_id) : "Almacén no asignado.", // Ajustar según tu lógica de almacenamiento
                        'producto' => $this->getProducto($stock->entrantes->producto_id),
                        'fecha' => Carbon::parse($rotura->fecha)->format('d/m/Y'),
                        'cantidad' => $rotura->cantidad,
                        'cajas' => floor($rotura->cantidad / $this->getUnidadeCaja($stock->entrantes->producto_id)),
                        'tipo' => 'Rotura',
                        'created_at' => $rotura->created_at,
                        'pedido_id' => '-', // No hay pedido asociado a roturas
                    ]);
                }    
            }
        }


        // Ordenar todos los datos por created_at
        $allData = $allData->sortBy('created_at');
        //dd($allData);
        

        $datos = [
            'producto_lotes' =>  $allData,
            'tipo' => 'Saliente',
        ];
        $pdf = PDF::loadView('livewire.stock.pdf-stock', $datos)->setPaper('a4', 'vertical')->output();
        return response()->streamDownload(
            fn () => print($pdf),
            'historial_stock_Saliente.pdf'
        );

    
    }


    public function imprimirEntrante(){


        $arrayProductosLotes = [];
        foreach ($this->producto_lotes as $loteIndex => $lote) {
            $arrayProductosLotes[] = [
                'lote_id' => $lote['lote_id'],
                'orden_numero' => $lote['orden_numero'],
                'almacen' => $this->almacen($lote),
                'producto' => $this->getProducto($lote['producto_id']),
                'fecha' => $this->formatFecha($lote['stock_id']),
                'cantidad' => $lote['cantidad'],
                'cajas' => floor($lote['cantidad']/ $this->getUnidadeCaja($lote['producto_id']) ),
            ];

        }

        $datos = [
            'producto_lotes' => $arrayProductosLotes,
        ];


        $pdf = PDF::loadView('livewire.stock.pdf-stock', $datos)->setPaper('a4', 'vertical')->output(); 
        return response()->streamDownload(
            fn () => print($pdf),
            'historial_stock_Entrante.pdf'
        );

    }

    public function editar($qr)
    {
        $stock = Stock::where('qr_id', $qr)->first();
        $stockentrante = StockEntrante::where('stock_id', $stock->id)->first();
        return redirect()->route('stock.edit' ,$stockentrante->id);
    }

    public function anadir($qr)
    {
        $stock = Stock::where('qr_id', $qr)->first();
        if (isset($stock)){
            $this->alert('error', '¡El qr ya esta asignado!', [
                'position' => 'center',
                'timer' => 3000,
                'toast' => false,
            ]);

        }else{

            return redirect()->route('stock.create' ,$qr);

        }

    }
    public function generarQRIndividual($lote)
    {
        $id = $lote['id'];
        $stock = StockEntrante::where('id', $id)->first();
        if(isset( $stock)){
            $stock_id = $stock->stock_id;
            $codigo = Stock::where('id', $stock_id)->orderBy('created_at', 'desc')->first()->qr_id;
        }

        if(isset($codigo)){
            $Qrcode= QrCode::errorCorrection('H')->format('png')->eye('circle')->size('500')->merge('/public/assets/images/lobo-qr.png')->errorCorrection('H')->generate($codigo);
            $pdf = PDF::loadView('stock.qrindividual', compact('Qrcode','codigo'))->setPaper('a4')->output();
            // Guardar el PDF generado en el almacenamiento local
            $pdfBase64 = base64_encode($pdf);

          // Suponiendo que quieres incluir el ID en el nombre del archivo
          $nombreArchivo = "QR_Orden_" . $lote['orden_numero'] . ".pdf";

          // Enviar el PDF en Base64 al frontend junto con el nombre del archivo
          $this->dispatchBrowserEvent('downloadPdfBase64', ['pdfBase64' => $pdfBase64, 'nombreArchivo' => $nombreArchivo]);
        }else{
            return;
        }
    }

    public function borrar($lote)
    {
        $id = $lote['id'];
        $stock = StockEntrante::where('id', $id)->first();
        if(isset( $stock)){
            $stock_id = $stock->stock_id;
            $stockqr = Stock::where('id', $stock_id)->orderBy('created_at', 'desc')->first();
        }

        if(isset($stockqr)){

            $qrborrado=$stockqr->update(['qr_id' => null]);
            if( $qrborrado)
            {
                $mensaje="Qr eliminado correctamente.\n";

            }else{ $mensaje="No se pudo eliminar el Qr.\n";}



        $this->alert('info', $mensaje, [
            'position' => 'center',
            'timer' => null,
            'toast' => false,
            'showConfirmButton' => true,
            'allowOutsideClick'=> false,

            'onConfirmed' => 'recarga',
            'confirmButtonText' => 'Entendido',
        ]);

        }else{
            return;
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

    public function getListeners()
    {
        return [
            'confirmed',
            'submit',
            'setLotes',
            'asignarQr',
            'editar',
            'generarQRIndividual',
            'anadir',
            'borrar',
        ];
    }
    public function formatFecha($id)
    {
        return Carbon::parse(Stock::find($id)->fecha)->format('d/m/Y');
    }
    public function alertaGuardar()
    {
        $this->alert('warning', '¿Estás seguro? Comprueba que se han usado todos los códigos QR, o que los códigos por generar sean necesarios.', [
            'position' => 'center',
            'toast' => false,
            'showConfirmButton' => true,
            'onConfirmed' => 'confirmed',
            'confirmButtonText' => 'Sí',
            'showDenyButton' => true,
            'denyButtonText' => 'No',
            'timer' => null,
            //que el alert no se cierre

        ]);
    }
    public function confirmed()
    {
        // Do something
        return redirect()->route('stock.crear-qr');
    }
    public function recarga()
    {
        return redirect()->route('stock.index');
    }
   
}
