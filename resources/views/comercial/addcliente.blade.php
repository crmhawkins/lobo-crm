@extends('layouts.app')

@section('title', 'Crear Cliente')

@section('head')
    @vite(['resources/sass/productos.scss'])
    @vite(['resources/sass/alumnos.scss'])
@endsection

@section('content-principal')
<div>
    @livewire('comercial.addcliente')
</div>
@endsection

