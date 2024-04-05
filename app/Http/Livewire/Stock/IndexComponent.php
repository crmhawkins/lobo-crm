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

class IndexComponent extends Component

{

    use LivewireAlert;
    public $productos;
    public $almacen_id;
    public $almacenes;
    public $producto_seleccionado ;
    public $producto_lotes;

    public function mount()
    {
        $this->almacenes = Almacen::all();
        $this->almacen_id = auth()->user()->almacen_id;
        $this->productos = Productos::all();
        $this->producto_seleccionado =  0;
        $this->setLotes();

    }
    public function render()
    {
        return view('livewire.stock.index-component', [
            'productos' => $this->productos,
        ]);
    }

    public function setLotes()
    {
        if($this->almacen_id == null){
            if($this->producto_seleccionado == 0){
                $this->producto_lotes = StockEntrante::where('cantidad','>', 0)->get();
            }else{
                $this->producto_lotes = StockEntrante::where('producto_id', $this->producto_seleccionado)
                ->where('cantidad','>', 0)
                ->get();
            }
        }else{
            if($this->producto_seleccionado == 0){

                $entradas_almacen = Stock::where('almacen_id', $this->almacen_id)->get()->pluck('id');
                $this->producto_lotes = StockEntrante::whereIn('stock_id', $entradas_almacen)
                ->where('cantidad','>', 0)
                ->get();
            }else{
                $entradas_almacen = Stock::where('almacen_id', $this->almacen_id)->get()->pluck('id');
                $this->producto_lotes = StockEntrante::where('producto_id', $this->producto_seleccionado)
                ->whereIn('stock_id', $entradas_almacen)
                ->where('cantidad','>', 0)
                ->get();
            }

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
            'timerProgressBar' => false,
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
