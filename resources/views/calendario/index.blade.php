
@extends('layouts.app')

@section('title', 'Ver Calendario')

@section('head')
    @vite(['resources/sass/productos.scss'])
    @vite(['resources/sass/alumnos.scss'])
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/1.5.3/jspdf.min.js"></script>
@endsection

@section('content-principal')
<div>
    @livewire('calendario.index-component')
</div>
@endsection