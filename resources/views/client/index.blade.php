@extends('layouts.app')
@section('title', 'Clientes')

@section('content')

@livewire('client-component', ['response' => $response])

@endsection
