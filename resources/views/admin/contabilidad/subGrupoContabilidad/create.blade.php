@extends('layouts.app')

@section('title', 'Crear Sub Grupo Contable')

@section('content-principal')
<div class="container-fluid">
    <h2 class="mb-3">Crear Sub Grupo Contable</h2>
    <a href="{{ route('admin.subGrupoContabilidad.index') }}" class="btn btn-secondary mb-3">Volver a la lista</a>
    <hr class="mb-4">

    <div class="row justify-content-center">
        <div class="col-md-8">
            <form action="{{ route('admin.subGrupoContabilidad.store') }}" method="POST">
                @csrf

                <!-- Selección de Grupo Contable -->
                <div class="form-group">
                    <label for="grupo_id">Grupo Contable</label>
                    <select name="grupo_id" id="grupo_id" class="form-control">
                        <option value="">Selecciona un Grupo Contable</option>
                        @foreach($grupos as $grupo)
                            <option value="{{ $grupo->id }}">{{ $grupo->numero }} - {{ $grupo->nombre }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Número -->
                <div class="form-group">
                    <label for="numero">Número</label>
                    <input type="text" name="numero" id="numero" class="form-control" required>
                </div>

                <!-- Nombre -->
                <div class="form-group">
                    <label for="nombre">Nombre</label>
                    <input type="text" name="nombre" id="nombre" class="form-control" required>
                </div>

                <!-- Descripción -->
                <div class="form-group">
                    <label for="descripcion">Descripción</label>
                    <textarea name="descripcion" id="descripcion" rows="4" class="form-control"></textarea>
                </div>

                <button type="submit" class="btn btn-primary">Crear Sub Grupo Contable</button>
            </form>
        </div>
    </div>
</div>
@endsection
