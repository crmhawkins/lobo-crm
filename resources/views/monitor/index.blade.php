
@extends('layouts.app')

@section('title', 'Ver Monitores')

@section('head')
    @vite(['resources/sass/productos.scss'])
    @vite(['resources/sass/alumnos.scss'])
@endsection

@section('content-principal')
<div>
    @livewire('monitores.index-component')
</div>
@endsection
