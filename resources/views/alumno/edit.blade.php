@extends('layouts.app')

@section('content')

@section('title', 'Alumnos')


<div>
    @livewire('alumnos.edit-component', ['identificador'=>$id])
</div>

@endsection

