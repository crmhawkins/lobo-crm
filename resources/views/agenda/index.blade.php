
@extends('layouts.app')

@section('title', 'Ver Agenda')

@section('head')
    @vite(['resources/sass/productos.scss'])
    @vite(['resources/sass/alumnos.scss'])
@endsection

@section('content-principal')
<div>
    @livewire('agenda.index-component')
</div>
@endsection