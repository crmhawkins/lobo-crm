@extends('layouts.app')

@section('content')

@livewire('clients.edit-component', ['identificador' => $id])

@endsection