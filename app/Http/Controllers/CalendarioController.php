<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Event;
use Carbon\Carbon;

class CalendarioController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $datas = [];
        $users = [];

        return view('calendario.index', compact('datas', 'users'));
    }

    public function store(Request $request)
    {
        try {
            // Verificar si 'start' y 'end' son arrays y extraer los valores correctos
            $start = is_array($request->start) ? $request->start['d']['d'] : $request->start;
            $end = is_array($request->end) ? $request->end['d']['d'] : $request->end;

            // Convertir las fechas al formato Y-m-d H:i:s compatible con MySQL y sumar 2 horas para la hora española
            $start = \Carbon\Carbon::parse($start)->addHours(1)->format('Y-m-d H:i:s');
            $end = \Carbon\Carbon::parse($end)->addHours(1)->format('Y-m-d H:i:s');

            // Crear el evento en la base de datos
            $event = Event::create([
                'title' => $request->title,
                'calendar_id' => $request->calendarId,
                'location' => $request->location,
                'isPrivate' => $request->isPrivate,
                'isAllDay' => $request->isAllday,
                'state' => $request->state,
                'category' => $request->category,
                'start' => $start,
                'end' => $end,
            ]);

            // Retornar respuesta con éxito
            return response()->json(['message' => 'Evento creado exitosamente', 'newID' => $event->id], 201);
        } catch (\Exception $e) {
            // Retornar respuesta en caso de error
            return response()->json(['message' => 'Error al crear el evento', 'error' => $e->getMessage()], 500);
        }
    }

    public function update(Request $request, $id)
    {
        // Buscar el evento por su ID usando el parámetro $id de la URL
        $event = Event::findOrFail($id);

        // Verificar si 'start' y 'end' son arrays y extraer los valores correctos
        $start = is_array($request->start) ? $request->start['d']['d'] : $request->start;
        $end = is_array($request->end) ? $request->end['d']['d'] : $request->end;

        // Convertir a hora local sumando 2 horas para la hora española
        $startTime = \Carbon\Carbon::parse($start)->addHours(1)->format('Y-m-d H:i:s');
        $endTime = \Carbon\Carbon::parse($end)->addHours(1)->format('Y-m-d H:i:s');

        // Actualizar el evento
        $event->update([
            'title' => $request->title,
            'calendar_id' => $request->calendarId,
            'location' => $request->location,
            'isPrivate' => $request->isPrivate,
            'isAllDay' => $request->isAllday,
            'state' => $request->state,
            'category' => $request->category,
            'start' => $startTime,  // Usamos la hora modificada
            'end' => $endTime,      // Usamos la hora modificada
        ]);

        // Respuesta de confirmación
        return response()->json(['message' => 'Evento actualizado exitosamente'], 200);
    }


    public function destroy($id)
    {
        // Buscar el evento por su ID y eliminarlo
        $event = Event::findOrFail($id);
        $event->delete();

        // Respuesta de confirmación
        return response()->json(['message' => 'Evento eliminado exitosamente'], 200);
    }
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('calendario.create');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        return view('calendario.edit', compact('id'));
    }
}
