<div class="container-fluid">
    <script src="//unpkg.com/alpinejs" defer></script>
    <div class="page-title-box">
        <div class="row align-items-center">
            <div class="col-sm-6">
                <h4 class="page-title">CUADRANTE SEMANAL</span></h4>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-right">
                    <li class="breadcrumb-item"><a href="javascript:void(0);">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="javascript:void(0);">Cuadrante</a></li>
                    <li class="breadcrumb-item active">Cuadrante semanal</li>
                </ol>
            </div>
        </div> <!-- end row -->
    </div>
    <div class="row">
        <div class="col-md-9">
            <div class="card m-b-30">
                <div class="card-body">
                    @foreach ($dias as $diaIndex => $dia)
                        <div class="form-group col-md-12">
                            <h5 class="ms-3"
                                style="border-bottom: 1px gray solid !important; padding-bottom: 10px !important;">
                                {{ $dia }}</h5>
                        </div>
                        <div class="form-group col-md-12">
                            @if ($eventos->where('diaEvento', $fechas[$diaIndex])->count() > 0)
                                @foreach ($eventos->where('diaEvento', $fechas[$diaIndex]) as $evento)
                                    <table class="table table-striped table-bordered nowrap">
                                        <tr>
                                            <th colspan="1">
                                                #{{ $presupuestos->where('id_evento', $evento->id)->first()->nPresupuesto }}
                                            </th>
                                            <th colspan="5">
                                                @if ($datoEdicion['id'] == $evento->id && $datoEdicion['column'] == 'eventoNombre')
                                                    <div class="col-md-8" x-data=""
                                                        x-init="$('#select2-evento').select2();
                                                        $('#select2-evento').on('change', function(e) {
                                                            var data = $('#select2-evento').select2('val');
                                                            @this.set('datoEdicion['
                                                                column ']', data);
                                                        });" wire:key='rand()'>
                                                        <select class="form-control" name="eventoNombre"
                                                            id="select2-evento" wire:model.lazy="datoEdicion.value"
                                                            wire:change.lazy='terminarEdicion'>
                                                            <option value="0">-- ELIGE UN TIPO DE EVENTO --
                                                            </option>
                                                            @foreach ($categorias as $tipo)
                                                                <option value="{{ $tipo->id }}">
                                                                    {{ $tipo->nombre }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                @else
                                                    <span class="align-middle"
                                                        wire:click="detectarEdicion('{{ $evento->id }}', 'eventoNombre')">

                                                        {{ $this->categorias->where('id', $evento->eventoNombre)->first()->nombre }}
                                                    </span>
                                                @endif
                                            <th>
                                            <th><a class="btn btn-sm btn-primary w-100"
                                                    href="{{ route('presupuestos.edit', $presupuestos->where('id_evento', $evento->id)->first()->id) }}"><i
                                                        class="fa fa-eye"></i></a> </th>
                                        </tr>
                                        <tr>
                                            <td>
                                                @if ($datoEdicion['id'] == $evento->id && $datoEdicion['column'] == 'precioFinal')
                                                    <input type="number" wire:model.lazy="datoEdicion.value"
                                                        wire:change.lazy="terminarEdicion">
                                                @else
                                                    <span class="align-middle"
                                                        wire:click="detectarEdicion('{{ $evento->id }}', 'precioFinal')">{{ $presupuestos->where('id_evento', $evento->id)->first()->precioFinal }}
                                                        €</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if ($datoEdicion['id'] == $evento->id && $datoEdicion['column'] == 'eventoNiños')
                                                    <input type="number" wire:model.lazy="datoEdicion.value"
                                                        wire:change.lazy="terminarEdicion" name="eventoNiños"
                                                        id="eventoNiños"> niños
                                                @else
                                                    <span class="align-middle"
                                                        wire:click="detectarEdicion('{{ $evento->id }}', 'eventoNiños')">{{ $evento->eventoNiños }}
                                                        niños</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if ($datoEdicion['id'] == $evento->id && $datoEdicion['column'] == 'eventoAdulto')
                                                    <input type="number" wire:model.lazy="datoEdicion.value"
                                                        wire:change.lazy="terminarEdicion"> adultos
                                                @else
                                                    <span class="align-middle"
                                                        wire:click="detectarEdicion('{{ $evento->id }}', 'eventoAdulto')">{{ $evento->eventoAdulto ? $evento->eventoAdulto : 0 }}
                                                        adultos</span>
                                                @endif
                                            </td>
                                            <td>{{ $this->checkAuthContrato($evento->id) }}</td>
                                            <th></th>
                                            <th>{{ $evento->eventoLugar }}</th>
                                            <th></th>
                                            <th>{{ $evento->eventoLocalidad }}</th>

                                        </tr>
                                        <tr>
                                            <th>Servicio</th>
                                            <th>Hora de montaje</th>
                                            <th>Hora de inicio</th>
                                            <th>Duración</th>
                                            <th>Tiempo de desmontaje</th>
                                            <th>Monitor</th>
                                            <th>Sueldo</th>
                                            <th>Gasoil</th>
                                        </tr>
                                        @foreach ($presupuestos->where('id_evento', $evento->id)->first()->servicios()->get() as $servicio)
                                            @foreach (json_decode($servicio->pivot->id_monitores, true) as $monitoresIndex => $monitores)
                                                <tr>
                                                    @if ($monitoresIndex == 0)
                                                        <td>
                                                            @if ($datoEdicion['id'] != null)
                                                                @if ($datoEdicion['id']['presupuesto'] == $evento->id && $datoEdicion['column'] == 'servicioNombre')
                                                                    <div class="col-md-8" x-data=""
                                                                        x-init="$('#select2-servicio').select2();
                                                                        $('#select2-servicio').on('change', function(e) {
                                                                            var data = $('#select2-servicio').select2('val');
                                                                            @this.set('datoEdicion['
                                                                                value ']', data);
                                                                        });" wire:key='rand()'>
                                                                        <select class="form-control"
                                                                            name="servicioNombre" id="select2-servicio"
                                                                            wire:model.lazy="datoEdicion.value"
                                                                            wire:change.lazy='terminarEdicionServicio'>
                                                                            <option value="0">-- ELIGE UN SERVICIO
                                                                                --
                                                                            </option>
                                                                            @foreach ($servicios as $servicio_select)
                                                                                <option
                                                                                    value="{{ $servicio_select->id }}">
                                                                                    {{ $servicio_select->nombre }}
                                                                                </option>
                                                                            @endforeach
                                                                        </select>
                                                                    </div>
                                                                @else
                                                                    <span class="align-middle"
                                                                        wire:click="detectarEdicionServicio('{{ $evento->id }}', '{{ $servicio->id }}', 'servicioNombre')">{{ $servicio->nombre }}</span>
                                                                @endif
                                                            @else
                                                                <span class="align-middle"
                                                                    wire:click="detectarEdicionServicio('{{ $evento->id }}', '{{ $servicio->id }}', 'servicioNombre')">{{ $servicio->nombre }}</span>
                                                            @endif

                                                        </td>
                                                        <td>
                                                            @if ($datoEdicion['id'] != null)
                                                                @if ($datoEdicion['id']['presupuesto'] == $evento->id && $datoEdicion['column'] == 'servicioHoraMontaje')
                                                                    <input type="time"
                                                                        wire:model.lazy="datoEdicion.value"
                                                                        wire:change.lazy="terminarEdicionServicio">
                                                                @else
                                                                    <span class="align-middle"
                                                                        wire:click="detectarEdicionServicio('{{ $evento->id }}', '{{ $servicio->id }}', 'servicioHoraMontaje')">{{ $servicio->pivot->hora_montaje }}</span>
                                                                @endif
                                                            @else
                                                                <span class="align-middle"
                                                                    wire:click="detectarEdicionServicio('{{ $evento->id }}', '{{ $servicio->id }}', 'servicioHoraMontaje')">{{ $servicio->pivot->hora_montaje }}</span>
                                                            @endif


                                                        </td>
                                                        <td>
                                                            @if ($datoEdicion['id'] != null)
                                                                @if ($datoEdicion['id']['presupuesto'] == $evento->id && $datoEdicion['column'] == 'servicioHoraInicio')
                                                                    <input type="time"
                                                                        wire:model.lazy="datoEdicion.value"
                                                                        wire:change.lazy="terminarEdicionServicio">
                                                                @else
                                                                    <span class="align-middle"
                                                                        wire:click="detectarEdicionServicio('{{ $evento->id }}', '{{ $servicio->id }}', 'servicioHoraInicio')">{{ $servicio->pivot->hora_inicio }}</span>
                                                                @endif
                                                            @else
                                                                <span class="align-middle"
                                                                    wire:click="detectarEdicionServicio('{{ $evento->id }}', '{{ $servicio->id }}', 'servicioHoraInicio')">{{ $servicio->pivot->hora_inicio }}</span>
                                                            @endif
                                                        </td>
                                                        <td>
                                                            @if ($datoEdicion['id'] != null)
                                                                @if ($datoEdicion['id']['presupuesto'] == $evento->id && $datoEdicion['column'] == 'servicioHoraTiempo')
                                                                    <input type="time"
                                                                        wire:model.lazy="datoEdicion.value"
                                                                        wire:change.lazy="terminarEdicionServicio">
                                                                @else
                                                                    <span class="align-middle"
                                                                        wire:click="detectarEdicionServicio('{{ $evento->id }}', '{{ $servicio->id }}', 'servicioHoraTiempo')">{{ $servicio->pivot->tiempo }}</span>
                                                                @endif
                                                            @else
                                                                <span class="align-middle"
                                                                    wire:click="detectarEdicionServicio('{{ $evento->id }}', '{{ $servicio->id }}', 'servicioHoraTiempo')">{{ $servicio->pivot->tiempo }}</span>
                                                            @endif
                                                        </td>
                                                        <td>
                                                            @if ($datoEdicion['id'] != null)
                                                                @if ($datoEdicion['id']['presupuesto'] == $evento->id && $datoEdicion['column'] == 'servicioHoraTiempoMontaje')
                                                                    <input type="time"
                                                                        wire:model.lazy="datoEdicion.value"
                                                                        wire:change.lazy="terminarEdicionServicio">
                                                                @else
                                                                    <span class="align-middle"
                                                                        wire:click="detectarEdicionServicio('{{ $evento->id }}', '{{ $servicio->id }}', 'servicioHoraTiempoMontaje')">{{ $servicio->pivot->tiempo_montaje }}</span>
                                                                @endif
                                                            @else
                                                                <span class="align-middle"
                                                                    wire:click="detectarEdicionServicio('{{ $evento->id }}', '{{ $servicio->id }}', 'servicioHoraTiempoMontaje')">{{ $servicio->pivot->tiempo_montaje }}</span>
                                                            @endif
                                                        </td>
                                                    @else
                                                        <td colspan="5">&nbsp;</td>
                                                    @endif
                                                    <td>
                                                        @if ($datoEdicion['id'] != null && isset($datoEdicion['monitor']))
                                                            @if (
                                                                $datoEdicion['id']['presupuesto'] == $evento->id &&
                                                                    $datoEdicion['id']['monitor'] == $monitoresIndex &&
                                                                    $datoEdicion['column'] == 'monitorNombre')
                                                                <div class="col-md-8" x-data=""
                                                                    x-init="$('#select2-monitor').select2();
                                                                    $('#select2-monitor').on('change', function(e) {
                                                                        var data = $('#select2-monitor').select2('val');
                                                                        @this.set('datoEdicion['
                                                                            value ']', data);
                                                                    });" wire:key='rand()'>
                                                                    <select class="form-control" name="servicioNombre"
                                                                        id="select2-monitor"
                                                                        wire:model.lazy="datoEdicion.value"
                                                                        wire:change.lazy='terminarEdicionMonitores'>
                                                                        <option value="0">-- ELIGE UN SERVICIO
                                                                            --
                                                                        </option>
                                                                        @foreach ($monitores_datos as $monitor_select)
                                                                            <option value="{{ $monitor_select->id }}">
                                                                                {{ $monitor_select->alias }}
                                                                            </option>
                                                                        @endforeach
                                                                    </select>
                                                                </div>
                                                            @else
                                                                <span class="align-middle"
                                                                    wire:click="detectarEdicionMonitores('{{ $evento->id }}', '{{ $servicio->id }}', '{{ $monitoresIndex }}', 'monitorNombre')">{{ $this->getMonitor($monitores) }}"
                                                                </span>
                                                            @endif
                                                        @else
                                                            <span class="align-middle"
                                                                wire:click="detectarEdicionMonitores('{{ $evento->id }}', '{{ $servicio->id }}', '{{ $monitoresIndex }}', 'monitorNombre')">{{ $this->getMonitor($monitores) }}</span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if ($datoEdicion['id'] != null && isset($datoEdicion['monitor']))
                                                            @if (
                                                                $datoEdicion['id']['presupuesto'] == $evento->id &&
                                                                    $datoEdicion['id']['monitor'] == $monitoresIndex &&
                                                                    $datoEdicion['column'] == 'sueldoMonitor')
                                                                <input type="number"
                                                                    wire:model.lazy="datoEdicion.value"
                                                                    wire:change.lazy="terminarEdicionMonitores">
                                                            @else
                                                                <span class="align-middle"
                                                                    wire:click="detectarEdicionMonitores('{{ $evento->id }}', '{{ $servicio->id }}', '{{ $monitoresIndex }}', 'sueldoMonitor')">{{ json_decode($servicio->pivot->sueldo_monitores, true)[$monitoresIndex] }}
                                                                    €</span>
                                                            @endif
                                                        @else
                                                            <span class="align-middle"
                                                                wire:click="detectarEdicionMonitores('{{ $evento->id }}', '{{ $servicio->id }}', '{{ $monitoresIndex }}', 'sueldoMonitor')">{{ json_decode($servicio->pivot->sueldo_monitores, true)[$monitoresIndex] }}
                                                                €</span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if ($datoEdicion['id'] != null && isset($datoEdicion['monitor']))
                                                            @if (
                                                                $datoEdicion['id']['presupuesto'] == $evento->id &&
                                                                    $datoEdicion['id']['monitor'] == $monitoresIndex &&
                                                                    $datoEdicion['column'] == 'gasto_gasoil')
                                                                <input type="number"
                                                                    wire:model.lazy="datoEdicion.value"
                                                                    wire:change.lazy="terminarEdicionMonitores">
                                                            @else
                                                                <span class="align-middle"
                                                                    wire:click="detectarEdicionMonitores('{{ $evento->id }}', '{{ $servicio->id }}', '{{ $monitoresIndex }}', 'gasto_gasoil')">
                                                                    @if (!empty(json_decode($servicio->pivot->gasto_gasoil, true)))
                                                                        {{ json_decode($servicio->pivot->gasto_gasoil, true)[$monitoresIndex] }}
                                                                        €
                                                                    @endif
                                                                </span>
                                                            @endif
                                                        @else
                                                            <span class="align-middle"
                                                                wire:click="detectarEdicionMonitores('{{ $evento->id }}', '{{ $servicio->id }}', '{{ $monitoresIndex }}', 'gasto_gasoil')">
                                                                @if (!empty(json_decode($servicio->pivot->gasto_gasoil, true)))
                                                                    {{ json_decode($servicio->pivot->gasto_gasoil, true)[$monitoresIndex] }}
                                                                    €
                                                                @endif
                                                            </span>
                                                        @endif
                                                    </td>
                                                </tr>
                                            @endforeach
                                        @endforeach
                                        @foreach ($presupuestos->where('id_evento', $evento->id)->first()->packs()->get() as $packIndex => $pack)
                                            <tr>
                                                <th>
                                                    {{ $pack->nombre }}</td>
                                                <th colspan="7">
                                                    </td>
                                            </tr>
                                            @foreach ($pack->servicios()->get() as $servicioIndex => $servicio)
                                                @foreach (json_decode($pack->pivot->id_monitores, true)[$servicioIndex] as $monitoresIndex => $monitores)
                                                    @if ($monitoresIndex == 0)
                                                        <tr>
                                                            <td>
                                                                @if ($monitoresIndex == 0)
                                                                    {{ $servicio->nombre }}
                                                                @endif
                                                            </td>
                                                            <td>
                                                                @if ($datoEdicion['id'] != null)
                                                                    @if (
                                                                        $datoEdicion['id']['presupuesto'] == $evento->id &&
                                                                            $datoEdicion['id']['pack'] == $pack->id &&
                                                                            $datoEdicion['id']['servicio'] == $servicioIndex &&
                                                                            $datoEdicion['column'] == 'packHoraMontaje')
                                                                        <input type="number"
                                                                            wire:model.lazy="datoEdicion.value"
                                                                            wire:change.lazy="terminarEdicionPack">
                                                                    @else
                                                                        <span class="align-middle"
                                                                            wire:click="detectarEdicionPack('{{ $evento->id }}', '{{ $pack->id }}', '{{ $servicioIndex }}', '{{ $monitoresIndex }}', 'packHoraMontaje')">
                                                                            @if (!empty(json_decode($pack->pivot->horas_montaje, true)))
                                                                                {{ json_decode($pack->pivot->horas_montaje, true)[$servicioIndex] }}
                                                                            @endif
                                                                        </span>
                                                                    @endif
                                                                @else
                                                                    <span class="align-middle"
                                                                        wire:click="detectarEdicionPack('{{ $evento->id }}', '{{ $pack->id }}', '{{ $servicioIndex }}', 'packHoraMontaje')">
                                                                        @if (!empty(json_decode($pack->pivot->horas_montaje, true)))
                                                                            {{ json_decode($pack->pivot->horas_montaje, true)[$servicioIndex] }}
                                                                        @endif
                                                                    </span>
                                                                @endif
                                                            </td>
                                                            <td>
                                                                @if ($datoEdicion['id'] != null)
                                                                    @if (
                                                                        $datoEdicion['id']['presupuesto'] == $evento->id &&
                                                                            $datoEdicion['id']['pack'] == $pack->id &&
                                                                            $datoEdicion['id']['servicio'] == $servicioIndex &&
                                                                            $datoEdicion['column'] == 'packHoraInicio')
                                                                        <input type="time"
                                                                            wire:model.lazy="datoEdicion.value"
                                                                            wire:change.lazy="terminarEdicionServicioPack">
                                                                    @else
                                                                        <span class="align-middle"
                                                                            wire:click="detectarEdicionPack('{{ $evento->id }}', '{{ $pack->id }}', '{{ $servicioIndex }}', 'packHoraInicio')">
                                                                            @if (!empty(json_decode($pack->pivot->horas_inicio, true)))
                                                                                {{ json_decode($pack->pivot->horas_inicio, true)[$servicioIndex] }}
                                                                            @endif
                                                                        </span>
                                                                    @endif
                                                                @else
                                                                    <span class="align-middle"
                                                                        wire:click="detectarEdicionPack('{{ $evento->id }}', '{{ $pack->id }}', '{{ $servicioIndex }}', 'packHoraInicio')">
                                                                        @if (!empty(json_decode($pack->pivot->horas_inicio, true)))
                                                                            {{ json_decode($pack->pivot->horas_inicio, true)[$servicioIndex] }}
                                                                        @endif
                                                                    </span>
                                                                @endif
                                                            </td>
                                                            <td>
                                                                @if ($datoEdicion['id'] != null)
                                                                    @if (
                                                                        $datoEdicion['id']['presupuesto'] == $evento->id &&
                                                                            $datoEdicion['id']['pack'] == $pack->id &&
                                                                            $datoEdicion['id']['servicio'] == $servicioIndex &&
                                                                            $datoEdicion['column'] == 'packHoraTiempo')
                                                                        <input type="time"
                                                                            wire:model.lazy="datoEdicion.value"
                                                                            wire:change.lazy="terminarEdicionServicioPack">
                                                                    @else
                                                                        <span class="align-middle"
                                                                            wire:click="detectarEdicionPack('{{ $evento->id }}', '{{ $pack->id }}', '{{ $servicioIndex }}', 'packHoraTiempo')">
                                                                            @if (!empty(json_decode($pack->pivot->tiempos, true)))
                                                                                {{ json_decode($pack->pivot->tiempos, true)[$servicioIndex] }}
                                                                            @endif
                                                                        </span>
                                                                    @endif
                                                                @else
                                                                    <span class="align-middle"
                                                                        wire:click="detectarEdicionPack('{{ $evento->id }}', '{{ $pack->id }}', '{{ $servicioIndex }}', 'packHoraTiempo')">
                                                                        @if (!empty(json_decode($pack->pivot->tiempos, true)))
                                                                            {{ json_decode($pack->pivot->tiempos, true)[$servicioIndex] }}
                                                                        @endif
                                                                    </span>
                                                                @endif
                                                            </td>
                                                            <td>
                                                                @if ($datoEdicion['id'] != null)
                                                                    @if (
                                                                        $datoEdicion['id']['presupuesto'] == $evento->id &&
                                                                            $datoEdicion['id']['pack'] == $pack->id &&
                                                                            $datoEdicion['id']['servicio'] == $servicioIndex &&
                                                                            $datoEdicion['column'] == 'packHoraTiempoMontaje')
                                                                        <input type="time"
                                                                            wire:model.lazy="datoEdicion.value"
                                                                            wire:change.lazy="terminarEdicionServicioPack">
                                                                    @else
                                                                        <span class="align-middle"
                                                                            wire:click="detectarEdicionPack('{{ $evento->id }}', '{{ $pack->id }}', '{{ $servicioIndex }}', 'packHoraTiempoMontaje')">
                                                                            @if (!empty(json_decode($pack->pivot->tiempos_montaje, true)))
                                                                                {{ json_decode($pack->pivot->tiempos_montaje, true)[$servicioIndex] }}
                                                                            @endif
                                                                        </span>
                                                                    @endif
                                                                @else
                                                                    <span class="align-middle"
                                                                        wire:click="detectarEdicionPack('{{ $evento->id }}', '{{ $pack->id }}', '{{ $servicioIndex }}', 'packHoraTiempoMontaje')">
                                                                        @if (!empty(json_decode($pack->pivot->tiempos_montaje, true)))
                                                                            {{ json_decode($pack->pivot->tiempos_montaje, true)[$servicioIndex] }}
                                                                        @endif
                                                                    </span>
                                                                @endif
                                                            </td>
                                                        @else
                                                            <td colspan="5">&nbsp;</td>
                                                    @endif
                                                    <td>
                                                        @if ($datoEdicion['id'] != null && isset($datoEdicion['monitor']))
                                                            @if (
                                                                $datoEdicion['id']['presupuesto'] == $evento->id &&
                                                                    $datoEdicion['id']['servicio'] == $servicioIndex &&
                                                                    $datoEdicion['id']['monitor'] == $monitoresIndex &&
                                                                    $datoEdicion['column'] == 'monitorNombrePack')
                                                                <div class="col-md-8" x-data=""
                                                                    x-init="$('#select2-monitorPack').select2();
                                                                    $('#select2-monitorPack').on('change', function(e) {
                                                                        var data = $('#select2-monitorPack').select2('val');
                                                                        @this.set('datoEdicion['
                                                                            value ']', data);
                                                                    });" wire:key='rand()'>
                                                                    <select class="form-control" name="servicioNombre"
                                                                        id="select2-monitorPack"
                                                                        wire:model.lazy="datoEdicion.value"
                                                                        wire:change.lazy='terminarEdicionMonitoresPack'>
                                                                        <option value="0">-- ELIGE UN SERVICIO
                                                                            --
                                                                        </option>
                                                                        @foreach ($monitores_datos as $monitor_select)
                                                                            <option
                                                                                value="{{ $monitor_select->id }}">
                                                                                {{ $monitor_select->alias }}
                                                                            </option>
                                                                        @endforeach
                                                                    </select>
                                                                </div>
                                                            @else
                                                                <span class="align-middle"
                                                                    wire:click="detectarEdicionMonitoresPack('{{ $evento->id }}', '{{ $pack->id }}', '{{ $servicioIndex }}', '{{ $monitoresIndex }}', 'monitorNombrePack')">{{ $this->getMonitor($monitores) }}"
                                                                </span>
                                                            @endif
                                                        @else
                                                            <span class="align-middle"
                                                                wire:click="detectarEdicionMonitoresPack('{{ $evento->id }}', '{{ $pack->id }}', '{{ $servicioIndex }}', '{{ $monitoresIndex }}', 'monitorNombrePack')">{{ $this->getMonitor($monitores) }}</span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if ($datoEdicion['id'] != null && isset($datoEdicion['monitor']))
                                                            @if (
                                                                $datoEdicion['id']['presupuesto'] == $evento->id &&
                                                                    $datoEdicion['id']['servicio'] == $servicioIndex &&
                                                                    $datoEdicion['id']['monitor'] == $monitoresIndex &&
                                                                    $datoEdicion['column'] == 'sueldoMonitorPack')
                                                                <input type="number"
                                                                    wire:model.lazy="datoEdicion.value"
                                                                    wire:change.lazy="terminarEdicionMonitoresPack">
                                                            @else
                                                                <span class="align-middle"
                                                                    wire:click="detectarEdicionMonitoresPack('{{ $evento->id }}', '{{ $pack->id }}', '{{ $servicioIndex }}', '{{ $monitoresIndex }}', 'sueldoMonitorPack')">{{ json_decode($pack->pivot->sueldos_monitores, true)[$servicioIndex][$monitoresIndex] }}
                                                                    €
                                                                </span>
                                                            @endif
                                                        @else
                                                            <span class="align-middle"
                                                                wire:click="detectarEdicionMonitoresPack('{{ $evento->id }}', '{{ $pack->id }}', '{{ $servicioIndex }}', '{{ $monitoresIndex }}', 'sueldoMonitorPack')">{{ json_decode($pack->pivot->sueldos_monitores, true)[$servicioIndex][$monitoresIndex] }}
                                                                €</span>
                                                        @endif

                                                    </td>
                                                    <td>0 €</td>
                                                    </tr>
                                                @endforeach
                                            @endforeach
                                        @endforeach
                                        <tr>
                                            <th colspan="8">Observaciones</th>
                                        </tr>
                                        <tr>
                                            <th colspan="8">
                                                {{ $presupuestos->where('id_evento', $evento->id)->first()->observaciones }}
                                            </th>
                                        </tr>
                                    </table>
                                @endforeach
                            @else
                                <h6 class="text-center">No hay eventos para este día.</h6>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card m-b-30 position-fixed">
                <div class="card-body">
                    <h5>Elige una semana para resumir</h5>
                    <div class="row">
                        <div class="col-12">
                            <input type="week" class="form-control" wire:model="semana"
                                wire:change="cambioSemana">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @section('scripts')
        <script>
            document.addEventListener('click', function(event) {
                // Si el elemento clickeado no es un input ni un td
                if (event.target.tagName !== 'INPUT' && event.target.tagName !== 'SPAN') {
                    Livewire.emit('terminarInputs')
                }
            });
        </script>
    @endsection
</div>
