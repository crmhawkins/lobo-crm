<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\Stock;
use App\Models\StockEntrante;

use Illuminate\Support\Facades\View;
use Illuminate\Support\Str;
use App\Models\Settings;
use Carbon\Carbon;

class StockController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $response = '';
        return view('stock.index', compact('response'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create($id)
    {
        return view('stock.create', compact('id'));
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

    public function historial()
    {


        return view('stock.historial' );
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        return view('stock.edit', compact('id'));
    }
    public function traspaso($id)
    {
        return view('stock.traspaso', compact('id'));
    }
    public function crearQR()
    {
        $count_qrs = Settings::find(1)->qr_creados_productos;
        $year = Carbon::now()->format('y');
        $qr_type = 'p';

        // Generar códigos QR
        $qrcodes = [];
        $codigos=[];
        for ($i = 0; $i < 4; $i++) { // Ajusta este número según la cantidad que desees
            $codigoAleatorio = $year . '-' . $qr_type . "-" . sprintf('%08d', $count_qrs + $i);
            $qrcodes[] = QrCode::errorCorrection('H')->format('png')->eye('circle')->size('300')->merge('/public/assets/images/lobo-qr.png')->errorCorrection('H')->generate($codigoAleatorio);
            $codigos[] = $codigoAleatorio;
        }
        $new_count = Settings::find(1)->update(['qr_creados_productos' => ($count_qrs + 4)]);


        // Generar y transmitir PDF
        $pdf = PDF::loadView('stock.qrcodes', compact('qrcodes','codigos'))->setPaper('a4');
        return $pdf->stream('qrcodes.pdf');
    }

    public function generarQRIndividual($id)
    {
        $stock_id = StockEntrante::where('id', $id)->first()->stock_id;
        $codigo = Stock::where('id', $stock_id)->orderBy('created_at', 'desc')->first()->qr_id;
        if(isset($codigo)){
        $Qrcode= QrCode::errorCorrection('H')->format('png')->eye('circle')->size('300')->merge('/public/assets/images/lobo-qr.png')->errorCorrection('H')->generate($codigo);
        $pdf = PDF::loadView('stock.qrindividual', compact('Qrcode'))->setPaper('a4');

        return $pdf->stream('qrindividual.pdf');

    }else{
            return;
        }
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
        //
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

}
