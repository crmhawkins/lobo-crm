@extends('layouts.app')

@section('title', 'Crear Grupo Contable')

@section('content-principal')
<div class="container-fluid">
    <h2 class="mb-3">Crear Grupo Contable</h2>
    <a href="{{ route('admin.grupoContabilidad.index') }}" class="btn btn-secondary mb-3">Volver a la lista</a>
    <hr class="mb-4">

    <div class="row justify-content-center">
        <div class="col-md-8">
            <form action="{{ route('admin.grupoContabilidad.store') }}" method="POST">
                @csrf
                <div class="form-group">
                    <label for="numero">Número</label>
                    <input type="text" name="numero" id="numero" class="form-control {{ $errors->has('numero') ? 'is-invalid' : '' }}" value="{{ old('numero') }}" required>
                    @if($errors->has('numero'))
                        <span class="invalid-feedback">{{ $errors->first('numero') }}</span>
                    @endif
                </div>

                <div class="form-group">
                    <label for="nombre">Nombre</label>
                    <input type="text" name="nombre" id="nombre" class="form-control {{ $errors->has('nombre') ? 'is-invalid' : '' }}" value="{{ old('nombre') }}" required>
                    @if($errors->has('nombre'))
                        <span class="invalid-feedback">{{ $errors->first('nombre') }}</span>
                    @endif
                </div>

                <div class="form-group">
                    <label for="descripcion">Descripción</label>
                    <textarea name="descripcion" id="descripcion" rows="4" class="form-control {{ $errors->has('descripcion') ? 'is-invalid' : '' }}" required>{{ old('descripcion') }}</textarea>
                    @if($errors->has('descripcion'))
                        <span class="invalid-feedback">{{ $errors->first('descripcion') }}</span>
                    @endif
                </div>

                <button type="submit" class="btn btn-primary">Crear Grupo Contable</button>
            </form>
        </div>
    </div>
</div>
@endsection
