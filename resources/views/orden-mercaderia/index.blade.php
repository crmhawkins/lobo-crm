@extends('layouts.app')

@section('title', 'Ver órdenes de compra de mercadería')

@section('head')
    @vite(['resources/sass/productos.scss'])
    @vite(['resources/sass/alumnos.scss'])
    <style>
        ul.pagination{
            justify-content: center;
        }

        </style>
@endsection

@section('content-principal')
<div>
    @livewire('orden-mercaderia.index-component')
</div>
@endsection
