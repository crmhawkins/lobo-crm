

@extends('layouts.app')

@section('title', 'Editar/Ver Movimiento de Caja')

@section('head')
    @vite(['resources/sass/productos.scss'])
    @vite(['resources/sass/alumnos.scss'])
@endsection

@section('content-principal')
<div>
    @livewire('caja.edit-component', ['identificador'=>$id])
</div>
@endsection

