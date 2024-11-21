<div class="container-fluid">
    <!-- Botón para abrir el modal -->
    <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#addEmpresaModal">
        Añadir Empresa de Transporte
    </button>

    <!-- Tabla para mostrar las empresas de transporte -->
    <table class="table mt-3">
        <thead>
            <tr>
                <th>ID</th>
                <th>Nombre</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            @foreach($empresas as $empresa)
            <tr>
                <td>{{ $empresa->id }}</td>
                <td>{{ $empresa->nombre }}</td>
                <td>
                    <button class="btn btn-warning" wire:click="editEmpresa({{ $empresa->id }})" data-toggle="modal" data-target="#addEmpresaModal">Editar</button>
                    <button class="btn btn-danger" wire:click="deleteEmpresa({{ $empresa->id }})">Eliminar</button>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <!-- Modal para añadir/editar una empresa -->
    <div wire:ignore.self class="modal fade" id="addEmpresaModal" tabindex="-1" role="dialog" aria-labelledby="addEmpresaModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addEmpresaModalLabel">{{ $empresaId ? 'Editar' : 'Añadir' }} Empresa de Transporte</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form wire:submit.prevent="{{ $empresaId ? 'updateEmpresa' : 'addEmpresa' }}">
                        <div class="form-group">
                            <label for="nombreEmpresa">Nombre de la Empresa</label>
                            <input type="text" class="form-control" id="nombreEmpresa" placeholder="Ingrese el nombre de la empresa" wire:model="nombreEmpresa">
                        </div>
                        <!-- Puedes añadir más campos aquí según sea necesario -->
                        <button type="submit" class="btn btn-primary">{{ $empresaId ? 'Actualizar' : 'Guardar' }}</button>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>
</div> 