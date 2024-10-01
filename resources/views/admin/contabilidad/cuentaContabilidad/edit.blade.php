@extends('layouts.app')

@section('title', 'Editar Cuenta Contable')

@section('head')
    @vite(['resources/sass/productos.scss'])
@endsection

@section('content-principal')
<div class="container-fluid">
    <h2 class="mb-3">Editar Cuenta Contable</h2>
    <a href="{{ route('admin.cuentasContables.index') }}" class="btn btn-secondary mb-3">Volver a la lista</a>
    <hr class="mb-4">

    <div class="row justify-content-center">
        <div class="col-md-8">
        <form action="{{ route('admin.cuentasContables.updated', $cuenta->id) }}" method="POST">
            @csrf
            @method('PUT') <!-- Cambiar a PUT o PATCH -->
            
            <!-- Grupo Contable -->
            <div class="form-group">
                <label for="sub_grupo_id">Grupo Contable</label>
                <select name="sub_grupo_id" id="sub_grupo_id" class="form-control {{ $errors->has('sub_grupo_id') ? 'is-invalid' : '' }}">
                    @foreach($grupo as $gr)
                        <option value="{{ $gr->id }}" {{ $cuenta->grupo_id == $gr->id ? 'selected' : '' }}>
                            {{ $gr->numero . ' - ' . $gr->nombre }}
                        </option>
                    @endforeach
                </select>
                @if($errors->has('grupo_id'))
                    <span class="invalid-feedback">{{ $errors->first('grupo_id') }}</span>
                @endif
            </div>

            <!-- Número -->
            <div class="form-group">
                <label for="numero">Número</label>
                <input type="text" name="numero" id="numero" class="form-control {{ $errors->has('numero') ? 'is-invalid' : '' }}" value="{{ old('numero', $cuenta->numero) }}" required>
                @if($errors->has('numero'))
                    <span class="invalid-feedback">{{ $errors->first('numero') }}</span>
                @endif
            </div>

            <!-- Nombre -->
            <div class="form-group">
                <label for="nombre">Nombre</label>
                <input type="text" name="nombre" id="nombre" class="form-control {{ $errors->has('nombre') ? 'is-invalid' : '' }}" value="{{ old('nombre', $cuenta->nombre) }}" required>
                @if($errors->has('nombre'))
                    <span class="invalid-feedback">{{ $errors->first('nombre') }}</span>
                @endif
            </div>

            <!-- Descripción -->
            <div class="form-group">
                <label for="descripcion">Descripción</label>
                <textarea name="descripcion" id="descripcion" rows="4" class="form-control {{ $errors->has('descripcion') ? 'is-invalid' : '' }}">{{ old('descripcion', $cuenta->descripcion) }}</textarea>
                @if($errors->has('descripcion'))
                    <span class="invalid-feedback">{{ $errors->first('descripcion') }}</span>
                @endif
            </div>

            <!-- Botón de enviar -->
            <button type="submit" class="btn btn-primary">Guardar Cambios</button>
        </form>


        </div>
    </div>
</div>
@endsection
