@extends('layouts.app')

@section('content')

@section('title', 'Empresas')


<div>
    @livewire('resumen-mensual.edit-component', ['identificador'=>$id])
</div>

@endsection

