

@extends('layouts.app')

@section('title', 'Editar/Ver Categoría de Evento')

@section('head')
    @vite(['resources/sass/productos.scss'])
    @vite(['resources/sass/alumnos.scss'])
@endsection

@section('content-principal')
<div>
    @livewire('categoria-evento.edit-component', ['identificador'=>$id])
</div>
@endsection

