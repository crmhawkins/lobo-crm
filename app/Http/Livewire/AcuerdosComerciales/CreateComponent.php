<?php

namespace App\Http\Livewire\AcuerdosComerciales;

use Livewire\Component;
use Jantinnerezo\LivewireAlert\LivewireAlert;

use App\Models\ClientesComercial;
use App\Models\acuerdosComerciales;
use Illuminate\Support\Facades\Auth;

use Carbon\Carbon;



class CreateComponent extends Component
{

    use LivewireAlert;
    public $user;
    public $cliente;
    public $numeroAcuerdoComercial;
    public $mesActual;
    public $identificador;
    public $nombre;
    public $dni;
    public $domicilio;
    public $email;
    public $telefono;
    public $establecimientos;
    public $domicilios_establecimientos;
    public $fecha_inicio;
    public $fecha_fin;
    public $productos_lobo = [];
    public $productos_otros = [];
    public $marketing;
    public $observaciones;
    public $dia_firma;
    public $mes_firma;
    public $anio_firma;

    public $firmaComercialLobo;
    public $firmaComercial;
    public $firmaCliente;
    public $firmaDistribuidor;
    public $firma_comercial_lobo_data;
    public $firma_comercial_data;
    public $firma_cliente_data;
    public $firma_distribuidor_data;


    public function mount()
{
    $this->user = Auth::user();
    $this->cliente = ClientesComercial::findOrFail($this->identificador)->toArray();
    // $this->dni = $this->cliente['cif'];

    // Inicializar los arrays de productos
    $this->productos_lobo = [
        ['ref' => '', 'vol_anual' => '', 'aportacion_directa' => '', 'rappel' => '', 'total' => '']
    ];

    $this->productos_otros = [
        ['ref' => '', 'vol_anual' => '', 'aportacion_directa' => '', 'rappel' => '', 'total' => '']
    ];

    // Obtener el último acuerdo comercial
    $ultimoAcuerdoComercial = acuerdosComerciales::where('nAcuerdo', 'like', '%/'.date('Y'))
        ->orderBy('id', 'desc')
        ->first();

    // Calcular el número del acuerdo comercial basado en el último acuerdo existente
    if (!$ultimoAcuerdoComercial) {
        $this->numeroAcuerdoComercial = '01/' . date('Y');
    } else {
        // Extraer el número del acuerdo actual y sumarle 1
        $ultimoNumeroAcuerdo = explode('/', $ultimoAcuerdoComercial->nAcuerdo)[0]; // Obtener el número antes de la barra
        $nuevoNumeroAcuerdo = str_pad(intval($ultimoNumeroAcuerdo) + 1, 2, '0', STR_PAD_LEFT); // Incrementar y pad con ceros
        $this->numeroAcuerdoComercial = $nuevoNumeroAcuerdo . '/' . date('Y');
    }

    $this->nAcuerdo = $this->numeroAcuerdoComercial;

    // Traducir el mes al español
    $meses = ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'];
    $this->mesActual = $meses[intval(date('m')) - 1];
}

public function formarFechaFirma($dia, $mes, $anio)
{
    $meses = [
        'Enero' => 1,
        'Febrero' => 2,
        'Marzo' => 3,
        'Abril' => 4,
        'Mayo' => 5,
        'Junio' => 6,
        'Julio' => 7,
        'Agosto' => 8,
        'Septiembre' => 9,
        'Octubre' => 10,
        'Noviembre' => 11,
        'Diciembre' => 12
    ];

    if (array_key_exists($mes, $meses)) {
        $mes_numero = $meses[$mes];
    } else {
        throw new \Exception("El mes no es válido.");
    }

    $fecha = Carbon::createFromFormat('d/m/Y', $dia . '/' . $mes_numero . '/' . $anio);

    return $fecha;
}
public function addProductLobo()
{
    $this->productos_lobo[] = ['ref' => '', 'vol_anual' => '', 'aportacion_directa' => '', 'rappel' => '', 'total' => ''];
}

public function addProductOtros()
{
    $this->productos_otros[] = ['ref' => '', 'vol_anual' => '', 'aportacion_directa' => '', 'rappel' => '', 'total' => ''];
}

public function deleteProductLobo($index)
{
    unset($this->productos_lobo[$index]);
}

public function deleteProductOtros($index)
{
    unset($this->productos_otros[$index]);
}

    public function saveAcuerdoComercial()
    {   
        //dd($this->productos_lobo , $this->productos_otros);
        // Validar los datos del acuerdo comercial
        $this->validate([
            'nAcuerdo' => 'required|string',
            'fecha_inicio' => 'nullable',
            'fecha_fin' => 'nullable',
            'observaciones' => 'nullable',
            'nombre' => 'nullable',
            'dni' => 'nullable',
            'domicilio' => 'nullable',
            'telefono' => 'nullable',
            'establecimientos' => 'nullable',
            'domicilios_establecimientos' => 'nullable',
            'fecha_inicio' => 'nullable',
            'fecha_fin' => 'nullable',
            'productos_lobo' => 'nullable',
            'marketing' => 'nullable',
            'observaciones' => 'nullable',
            'dia_firma' => 'nullable',
            'mes_firma' => 'nullable',
            'anio_firma' => 'nullable',
        ]);

        $fecha_firma = $this->formarFechaFirma($this->dia_firma, $this->mes_firma, $this->anio_firma);

        // Crear el acuerdo comercial
        $acuerdoComercial = acuerdosComerciales::create([
            'user_id' => $this->user->id,
            'cliente_id' => $this->cliente['id'],
            'nAcuerdo' => $this->nAcuerdo,
            'nombre_empresa'=> $this->cliente['nombre'],
            'cif_empresa' => $this->cliente['cif'],
            'nombre' => $this->nombre,
            'dni' => $this->dni,
            'email' => $this->cliente['email'],
            'telefono' => $this->cliente['telefono'],
            'domicilio' => $this->cliente['direccion'],
            'establecimiento' => $this->establecimientos,
            'domicilio_establecimientos' => $this->domicilios_establecimientos,
            'fecha_inicio' => $this->fecha_inicio,
            'fecha_fin' => $this->fecha_fin,
            'prductos_lobo' => json_encode($this->productos_lobo), // Serializar array
            'productos_otros' => json_encode($this->productos_otros), // Serializar array
            'marketing' => $this->marketing,
            'observaciones' => $this->observaciones,
            'firma_comercial_lobo' => $this->firma_comercial_lobo_data, // Guardar la firma en base64
            'firma_comercial' => $this->firma_comercial_data, // Guardar la firma en base64
            'firma_cliente' => $this->firma_cliente_data, // Guardar la firma en base64
            'firma_distribuidor' => $this->firma_distribuidor_data, // Guardar la firma en base64,
            'fecha_firma' => $fecha_firma->format('Y-m-d'),
        ]);

        // Guardar el acuerdo comercial
        $acuerdoComercial->save();
        $this->dispatchBrowserEvent('acuerdoGuardado');

        // Alerta de éxito
        // $this->alert('success', '¡Acuerdo comercial creado con éxito!');

    }

    public function render()
    {
        return view('livewire.acuerdos-comerciales.create-component');
    }
}
