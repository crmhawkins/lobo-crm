@extends('layouts.app')

@section('title', 'Editar Sub Cuenta Hija Contable')

@section('content-principal')
<div class="container-fluid">
    <h2 class="mb-3">Editar Sub Cuenta Hija Contable</h2>
    <a href="{{ route('admin.subCuentasHijaContables.index') }}" class="btn btn-secondary mb-3">Volver a la lista</a>
    <hr class="mb-4">

    <div class="row justify-content-center">
        <div class="col-md-8">
            <form action="{{ route('admin.subCuentasHijaContables.update', $subCuentaHijo->id) }}" method="POST">
                @csrf
                @method('PUT')

                <!-- Selección de Sub Cuenta -->
                <div class="form-group">
                    <label for="cuenta_id">Cuenta Contable</label>
                    <select name="sub_cuenta_id" id="sub_cuenta_id" class="form-control">
                        @foreach($subCuentas as $subCuenta)
                            <option value="{{ $subCuenta->id }}" {{ $subCuentaHijo->sub_cuenta_id == $subCuenta->id ? 'selected' : '' }}>
                                {{ $subCuenta->numero }} - {{ $subCuenta->nombre }}
                            </option>
                        @endforeach
                    </select>
                    @if($errors->has('cuenta_id'))
                        <span class="invalid-feedback">{{ $errors->first('cuenta_id') }}</span>
                    @endif
                </div>

                <!-- Número -->
                <div class="form-group">
                    <label for="numero">Número</label>
                    <input type="text" name="numero" id="numero" class="form-control {{ $errors->has('numero') ? 'is-invalid' : '' }}" value="{{ old('numero', $subCuentaHijo->numero) }}" required>
                    @if($errors->has('numero'))
                        <span class="invalid-feedback">{{ $errors->first('numero') }}</span>
                    @endif
                </div>

                <!-- Nombre -->
                <div class="form-group">
                    <label for="nombre">Nombre</label>
                    <input type="text" name="nombre" id="nombre" class="form-control {{ $errors->has('nombre') ? 'is-invalid' : '' }}" value="{{ old('nombre', $subCuentaHijo->nombre) }}" required>
                    @if($errors->has('nombre'))
                        <span class="invalid-feedback">{{ $errors->first('nombre') }}</span>
                    @endif
                </div>

                <!-- Descripción -->
                <div class="form-group">
                    <label for="descripcion">Descripción</label>
                    <textarea name="descripcion" id="descripcion" rows="4" class="form-control {{ $errors->has('descripcion') ? 'is-invalid' : '' }}">{{ old('descripcion', $subCuentaHijo->descripcion) }}</textarea>
                    @if($errors->has('descripcion'))
                        <span class="invalid-feedback">{{ $errors->first('descripcion') }}</span>
                    @endif
                </div>

                <button type="submit" class="btn btn-primary">Guardar Cambios</button>
            </form>
        </div>
    </div>
</div>
@endsection
