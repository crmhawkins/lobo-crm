<?php

namespace App\Http\Controllers;

use App\Models\Alertas;
use App\Http\Requests\StoreAlertasRequest;
use App\Http\Requests\UpdateAlertasRequest;

class AlertasController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreAlertasRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreAlertasRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Alertas  $alertas
     * @return \Illuminate\Http\Response
     */
    public function show(Alertas $alertas)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Alertas  $alertas
     * @return \Illuminate\Http\Response
     */
    public function edit(Alertas $alertas)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateAlertasRequest  $request
     * @param  \App\Models\Alertas  $alertas
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateAlertasRequest $request, Alertas $alertas)
    {
        //
    }

    public function marcarLeida($id)
    {
        // Buscar la alerta por ID
        $alerta = Alertas::where('id', $id)
                        ->first();

        //dd($alerta);

        // Si existe la alerta y pertenece al usuario autenticado
        if ($alerta) {
            $alerta->leida = true; // Marcar la alerta como leída
            $alerta->save(); // Guardar los cambios
        }

        // Redirigir a la página anterior o a una página específica
        return redirect()->back()->with('message', 'Alerta marcada como leída.');
    }

    public function marcarTodasLeidas()
{
    Alertas::where('user_id', auth()->id())
        ->where(function ($query) {
            $query->where('leida', false)
                  ->orWhereNull('leida');
        })
        ->update(['leida' => true]);

    return back()->with('success', 'Todas las alertas se han marcado como leídas.');
}


public function popup()
{
    return view('alertas.popup');
}

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Alertas  $alertas
     * @return \Illuminate\Http\Response
     */
    public function destroy(Alertas $alertas)
    {
        //
    }
}
