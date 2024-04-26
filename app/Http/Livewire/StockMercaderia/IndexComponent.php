<?php

namespace App\Http\Livewire\StockMercaderia;

use App\Models\Mercaderia;
use App\Models\Settings;
use Carbon\Carbon;
use Livewire\Component;
use App\Models\StockMercaderia;
use App\Models\StockMercaderiaEntrante;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Barryvdh\DomPDF\Facade\Pdf;
use Jantinnerezo\LivewireAlert\LivewireAlert;

class IndexComponent extends Component

{
    use LivewireAlert;
    public $mercaderia;
    public $producto_seleccionado;
    public $producto_lotes;

    public function mount()
    {
        $this->mercaderia = Mercaderia::all();
    }
    public function render()
    {
        return view('livewire.stock-mercaderia.index-component', [
            'mercaderia' => $this->mercaderia,
        ]);
    }

    public function getCantidad($id)
    {
        return StockMercaderiaEntrante::where('mercaderia_id', $id)->get()->sum('cantidad');
    }


    public function generarQRIndividual($mercaderia)
    {
        $id = $mercaderia['id'];

        $stock = StockMercaderiaEntrante::where('mercaderia_id', $id)->first();
        if(isset( $stock)){
            $stock_id = $stock->stock_id;
            $codigo = StockMercaderia::where('id', $stock_id)->orderBy('created_at', 'desc')->first()->qr_id;}
            else{
                $count_qrs = Settings::find(1)->qr_creados_mercaderia;
                $year = Carbon::now()->format('y');
                $qr_type = 'm';
                $codigo = $year . '-' . $qr_type . "-" . sprintf('%08d', $count_qrs );
                $new_count = Settings::find(1)->update(['qr_creados_mercaderia' => ($count_qrs + 1)]);
            }

        if(isset($codigo)){
            $Qrcode= QrCode::errorCorrection('H')->format('png')->eye('circle')->size('500')->merge('/public/assets/images/lobo-qr.png')->errorCorrection('H')->generate($codigo);
            $pdf = PDF::loadView('stock-mercaderia.qrindividual', compact('Qrcode'))->setPaper('a4')->output();
            // Guardar el PDF generado en el almacenamiento local
            $pdfBase64 = base64_encode($pdf);

           // Suponiendo que quieres incluir el ID en el nombre del archivo
            $nombreArchivo = "QR_" . $mercaderia['nombre'] . ".pdf";

            // Enviar el PDF en Base64 al frontend junto con el nombre del archivo
            $this->dispatchBrowserEvent('downloadPdfBase64', ['pdfBase64' => $pdfBase64, 'nombreArchivo' => $nombreArchivo]);
        }else{
            return;
        }

    }

    public function getListeners()
    {
        return [
            'confirmed',
            'submit',
            'setLotes'
        ];
    }
    public function formatFecha($fecha)
    {
        return Carbon::parse($fecha)->format('d/m/Y');
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
    public function stockIniciado($id)
    {
        $existe = StockMercaderiaEntrante::where('mercaderia_id', $id)->get();
        return !$existe->isEmpty();
    }
    public function confirmed()
    {
        // Do something
        return redirect()->route('stock-mercaderia.crear-qr');
    }
}
