@extends('layouts.app')

@section('title', 'Editar órden de compra de mercadería')

@section('head')
@vite(['resources/sass/productos.scss'])
@vite(['resources/sass/alumnos.scss'])
@endsection

@section('content-principal')
<div>
    @livewire('orden-mercaderia.edit-component', ['identificador'=>$id])
</div>
@endsection
