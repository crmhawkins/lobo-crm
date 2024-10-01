@extends('layouts.app')

@section('title', 'Crear Cuenta Contable')

@section('content-principal')
<div class="container-fluid">
    <h2 class="mb-3">Crear Nueva Cuenta Contable</h2>
    <a href="{{ route('admin.cuentasContables.index') }}" class="btn btn-secondary mb-3">Volver</a>

    <div class="row justify-content-center">
        <div class="col-md-8">
            {{-- Mostrar errores de validación --}}
            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{-- Formulario para crear cuenta contable --}}
            <form action="{{ route('admin.cuentasContables.store') }}" method="POST">
                @csrf
                <div class="form-group">
                    <label for="sub_grupo_id">Sub Grupo Contable</label>
                    <select class="form-control" name="sub_grupo_id" id="sub_grupo_id" required>
                        <option value="">-- Selecciona un Sub Grupo --</option>
                        @foreach($subgrupos as $subgrupo)
                            <option value="{{ $subgrupo->id }}">{{ $subgrupo->numero }} - {{ $subgrupo->nombre }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label for="numero">Número</label>
                    <input type="text" class="form-control @error('numero') is-invalid @enderror" name="numero" id="numero" value="{{ old('numero') }}" required>
                    @error('numero')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="nombre">Nombre</label>
                    <input type="text" class="form-control @error('nombre') is-invalid @enderror" name="nombre" id="nombre" value="{{ old('nombre') }}" required>
                    @error('nombre')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="descripcion">Descripción</label>
                    <textarea class="form-control @error('descripcion') is-invalid @enderror" name="descripcion" id="descripcion" rows="4">{{ old('descripcion') }}</textarea>
                    @error('descripcion')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>

                <button type="submit" class="btn btn-primary">Guardar Cuenta Contable</button>
            </form>
        </div>
    </div>
</div>
@endsection
