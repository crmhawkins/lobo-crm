@extends('layouts.app')

@section('title', 'Tipo de Evento')

@section('head')
    @vite(['resources/sass/productos.scss'])
    @vite(['resources/sass/alumnos.scss'])
@endsection

@section('content-principal')
<div>
    @livewire('tipo-evento.create-from-budget-component')
</div>
@endsection

