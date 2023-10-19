<?php

namespace App\Http\Controllers;

use App\Models\Alumno;
use App\Models\Clients;
use App\Models\Empresa;
use App\Models\Cursos;
use App\Models\CursosCelebracion;
use App\Models\Facturas;
use App\Models\Pedido;
use App\Models\Presupuesto;
use App\Models\Presupuestos;
use App\Models\Productos;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Spatie\Browsershot\Browsershot;
use Carbon\Carbon;
use DateTime;

class FacturaController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $response = '';
        // $user = Auth::user();

        return view('factura.index', compact('response'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('factura.create');

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
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
        return view('factura.edit', compact('id'));

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

    public function pdf($id)
    {

        $factura = Facturas::where('id', $id)->first();
        if($factura != null){
            $pedido = Pedido::where('id', $factura->pedido_id)->first();
        $productos = Productos::all();
        $cliente = Clients::where('id', $pedido->cliente_id)->first();


        // Se llama a la vista Liveware y se le pasa los productos. En la vista se epecifican los estilos del PDF
        $pdf = Pdf::loadView('livewire.facturas.pdf-component', compact('factura', 'pedido', "productos", "cliente"));
        return $pdf->stream();
        }else{
            return redirect('admin/facturas');
        }


    }

    public function certificado($id){

        // Datos a enviar al certificado
        $factura = Facturas::where('id', $id)->first();
        $presupuesto = Presupuesto::where('id', $factura->id_presupuesto)->first();
        $alumno = Alumno::where('id', $presupuesto->alumno_id)->first();
        $curso = Cursos::where('id', $presupuesto->curso_id)->first();
        $cursoCelebracion = CursosCelebracion::where('id', $curso->celebracion_id)->first();

        // Fecha del final del curso
        $date = Carbon::createFromFormat('d/m/Y', $curso->fecha_fin);
        $diaMes = $date->day;
        $nombreMes = ucfirst($date->monthName);
        $numeroMes = $date->month;
        $anioMes = $date->year;
        $cursoFechaCelebracion = $diaMes." de ".$nombreMes." de ".$anioMes;

        $cursoFechaCelebracionConBarras = $diaMes."/".$numeroMes."/".$anioMes;

        // Se llama a la vista Liveware y se le pasa los productos. En la vista se epecifican los estilos del PDF
        $pdf = PDF::loadView('livewire.facturas.certificado-component', compact('cursoCelebracion', 'cursoFechaCelebracion', 'cursoFechaCelebracionConBarras', 'alumno', 'curso'));

        // Establece la orientación horizontal del papel
        $pdf->setPaper('A4', 'landscape');

        return $pdf->stream();

    }
}
