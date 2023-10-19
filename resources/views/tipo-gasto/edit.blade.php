

@extends('layouts.app')

@section('title', 'Editar/Ver Tipo de Gasto')

@section('head')
    @vite(['resources/sass/productos.scss'])
    @vite(['resources/sass/alumnos.scss'])
@endsection

@section('content-principal')
<div>
    @livewire('tipo-gasto.edit-component', ['identificador'=>$id])
</div>
@endsection

