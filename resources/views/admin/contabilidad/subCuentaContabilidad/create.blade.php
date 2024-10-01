@extends('layouts.app')

@section('title', 'Crear Sub Cuenta Contable')

@section('content-principal')
<div class="container-fluid">
    <h2 class="mb-3">Crear Sub Cuenta Contable</h2>
    <a href="{{ route('admin.subCuentasContables.index') }}" class="btn btn-secondary mb-3">Volver a la lista</a>
    <hr class="mb-4">

    <div class="row justify-content-center">
        <div class="col-md-8">
            <form action="{{ route('admin.subCuentasContables.store') }}" method="POST">
                @csrf
                <!-- Selección de Cuenta Contable -->
                <div class="form-group">
                    <label for="cuenta_id">Cuenta Contable</label>
                    <select name="cuenta_id" id="cuenta_id" class="form-control {{ $errors->has('cuenta_id') ? 'is-invalid' : '' }}">
                        <option value="">Selecciona una Cuenta Contable</option>
                        @foreach($cuentasContables as $cuenta)
                            <option value="{{ $cuenta->id }}">{{ $cuenta->numero }} - {{ $cuenta->nombre }}</option>
                        @endforeach
                    </select>
                    @if($errors->has('cuenta_id'))
                        <span class="invalid-feedback">{{ $errors->first('cuenta_id') }}</span>
                    @endif
                </div>

                <!-- Número -->
                <div class="form-group">
                    <label for="numero">Número</label>
                    <input type="text" name="numero" id="numero" class="form-control {{ $errors->has('numero') ? 'is-invalid' : '' }}" value="{{ old('numero') }}" required>
                    @if($errors->has('numero'))
                        <span class="invalid-feedback">{{ $errors->first('numero') }}</span>
                    @endif
                </div>

                <!-- Nombre -->
                <div class="form-group">
                    <label for="nombre">Nombre</label>
                    <input type="text" name="nombre" id="nombre" class="form-control {{ $errors->has('nombre') ? 'is-invalid' : '' }}" value="{{ old('nombre') }}" required>
                    @if($errors->has('nombre'))
                        <span class="invalid-feedback">{{ $errors->first('nombre') }}</span>
                    @endif
                </div>

                <!-- Descripción -->
                <div class="form-group">
                    <label for="descripcion">Descripción</label>
                    <textarea name="descripcion" id="descripcion" rows="4" class="form-control {{ $errors->has('descripcion') ? 'is-invalid' : '' }}">{{ old('descripcion') }}</textarea>
                    @if($errors->has('descripcion'))
                        <span class="invalid-feedback">{{ $errors->first('descripcion') }}</span>
                    @endif
                </div>

                <!-- Botón de enviar -->
                <button type="submit" class="btn btn-primary">Crear Sub Cuenta Contable</button>
            </form>
        </div>
    </div>
</div>
@endsection
