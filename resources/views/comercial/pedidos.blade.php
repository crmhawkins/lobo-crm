@extends('layouts.app')

@section('title', 'Ver pedidos')

@section('head')
    @vite(['resources/sass/productos.scss'])
    @vite(['resources/sass/alumnos.scss'])
    <style>
        ul.pagination{
            justify-content: center;
        }
        div#datatable-buttons_filter {
            display: flex !important;
            flex-direction: row-reverse !important;
            align-items: center !important;
        }
        label {
            margin-top: -13px;
        }

        .row{

            margin-bottom: 5px;
        }
        div#Botonesfiltros {
            padding: 5px;
        }
        </style>
@endsection

@section('content-principal')
<div>
    
    @livewire('comercial.pedidos')
</div>
@endsection
