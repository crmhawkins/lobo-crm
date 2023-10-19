@extends('layouts.app')

@section('title', 'Opciones')

@section('head')
@vite(['resources/sass/productos.scss'])
@vite(['resources/sass/alumnos.scss'])
<meta charset="UTF-8">
@endsection

@section('content-principal')
<div>
    @livewire('settings-component')
</div>
@endsection