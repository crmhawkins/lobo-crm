@extends('layouts.app')

@section('title', 'Dashboard')

@section('head')
    @vite(['resources/sass/productos.scss'])
    @vite(['resources/sass/alumnos.scss'])
@endsection

@section('content-principal')
<div>
    @livewire('home-component')
</div>

@endsection
