<?php

namespace App\Http\Livewire\AcuerdosComerciales;

use Livewire\Component;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use App\Models\Clients;
use App\Models\acuerdosComerciales;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class EditComponent extends Component
{
    use LivewireAlert;

    public $acuerdo;
    public $cliente;
    public $productos_lobo = [];
    public $productos_otros = [];
    public $marketing;
    public $observaciones;
    public $dia_firma;
    public $mes_firma;
    public $anio_firma;
    public $identificador;
    public $user;
    public $nAcuerdo;
    public $mesActual;
    public $anioActual;
    public $nombre_empresa;
    public $cif_empresa;
    public $nombre;
    public $dni;
    public $domicilio;
    public $email;
    public $tel;
    public $establecimientos;
    public $domicilios_establecimientos;
    public $fecha_inicio;
    public $fecha_fin;
    
    
    public $firma_comercial_lobo_data;
    public $firma_comercial_data;
    public $firma_cliente_data;
    public $firma_distribuidor_data;
    public $fecha_firma;


    public function getListeners()
    {
        return [
           'saveAcuerdoComercial',
           'deleteAcuerdo'
        ];
    }

    public function mount($identificador)
    {
        $this->user = Auth::user();
        $this->acuerdo = acuerdosComerciales::findOrFail($this->identificador);
        $this->nAcuerdo = $this->acuerdo->nAcuerdo;

        // Cargar cliente y demás datos del acuerdo
        $this->cliente = Clients::findOrFail($this->acuerdo->cliente_id);
        $this->nombre_empresa = $this->cliente->nombre;
        $this->cif_empresa = $this->cliente->dni_cif;
        $this->nombre = $this->acuerdo->nombre;
        $this->dni = $this->acuerdo->dni;
        $this->email = $this->acuerdo->email;
        $this->tel = $this->acuerdo->telefono;
        $this->domicilio = $this->acuerdo->domicilio;
        $this->establecimientos = $this->acuerdo->establecimiento;
        $this->domicilios_establecimientos = $this->acuerdo->domicilio_establecimientos;
        $this->fecha_inicio = $this->acuerdo->fecha_inicio;
        $this->fecha_fin = $this->acuerdo->fecha_fin;
        $this->productos_lobo = json_decode($this->acuerdo->prductos_lobo, true) ?? [];
        $this->productos_otros = json_decode($this->acuerdo->productos_otros, true) ?? [];
        $this->marketing = $this->acuerdo->marketing;
        $this->observaciones = $this->acuerdo->observaciones;
        // $this->dia_firma = now()->format('d');
        // $this->mes_firma = now()->format('F');
        // $this->anio_firma = now()->format('Y');
        $this->fecha_firma = $this->acuerdo->fecha_firma;
        $this->fechaFirma($this->fecha_firma);
        

        // Inicializar las firmas si existen
        $this->firma_comercial_lobo_data = $this->acuerdo->firma_comercial_lobo;
        $this->firma_comercial_data = $this->acuerdo->firma_comercial;
        $this->firma_cliente_data = $this->acuerdo->firma_cliente;
        $this->firma_distribuidor_data = $this->acuerdo->firma_distribuidor;
    }
    public function fechaFirma($fecha_firma)
{
    if ($fecha_firma == null) {
        return;
    }

    // Si la fecha es una cadena, convertirla a un objeto Carbon
    if (!($fecha_firma instanceof Carbon)) {
        $fecha_firma = Carbon::parse($fecha_firma);
    }

    // Establecer la localización a español
    setlocale(LC_TIME, 'es_ES.UTF-8');
    Carbon::setLocale('es');

    $this->dia_firma = $fecha_firma->format('d');

    // Mapeo de meses en inglés a español
    $meses = [
        'January' => 'Enero',
        'February' => 'Febrero',
        'March' => 'Marzo',
        'April' => 'Abril',
        'May' => 'Mayo',
        'June' => 'Junio',
        'July' => 'Julio',
        'August' => 'Agosto',
        'September' => 'Septiembre',
        'October' => 'Octubre',
        'November' => 'Noviembre',
        'December' => 'Diciembre'
    ];

    // Obtener el mes en inglés
    $mes_ingles = $fecha_firma->format('F'); // 'F' devuelve el mes completo en inglés

    // Traducir el mes al español
    $this->mes_firma = $meses[$mes_ingles] ?? $mes_ingles; // Asignar el mes traducido

    $this->anio_firma = $fecha_firma->format('Y');
}

    
    public function formarFechaFirma($dia, $mes, $anio)
{
    // Crear un mapeo de los meses en español a sus correspondientes números
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

    // Convertir el nombre del mes en español a número usando el mapeo
    if (array_key_exists($mes, $meses)) {
        $mes_numero = $meses[$mes];
    } else {
        throw new \Exception("El mes no es válido.");
    }

    // Formar la fecha usando el día, mes (numérico) y año
    $fecha = Carbon::createFromFormat('d/m/Y', $dia.'/'.$mes_numero.'/'.$anio);

    return $fecha;
}

public function deleteAcuerdo()
{
    // Eliminar el acuerdo comercial
    $this->acuerdo->delete();

    // Emitir un evento para redirigir después de la eliminación
    $this->dispatchBrowserEvent('acuerdoEliminado', ['clienteId' => $this->acuerdo->cliente_id]);
}

public function confirmDelete()
{
    $this->dispatchBrowserEvent('confirmarEliminacion');
}

    public function saveAcuerdoComercial()
    {
        // Validar los datos
        $this->validate([
            'nAcuerdo' => 'required|string',
            'productos_lobo' => 'nullable',
            'productos_otros' => 'nullable',
            'marketing' => 'nullable',
            'observaciones' => 'nullable',
            'firma_comercial_lobo_data' => 'nullable|string',
            'firma_comercial_data' => 'nullable|string',
            'firma_cliente_data' => 'nullable|string',
            'firma_distribuidor_data' => 'nullable|string',
        ]);

            $fecha_firma = $this->formarFechaFirma($this->dia_firma, $this->mes_firma, $this->anio_firma);
            $this->fecha_firma = $fecha_firma->format('Y-m-d');
        

        //dd($this->fecha_firma);

        // Actualizar el acuerdo comercial
        $this->acuerdo->update([
            'productos_lobo' => json_encode($this->productos_lobo),
            'productos_otros' => json_encode($this->productos_otros),
            'marketing' => $this->marketing,
            'observaciones' => $this->observaciones,
            'firma_comercial_lobo' => $this->firma_comercial_lobo_data,
            'firma_comercial' => $this->firma_comercial_data,
            'firma_cliente' => $this->firma_cliente_data,
            'firma_distribuidor' => $this->firma_distribuidor_data,
            'fecha_firma' => $this->fecha_firma,
        ]);

        $this->dispatchBrowserEvent('acuerdoGuardado');
        
    }

    public function render()
    {
        return view('livewire.acuerdos-comerciales.edit-component');
    }
}
