<?php

namespace App\Http\Controllers;

use App\Models\Alumno;
use App\Models\Albaran;
use App\Models\Clients;
use App\Models\Empresa;
use App\Models\Cursos;
use App\Models\CursosCelebracion;
use App\Models\Facturas;
use App\Models\Pedido;
use App\Models\Presupuesto;
use App\Models\Presupuestos;
use App\Models\Productos;
use App\Models\Configuracion;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Spatie\Browsershot\Browsershot;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
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
    public function create($id)
    {

        return view('factura.create', compact('id'));

    }


    public function create2()
    {

        return view('factura.createrectificativa');

    }

    public function create1()
    {

        return view('factura.create',);

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

    public function pdf($id,$iva)
    {

        $factura = Facturas::find($id);
        $configuracion = Configuracion::where('id', 1)->first();
        if($factura != null){
            $pedido = Pedido::find($factura->pedido_id);
            $albaran =  Albaran :: where('pedido_id', $factura->pedido_id)->first();
            $cliente = Clients::find($pedido->cliente_id);
            $productosPedido = DB::table('productos_pedido')->where('pedido_id', $pedido->id)->get();

            // Preparar los datos de los productos del pedido
            $productos = [];
            foreach ($productosPedido as $productoPedido) {
                $producto = Productos::find($productoPedido->producto_pedido_id);
                if ($producto) {
                    if(!isset($producto->peso_neto_unidad) || $producto->peso_neto_unidad <=0){
                        $peso ="Peso no definido";
                    }else{
                    $peso =($producto->peso_neto_unidad * $productoPedido->unidades) /1000;
                    }
                    $productos[] = [
                        'nombre' => $producto->nombre,
                        'cantidad' => $productoPedido->unidades,
                        'precio_ud' => $productoPedido->precio_ud,
                        'precio_total' => $productoPedido->precio_total,
                        'iva' => $producto->iva,
                        'lote_id' => $productoPedido->lote_id,
                        'peso_kg' => $peso,
                    ];
                }
            }

            $datos = [
                'conIva' => $iva,
                'albaran' => $albaran,
                'factura' => $factura,
                'pedido' => $pedido,
                'cliente' => $cliente,
                'localidad_entrega' => $pedido->localidad_entrega,
                'direccion_entrega' => $pedido->direccion_entrega,
                'cod_postal_entrega' => $pedido->cod_postal_entrega,
                'provincia_entrega' => $pedido->provincia_entrega,
                'fecha' => $pedido->fecha,
                'observaciones' => $pedido->observaciones,
                'precio' => $pedido->precio,
                'descuento' => $pedido->descuento,
                'productos' => $productos,
                'configuracion' => $configuracion,
            ];

        // Se llama a la vista Liveware y se le pasa los productos. En la vista se epecifican los estilos del PDF
        $pdf = Pdf::loadView('livewire.facturas.pdf-component',$datos);
        $pdf->render();

            $totalPages = $pdf->getCanvas()->get_page_count();

            $pdf->getCanvas()->page_script(function ($pageNumber, $pageCount, $canvas, $fontMetrics) use ($totalPages) {
                $text = "Página $pageNumber de $totalPages";
                $font = $fontMetrics->getFont('Helvetica', 'normal');
                $size = 10;
                $width = $canvas->get_width();
                $canvas->text($width - 100, 15, $text, $font, $size);
            });
        return $pdf->stream();
        }else{
            return redirect('admin/facturas');
        }


    }
    public function pdfPreview($id)
    {

        $factura = Facturas::find($id);

        if($factura != null){
            $pedido = Pedido::find($factura->pedido_id);
            $albaran =  Albaran :: where('pedido_id', $factura->pedido_id)->first();
            $cliente = Clients::find($pedido->cliente_id);
            $productosPedido = DB::table('productos_pedido')->where('pedido_id', $pedido->id)->get();

            // Preparar los datos de los productos del pedido
            $productos = [];
            foreach ($productosPedido as $productoPedido) {
                $producto = Productos::find($productoPedido->producto_pedido_id);
                if ($producto) {
                    if(!isset($producto->peso_neto_unidad) || $producto->peso_neto_unidad <=0){
                        $peso ="Peso no definido";
                    }else{
                    $peso =($producto->peso_neto_unidad * $productoPedido->unidades) /1000;
                    }
                    $productos[] = [
                        'nombre' => $producto->nombre,
                        'cantidad' => $productoPedido->unidades,
                        'precio_ud' => $productoPedido->precio_ud,
                        'precio_total' => $productoPedido->precio_total,
                        'iva' => $producto->iva,
                        'lote_id' => $productoPedido->lote_id,
                        'peso_kg' => $peso,
                    ];
                }
            }
            $iva = "";
            $conIva = "";
            $localidad_entrega = $pedido->localidad_entrega;
            $direccion_entrega = $pedido->direccion_entrega;
            $cod_postal_entrega = $pedido->cod_postal_entrega;
            $provincia_entrega = $pedido->provincia_entrega;
            $fecha = $pedido->fecha;
            $observaciones = $pedido->observaciones;
            $precio = $pedido->precio;
            $descuento = $pedido->descuento;
            $datos = [
                'conIva' => $iva,
                'albaran' => $albaran,
                'factura' => $factura,
                'pedido' => $pedido,
                'cliente' => $cliente,
                'localidad_entrega' => $pedido->localidad_entrega,
                'direccion_entrega' => $pedido->direccion_entrega,
                'cod_postal_entrega' => $pedido->cod_postal_entrega,
                'provincia_entrega' => $pedido->provincia_entrega,
                'fecha' => $pedido->fecha,
                'observaciones' => $pedido->observaciones,
                'precio' => $pedido->precio,
                'descuento' => $pedido->descuento,
                'productos' => $productos,
            ];

        // Se llama a la vista Liveware y se le pasa los productos. En la vista se epecifican los estilos del PDF


        return view('livewire.facturas.pdf-component', compact('datos', 'factura',
        'conIva',
        'albaran',
        'factura',
        'pedido',
        'cliente',
        'localidad_entrega',
        'direccion_entrega',
        'cod_postal_entrega',
        'provincia_entrega',
        'fecha',
        'observaciones',
        'precio',
        'descuento',
        'productos',
    ));
        }else{
            return redirect('admin/facturas');
        }


    }

    public function certificado($id){

        // Datos a enviar al certificado
        $configuracion = Configuracion::where('id', 1)->first();
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
        $pdf = PDF::loadView('livewire.facturas.certificado-component', compact('cursoCelebracion', 'cursoFechaCelebracion', 'cursoFechaCelebracionConBarras', 'alumno', 'curso', 'configuracion', 'factura', 'presupuesto'));

        // Establece la orientación horizontal del papel
        $pdf->setPaper('A4', 'landscape');
        $pdf->render();

            $totalPages = $pdf->getCanvas()->get_page_count();

            $pdf->getCanvas()->page_script(function ($pageNumber, $pageCount, $canvas, $fontMetrics) use ($totalPages) {
                $text = "Página $pageNumber de $totalPages";
                $font = $fontMetrics->getFont('Helvetica', 'normal');
                $size = 10;
                $width = $canvas->get_width();
                $canvas->text($width - 100, 15, $text, $font, $size);
            });

        return $pdf->stream();

    }
    public function indexApi()
    {
        $data = Facturas::all();
        return response()->json($data);
    }
}
