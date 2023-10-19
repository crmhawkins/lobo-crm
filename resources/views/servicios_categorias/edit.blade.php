

@extends('layouts.app')

@section('title', 'Editar/Ver Categoria de Servicio')

@section('head')
    @vite(['resources/sass/productos.scss'])
    @vite(['resources/sass/alumnos.scss'])
@endsection

@section('content-principal')
<div>
    @livewire('servicios-categorias.edit-component', ['identificador'=>$id])
</div>
@endsection

