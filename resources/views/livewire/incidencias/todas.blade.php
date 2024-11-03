<div class="container-fluid">
    <h2>Registros de Incidencias</h2>

    <!-- Navegación de pestañas -->
    <div class="border-b border-gray-200">
        <nav class="-mb-px flex space-x-8" aria-label="Tabs">
            <button
                wire:click="setActiveTab('incidencias')"
                class="btn @if($activeTab === 'incidencias') btn-primary @else btn-outline-primary @endif"
            >
                Incidencias Generales
            </button>

            <button
                wire:click="setActiveTab('pedidos')"
                class="btn @if($activeTab === 'pedidos') btn-primary @else btn-outline-primary @endif"
            >
                Incidencias de Pedidos
            </button>
        </nav>
    </div>

    <!-- Contenido de las pestañas -->
    <div class="mt-4">
        @if($activeTab === 'incidencias')
            <!-- Contenido de incidencias generales -->
            <div>
                <table class="table">
                    <thead>
                        <tr>
                            <th>Fecha</th>
                            <th>Descripción</th>
                            <th>Estado</th>
                            <th>Responsable</th>
                            <th>Actualización</th>
                            <th>Archivada</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($incidencias as $incidencia)
                            <tr>
                                <td>{{ $incidencia->created_at }}</td>
                                <td>{{ $incidencia->observaciones }}</td>
                                <td class="text-uppercase @if($incidencia->estado === 'solucionada') text-success
                                    @else text-warning
                                    @endif">
                                    {{ $incidencia->estado }}
                                </td>
                                <td>{{ $incidencia->user->name }} {{ $incidencia->user->surname  }}</td>
                                <td>{{ $incidencia->updated_at }}</td>
                                <td class=" @if($incidencia->deleted_at) text-info @else text-danger @endif">
                                    {{ $incidencia->deleted_at ? 'Si' : 'No' }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                <div class="mt-4">
                    {{ $incidencias->links() }}
                </div>
            </div>
        @endif

        @if($activeTab === 'pedidos')
            <!-- Contenido de incidencias de pedidos -->
            <div>
                <table class="table">
                    <thead>
                        <tr>
                            <th>Fecha</th>
                            <th>Pedido</th>
                            <th>Descripción</th>
                            <th>Estado</th>
                            <th>Responsable</th>
                            <th>Actualización</th>
                            <th>Archivada</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($incidenciasPedidos as $incidenciaPedido)
                            <tr>
                                <td>{{ $incidenciaPedido->created_at }}</td>
                                <td><a class="btn btn-sm btn-outline-primary" href="{{ route('pedidos.edit', $incidenciaPedido->pedido_id) }}">nº{{ $incidenciaPedido->pedido_id }} -- {{ $incidenciaPedido->pedido->cliente->nombre }}</a></td>
                                <td>{{ $incidenciaPedido->observaciones }}</td>
                                <td class="text-uppercase @if($incidenciaPedido->estado === 'solucionada') text-success
                                    @else text-warning
                                    @endif">
                                    {{ $incidenciaPedido->estado }}
                                </td>
                                <td>{{ $incidenciaPedido->user->name }} {{ $incidenciaPedido->user->surname  }}</td>
                                <td>{{ $incidenciaPedido->updated_at }}</td>
                                <td class=" @if($incidenciaPedido->deleted_at) text-info @else text-danger @endif">
                                    {{ $incidenciaPedido->deleted_at ? 'Si' : 'No' }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                <div class="mt-4">
                    {{ $incidenciasPedidos->links() }}
                </div>
            </div>
        @endif
    </div>
</div>






