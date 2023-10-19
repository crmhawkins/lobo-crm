@extends('layouts.app')

@section('title', 'Editar Contrato {{$id}}')

@section('head')
    @vite(['resources/sass/productos.scss'])
    @vite(['resources/sass/alumnos.scss'])
@endsection

@section('content-principal')
<div>
    @livewire('contratos.edit-component', ['identificador'=>$id])
</div>

@endsection

