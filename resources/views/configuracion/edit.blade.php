@extends('layouts.app')

@section('title', 'Opciones de Configuración')


@section('content-principal')
<div>
    @livewire('configuracion.edit-component' , ['configuracion'=>$configuracion])
</div>

@endsection
