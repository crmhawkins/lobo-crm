@extends('layouts.app')

@section('title', 'Editar Servicio')

@section('head')
@vite(['resources/sass/productos.scss'])
@vite(['resources/sass/alumnos.scss'])
@endsection

@section('content-principal')
<div>
    @livewire('servicios.edit-component', ['identificador'=>$id])
</div>

@endsection