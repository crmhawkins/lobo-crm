<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use App\Models\Mercaderia;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\StockMercaderia;
use App\Models\StockMercaderiaEntrante;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Str;
use App\Models\Settings;
use Carbon\Carbon;

class StockMercaderiaController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $response = '';
        return view('stock-mercaderia.index', compact('response'));
    }

    public function crearQR()
    {
        $count_qrs = Settings::find(1)->qr_creados_mercaderia;
        $year = Carbon::now()->format('y');
        $qr_type = 'm';

        $qrcodes = [];
        for ($i = 1; $i <= 6; $i++) { // Ajusta este número según la cantidad que desees
            $codigoAleatorio = $year . '-' . $qr_type . "-" . sprintf('%08d', $count_qrs + $i);
            $qrcodes[] = QrCode::errorCorrection('H')->format('png')->size('300')->merge('/public/assets/images/lobo-qr.png')->errorCorrection('H')->generate(route('stock-mercaderia.create', ['id' => $codigoAleatorio]));
        }
        $new_count = Settings::find(1)->update(['qr_creados_mercaderia' => ($count_qrs + 6)]);


        // Generar y transmitir PDF
        $pdf = PDF::loadView('stock-mercaderia.qrcodes', compact('qrcodes'))->setPaper('a4');
        return $pdf->stream('qrcodes.pdf');
    }
    public function generarQRIndividual($id)
    {
        $stock_id = StockMercaderiaEntrante::where('mercaderia_id', $id)->first()->stock_id;
        $codigo = StockMercaderia::where('id', $stock_id)->orderBy('created_at', 'desc')->first()->qr_id;
        if(isset($codigo)){
        $Qrcode= QrCode::errorCorrection('H')->format('png')->eye('circle')->size('300')->merge('/public/assets/images/lobo-qr.png')->errorCorrection('H')->generate($codigo);
        $pdf = PDF::loadView('stock-mercaderia.qrindividual', compact('Qrcode'))->setPaper('a4');
        return $pdf->stream('qrindividual.pdf');

        }else{
            return;
        }

    }
    public function mostrarQR()
    {
        $response = '';
        return view('stock-mercaderia.mostrar-qr', compact('response'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create($id)
    {
        return view('stock-mercaderia.create', compact('id'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        return 'CREADO';
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        return view('stock-mercaderia.edit', compact('id'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
    public function indexApi()
    {
        $data = Mercaderia::all();
        return response()->json($data);
    }
}
