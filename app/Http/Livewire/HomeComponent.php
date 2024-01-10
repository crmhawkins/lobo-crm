<?php

namespace App\Http\Livewire;

use App\Models\Jornada;
use App\Models\Pausa;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class HomeComponent extends Component
{
    public $jornada_activa;
    public $pausa_activa;

    public function mount()
    {
        $this->checkJornada();

    }
    public function render()
    {
        return view('livewire.home-component');
    }

    public function iniciarJornada()
    {
        $hora_inicio = Carbon::now()->toDateTimeString();
        $user_id = Auth::id();
        Jornada::create(['user_id' => $user_id, 'hora_inicio' => $hora_inicio, 'status' => 1]);
        $this->checkJornada();
    }

    public function finalizarJornada()
    {
        $hora_final = Carbon::now()->toDateTimeString();
    $user_id = Auth::id();

    // Obtener la jornada activa actual
    $jornada_actual = Jornada::where('user_id', $user_id)->where('status', 1)->first();

    // Asegurarse de que existe una jornada activa antes de intentar actualizar
    if ($jornada_actual) {
        $jornada_actual->update([
            'hora_final' => $hora_final, // Actualizar solo la hora final
            'status' => 0 // Cambiar el estado a inactivo
        ]);
    }

    $this->checkJornada();
    }
    public function iniciarPausa()
    {
        $hora_inicio = Carbon::now()->toDateTimeString();
        $user_id = Auth::id();
        Pausa::create(['user_id' => $user_id, 'hora_inicio' => $hora_inicio, 'status' => 1]);
        $this->checkJornada();
    }

    public function finalizarPausa()
    {
        $hora_final = Carbon::now()->toDateTimeString();
        $user_id = Auth::id();
        $jornada_actual = Pausa::where('user_id', $user_id)->where('status', 1)->first();
        $jornada_actual->update(['hora_final' => $hora_final, 'status' => 0]);
        $this->checkJornada();
    }

    public function checkJornada()
    {
        $jornada = Jornada::where('user_id', Auth::id())->where('status', 1)->count();
        $pausa = Pausa::where('user_id', Auth::id())->where('status', 1)->count();
        if ($jornada > 0) {
            $this->jornada_activa = 1;
        } else {
            $this->jornada_activa = 0;
        }
        if ($pausa > 0) {
            $this->pausa_activa = 1;
        } else {
            $this->pausa_activa = 0;
        }
    }

    public function getHorasTrabajadas($query)
    {
        switch ($query) {
            case 'Hoy':
                $fecha = Carbon::now();

                $inicioDelDia = $fecha->copy()->startOfDay();
                $finalDelDia = $fecha->copy()->endOfDay();

                $usuarioId = auth()->id();

                $logsJornada = Jornada::where('user_id', $usuarioId)
                    ->where('hora_inicio', '>=', $inicioDelDia)
                    ->where(function ($query) use ($finalDelDia) {
                        $query->where('hora_final', '<=', $finalDelDia)->orWhereNull('hora_final');
                    })
                    ->orderBy('hora_inicio', 'asc')
                    ->get();

                $logsPausas = Pausa::where('user_id', $usuarioId)
                    ->where('hora_inicio', '>=', $inicioDelDia)
                    ->where(function ($query) use ($finalDelDia) {
                        $query->where('hora_final', '<=', $finalDelDia)->orWhereNull('hora_final');
                    })
                    ->orderBy('hora_inicio', 'asc')
                    ->get();

                $totalSegundosTrabajados = 0;

                foreach ($logsJornada as $log) {
                    $hora_inicio = Carbon::parse($log->hora_inicio);
                    $hora_final = $log->hora_final ? Carbon::parse($log->hora_final) : $fecha;

                    $totalSegundosTrabajados += $hora_inicio->diffInSeconds($hora_final);
                }

                foreach ($logsPausas as $log) {
                    $hora_inicio = Carbon::parse($log->hora_inicio);
                    $hora_final = $log->hora_final ? Carbon::parse($log->hora_final) : $fecha;

                    $totalSegundosTrabajados -= $hora_inicio->diffInSeconds($hora_final);
                }

                $horas = intdiv($totalSegundosTrabajados, 3600);
                $minutos = intdiv($totalSegundosTrabajados % 3600, 60);
                $segundos = $totalSegundosTrabajados % 60;

                $diferencia = sprintf("%02d horas, %02d minutos, %02d segundos", $horas, $minutos, $segundos);

                return $diferencia;

            case 'Semana':
                $usuarioId = Auth::id();
                $ahora = Carbon::now();
                $inicioDeSemana = $ahora->copy()->startOfWeek();
                $finDeSemana = $ahora->copy()->endOfWeek();

                $totalSegundosJornada = 0;
                $totalSegundosPausas = 0;

                for ($dia = $inicioDeSemana->copy(); $dia->lte($finDeSemana); $dia->addDay()) {
                    // Calcular duración total de las jornadas del día
                    $logsJornada = Jornada::where('user_id', $usuarioId)
                        ->whereDate('hora_inicio', $dia->toDateString())
                        ->get();

                    foreach ($logsJornada as $log) {
                        $hora_inicio = Carbon::parse($log->hora_inicio);
                        $hora_final = $log->hora_final ? Carbon::parse($log->hora_final) : $ahora;

                        $duracion = $hora_inicio->diffInSeconds($hora_final);
                        $totalSegundosJornada += $duracion;
                    }

                    // Calcular duración total de las pausas del día
                    $logsPausas = Pausa::where('user_id', $usuarioId)
                        ->whereDate('hora_inicio', $dia->toDateString())
                        ->get();

                    foreach ($logsPausas as $log) {
                        $hora_inicio = Carbon::parse($log->hora_inicio);
                        $hora_final = $log->hora_final ? Carbon::parse($log->hora_final) : $ahora;

                        $duracion = $hora_inicio->diffInSeconds($hora_final);
                        $totalSegundosPausas += $duracion;
                    }
                }

                // Restar la duración total de las pausas a la duración total de las jornadas
                $totalSegundosTrabajados = $totalSegundosJornada - $totalSegundosPausas;

                $horas = intdiv($totalSegundosTrabajados, 3600);
                $minutos = intdiv($totalSegundosTrabajados % 3600, 60);
                $segundos = $totalSegundosTrabajados % 60;

                $diferencia = sprintf("%02d horas, %02d minutos, %02d segundos", $horas, $minutos, $segundos);
                return $diferencia;
        }
    }
}
