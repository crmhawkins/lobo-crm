<?php

namespace App\Http\Controllers;

use App\Models\Albaran;
use App\Models\Clients;
use App\Models\Pedido;
use App\Models\Productos;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AlmacenController extends Controller
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

        return view('almacen.index', compact('response'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create($id)
    {
        return view('almacen.create', compact('id'));
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
        return view('almacen.edit', compact('id'));
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

        $albaran = Albaran::where('id', $id)->first();
        if ($albaran != null) {
            $pedido = Pedido::where('id', $albaran->pedido_id)->first();
            if($pedido->tipo_pedido_id == 0 || $pedido->tipo_pedido_id == 1){
                $productos = Productos::all();
            $cliente = Clients::where('id', $pedido->cliente_id)->first();
            $productos_pedido = DB::table('productos_pedido')->where('pedido_id', $albaran->pedido_id)->get();
            $base_imponible = 0;
            foreach ($productos_pedido as $producto) {
                $base_imponible += ($producto->precio_ud * $producto->unidades);
            }

            // Se llama a la vista Liveware y se le pasa los productos. En la vista se epecifican los estilos del PDF
            $pdf = Pdf::loadView('livewire.almacen.pdf-component', compact('albaran', 'productos_pedido', 'base_imponible', 'pedido', "productos", "cliente"));
            return $pdf->stream();
            }else{
                $productos = Productos::all();
            $cliente = Clients::where('id', $pedido->cliente_id)->first();
            $productos_pedido = DB::table('productos_pedido')->where('pedido_id', $albaran->pedido_id)->get();
            $base_imponible = 0;
            foreach ($productos_pedido as $producto) {
                $base_imponible += ($producto->precio_ud * $producto->unidades);
            }

            // Se llama a la vista Liveware y se le pasa los productos. En la vista se epecifican los estilos del PDF
            $pdf = Pdf::loadView('livewire.almacen.ticket-component', compact('albaran', 'productos_pedido', 'base_imponible', 'pedido', "productos", "cliente"));
            return $pdf->stream();
            }

        } else {
            return redirect('admin/almacen');
        }
    }
}
