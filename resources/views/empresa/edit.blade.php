@extends('layouts.app')

@section('content')

@section('title', 'Empresas')


<div>
    @livewire('empresas.edit-component', ['identificador'=>$id])
</div>

@endsection

