@extends('layouts.app')

@section('content')

@section('title', 'Cursos')


<div>
    @livewire('cursos.edit-component', ['identificador'=>$id])
</div>

@endsection

