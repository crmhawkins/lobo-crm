<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\Alertas;

class CheckForAlerts
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle($request, Closure $next)
    {
        // Busca alertas no leídas del usuario actual
        $alerta = Alertas::where('user_id', auth()->id())
            ->where(function ($query) {
                $query->where('leida', false)
                    ->orWhereNull('leida');
            })
            ->where('popup', true) // Añadimos esta condición
            ->first();
        //dd($alerta);
    
        // Si hay una alerta no leída, pasa la alerta a la vista
        if ($alerta) {
            session()->flash('alerta', $alerta);
        }
    
        return $next($request);
    }
}
