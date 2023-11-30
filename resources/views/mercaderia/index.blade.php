@extends('layouts.app')

@section('title', 'Ver mercadería')

@section('head')
    @vite(['resources/sass/productos.scss'])
    @vite(['resources/sass/alumnos.scss'])
@endsection

@section('content-principal')
<div>
    @livewire('mercaderia.index-component')
</div>

@endsection
