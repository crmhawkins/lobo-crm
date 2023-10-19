<?php

namespace App\Http\Controllers;

use App\Models\Facturas;
use App\Http\Requests\StoreFacturasRequest;
use App\Http\Requests\UpdateFacturasRequest;
use josemmo\Facturae\Facturae;
use josemmo\Facturae\FacturaeParty;
use josemmo\Facturae\FacturaeItem;
use josemmo\Facturae\Common\FacturaeSigner;
use App\Models\Settings;
use App\Models\ConceptosFactura;
use App\Models\Productos;
use App\Models\Clients;


use Illuminate\Support\Facades\Storage;
use RuntimeException;
use Carbon\Carbon;
use PDF;

class FacturasController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        return view('facturas.index');

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
        return view('facturas.create');

    }



    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreFacturasRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreFacturasRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Facturas  $facturas
     * @return \Illuminate\Http\Response
     */
    public function electronica($id)
    {


        $empresa = Settings::all();
        $factura = Facturas::where('id', $id)->first();
        $cliente = Clients::where('id', $factura->id_cliente)->first();
        $conceptos = ConceptosFactura::where('id_factura', $factura->id)->get();

        foreach ($conceptos as $key => $concepto) {
            $producto = Productos::where('id', $concepto->id_producto)->first();
            $concepto['producto'] = $producto;
        }



        $fac = new Facturae();

        // Asignamos el número EMP2017120003 a la factura
        // Nótese que Facturae debe recibir el lote y el
        // número separados
        $fac->setNumber($factura->numeroFactura,$factura->serie);

        // Asignamos la fecha
        $fecha = Carbon::createFromDate($factura->fecha)->format('Y-m-d');
        $fac->setIssueDate($fecha);

        // Incluimos los datos del vendedor
        $fac->setSeller(new FacturaeParty([
            "taxNumber" => $empresa[0]->taxNumber,
            "name"      => $empresa[0]->name,
            "address"   => $empresa[0]->adress,
            "postCode"  => $empresa[0]->postCode,
            "town"      => $empresa[0]->ciudad,
            "province"  => $empresa[0]->province
        ]));

        // Incluimos los datos del comprador,
        // con finos demostrativos el comprador será
        // una persona física en vez de una empresa
        if($cliente->tipoCliente == 1){
            $fac->setBuyer(new FacturaeParty([
                "isLegalEntity" => false,       // Importante!
                "taxNumber"     => $cliente->dni,
                "name"          => $cliente->nameCliente,
                "firstSurname"  => $cliente->firstSurname,
                "lastSurname"   => $cliente->lastSurname,
                "address"       => $cliente->adressCliente,
                "postCode"      => $cliente->postCodeCliente,
                "town"          => $cliente->ciudadCliente,
                "province"      => $cliente->provinceCliente
            ]));
        }else {
            $fac->setBuyer(new FacturaeParty([
                "isLegalEntity" => true,       // Importante!
                "taxNumber"     => $cliente->taxNumber,
                "name"          => $cliente->nameEmpresa,
                "address"       => $cliente->address,
                "postCode"      => $cliente->postCode,
                "town"          => $cliente->ciudad,
                "province"      => $cliente->province,
            ]));
        }
        foreach ($conceptos as $key => $concepto) {
            if ($concepto->descuento > 0) {
                $fac->addItem(new FacturaeItem([
                    "articleCode" => $concepto->producto->cod_producto,
                    "name" => $concepto->producto->nombre,
                    "unitPriceWithoutTax" => $concepto->precio,
                    "quantity" => $concepto->cantidad,
                    // NOTA: estos descuentos y cargos se
                    // aplican sobre la base imponible
                    "discounts" => [
                        //   ["reason" => "Descuento del 20%", "rate" => 20],
                          ["reason" => "Descuento", "amount" => $concepto->descuento]
                    ],
                        # code.
                    // "charges" => [
                    //   ["reason" => "Recargo del 1,30%", "rate" => 1.3]
                    // ]
                    "taxes" => [Facturae::TAX_IVA => $concepto->iva]
                ]));
            }else {
                $fac->addItem(new FacturaeItem([
                    "articleCode" => $concepto->producto->cod_producto,
                    "name" => $concepto->producto->nombre,
                    "unitPriceWithoutTax" => $concepto->precio,
                    "quantity" => $concepto->cantidad,
                    // NOTA: estos descuentos y cargos se
                    // aplican sobre la base imponible
                    // "discounts" => [
                    //     //   ["reason" => "Descuento del 20%", "rate" => 20],
                    //       ["reason" => "Descuento", "amount" => $concepto->descuento]
                    // ],
                        # code.
                    // "charges" => [
                    //   ["reason" => "Recargo del 1,30%", "rate" => 1.3]
                    // ]
                    "taxes" => [Facturae::TAX_IVA => $concepto->iva]
                ]));
            }

        }




        // $fac->addDiscount('Descuento ', 20);
        // Firmamos la factura con el certificado
        $encryptedStore = file_get_contents("ipoint.pfx");
        $fac->sign($encryptedStore, null, "06102023");

        // ... y exportarlo a un archivo
        $fac->export($factura->numeroFactura.$factura->serie.".xsig");

        $myFile = public_path($factura->numeroFactura.$factura->serie.".xsig");
        // dd($myFile);
        $headers = 'Content-Type: application/xsig';
        // return response()->download(public_path($factura->numeroFactura.$factura->serie.".xsig"));
        return Storage::disk('public_local')->download($factura->numeroFactura.$factura->serie.".xsig");


        return view('facturas.electronica',compact('id'));

    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Facturas  $facturas
     * @return \Illuminate\Http\Response
     */
    public function show(Facturas $facturas)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Facturas  $facturas
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        return view('facturas.edit', compact('id'));

    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateFacturasRequest  $request
     * @param  \App\Models\Facturas  $facturas
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateFacturasRequest $request, Facturas $facturas)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Facturas  $facturas
     * @return \Illuminate\Http\Response
     */
    public function destroy(Facturas $facturas)
    {
        //
    }


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function generar()
    {
        //
        return view('facturas.index');

    }

    public function pdf($id)
    {

        $data = "Me llamo Ignacio Félix";
        $empresa = Settings::all();
        $factura = Facturas::where('id', $id)->first();
        $cliente = Clients::where('id', $factura->id_cliente)->first();
        $conceptos2 = ConceptosFactura::where('id_factura', $factura->id)->get();

        foreach ($conceptos2 as $key => $concepto) {
            $producto = Productos::where('id', $concepto->id_producto)->first();
            $concepto['producto'] = $producto;
        }
        // Se llama a la vista Liveware y se le pasa los productos. En la vista se epecifican los estilos del PDF
        $pdf = PDF::loadView('livewire.facturas.pdf-component', compact('empresa', 'factura', 'cliente', 'conceptos2', 'data'));
        return $pdf->stream();

    }
}
