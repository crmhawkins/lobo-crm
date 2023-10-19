@extends('layouts.app')

@section('title', 'Crear Contrato')

@section('head')
    @vite(['resources/sass/productos.scss'])
    @vite(['resources/sass/alumnos.scss'])
@endsection

@section('content-principal')
<div>
    @livewire('contratos.create-component')
</div>
@endsection




