<?php

namespace App\Http\Livewire;

use Livewire\Component;
use josemmo\Facturae\Facturae;
use josemmo\Facturae\FacturaeParty;
use josemmo\Facturae\FacturaeItem;


use josemmo\Facturae\Common\FacturaeSigner;
use App\Models\Settings;

use Illuminate\Support\Facades\Storage;
use RuntimeException;
use Carbon\Carbon;

class FacturasComponent extends Component
{
    public $serie;
    public $numberFac;
    public $fecha;
    public $name;
    public $firstSurname;
    public $lastSurname;
    public $taxNumber;
    public $address;
    public $town;
    public $province;
    public $postCode;


    public $response;
    public $indentificador;
    public $nameProducto;
    public $cantidad;
    public $precio;
    public $iva;

    public $updateMode = false;
    public $inputs = [];
    public $i = 1;

   

    public function mounasdasdt()
    {
        // Creamos la factura
        $fac = new Facturae();

        // Asignamos el número EMP2017120003 a la factura
        // Nótese que Facturae debe recibir el lote y el
        // número separados
        $fac->setNumber('EMP201712', '0003');

        // Asignamos el 01/12/2017 como fecha de la factura
        $fac->setIssueDate('2017-12-01');

        // Incluimos los datos del vendedor
        $fac->setSeller(new FacturaeParty([
        "taxNumber" => "A00000000",
        "name"      => "Perico de los Palotes S.A.",
        "address"   => "C/ Falsa, 123",
        "postCode"  => "12345",
        "town"      => "Madrid",
        "province"  => "Madrid"
        ]));

        // Incluimos los datos del comprador,
        // con finos demostrativos el comprador será
        // una persona física en vez de una empresa
        $fac->setBuyer(new FacturaeParty([
        "isLegalEntity" => false,       // Importante!
        "taxNumber"     => "00000000A",
        "name"          => "Antonio",
        "firstSurname"  => "García",
        "lastSurname"   => "Pérez",
        "address"       => "Avda. Mayor, 7",
        "postCode"      => "54321",
        "town"          => "Madrid",
        "province"      => "Madrid"
        ]));

        // Añadimos los productos a incluir en la factura
        // En este caso, probaremos con tres lámpara por
        // precio unitario de 20,14€ con 21% de IVA ya incluído
        $fac->addItem("Lámpara de pie", 20.14, 3, Facturae::TAX_IVA, 21);

        // Firmamos la factura con el certificado
        $encryptedStore = file_get_contents("ipoint.pfx");
        $fac->sign($encryptedStore, null, "06102023");

        // ... y exportarlo a un archivo
        $fac->export("salida01.xsig");
        
    }

    public function add($i)
    {
        $i = $i + 1;
        $this->i = $i;
        array_push($this->inputs ,$i);
    }

    public function remove($i)
    {
        unset($this->inputs[$i]);
    }

    private function resetInputFields(){
        $this->nameProducto = '';
        $this->cantidad = '';
    }
    public function render()
    {

        return view('livewire.facturas.facturas-component');
    }

    public function submit()
    {
        $validatedDate = $this->validate([
            'serie' => 'required',
            'numberFac' => 'required',
            'fecha' => 'required',
            'name' => 'required',
            'firstSurname' => 'required',
            'lastSurname' => 'required',
            'taxNumber' => 'required',
            'address' => 'required',
            'town' => 'required',
            'province' => 'required',
            'postCode' => 'required',
            'nameProducto.0' => 'required',
            'cantidad.0' => 'required',
            'precio.0' => 'required',
            'iva.0' => 'required',
            'nameProducto.*' => 'required',
            'cantidad.*' => 'required',
            'precio.*' => 'required',
            'iva.*' => 'required',
            ],
        );

        $fac = new Facturae();

        // Asignamos el número EMP2017120003 a la factura
        // Nótese que Facturae debe recibir el lote y el
        // número separados
        $fac->setNumber($this->serie, $this->numberFac);

        // Asignamos la fecha
        $fecha = Carbon::createFromDate($this->fecha)->format('Y-m-d');
        $fac->setIssueDate($fecha);

        $empresa = Settings::whereNull('deleted_at')->first();

        // Incluimos los datos del vendedor
        $fac->setSeller(new FacturaeParty([
            "taxNumber" => $empresa->taxNumber,
            "name"      => $empresa->name,
            "address"   => $empresa->address,
            "postCode"  => $empresa->postCode,
            "town"      => $empresa->ciudad,
            "province"  => $empresa->province
        ]));

        // Incluimos los datos del comprador,
        // con finos demostrativos el comprador será
        // una persona física en vez de una empresa
        $fac->setBuyer(new FacturaeParty([
            "isLegalEntity" => false,       // Importante!
            "taxNumber"     => $this->taxNumber,
            "name"          => $this->name,
            "firstSurname"  => $this->firstSurname,
            "lastSurname"   => $this->lastSurname,
            "address"       => $this->address,
            "postCode"      => $this->postCode,
            "town"          => $this->town,
            "province"      => $this->province
            ]));

        $listaDeProductos = [];
        foreach ($this->nameProducto as $key => $value) {
            // $linea ñ= [$this->nameProducto[$key], $this->precio[$key], $this->cantidad[$key], Facturae::TAX_IVA, $this->iva[$key]];
            // array_push($listaDeProductos, $linea);
            // $fac->addItem('0001',$this->nameProducto[$key], $this->precio[$key], $this->cantidad[$key], Facturae::TAX_IVA, $this->iva[$key]);
            // $fac->addItem(new FacturaeItem([
            //     'articleCode' => '0001',
            //     "name" => $this->nameProducto[$key],
            //     "unitPriceWithoutTax" => $this->precio[$key], // NOTA: estos descuentos y cargos se
            //     "quantity" =>  $this->cantidad[$key],                       // aplican sobre la base imponible
            //     "discounts" => [
            //     //   ["reason" => "Descuento del 20%", "rate" => 20],
            //       ["reason" => "5€ de descuento", "amount" => 5]
            //     ],
            //     // "charges" => [
            //     //   ["reason" => "Recargo del 1,30%", "rate" => 1.3]
            //     // ],
            //     "taxes" => [Facturae::TAX_IVA => $this->iva[$key]]
            // ]));
        }
        $fac->addItem(new FacturaeItem([
            "articleCode" => '0001',
            "name" => "Un producto con descuento",
            "unitPriceWithoutTax" => 500,
            "quantity" => 20,
            // NOTA: estos descuentos y cargos se
            // aplican sobre la base imponible
            "discounts" => [
            //   ["reason" => "Descuento del 20%", "rate" => 20],
              ["reason" => "5€ de descuento", "amount" => 5]
            ],
            // "charges" => [
            //   ["reason" => "Recargo del 1,30%", "rate" => 1.3]
            // ]
            "taxes" => [Facturae::TAX_IVA => 21]
          ]));
        // $fac->addDiscount('Descuento ', 20);
        // Firmamos la factura con el certificado
        $encryptedStore = file_get_contents("ipoint.pfx");
        $fac->sign($encryptedStore, null, "06102023");

        // ... y exportarlo a un archivo
        $fac->export($this->serie.$this->numberFac.".xsig");

        $this->inputs = [];

        $this->resetInputFields();

        session()->flash('message', 'Users Created Successfully.');
    }
}
