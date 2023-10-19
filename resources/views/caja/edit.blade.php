

@extends('layouts.app')

@section('title', 'Editar/Ver Tipo de Evento')

@section('head')
    @vite(['resources/sass/productos.scss'])
    @vite(['resources/sass/alumnos.scss'])
@endsection

@section('content-principal')
<div>
    @livewire('caja.edit-component', ['identificador'=>$id])
</div>
@endsection

