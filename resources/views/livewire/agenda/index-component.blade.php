<div class="container-fluid">
    <script src="//unpkg.com/alpinejs" defer></script>
    <?php

    use Carbon\Carbon;
    ?>
    <div class="page-title-box">
        <div class="row align-items-center">
            <div class="col-sm-6">
                <h4 class="page-title">AGENDA</span></h4>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-right">
                    <li class="breadcrumb-item"><a href="javascript:void(0);">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="javascript:void(0);">Agenda</a></li>
                </ol>
            </div>
        </div> <!-- end row -->
    </div>
    <div class="row">
        <div class="col-md-9">
            <div class="card m-b-30">
                <div class="card-body row">
                    @foreach ($dias as $diaIndex => $dia)
                        <div class="form-group col-md-4">
                            <h5 class="ms-3"
                                style="border-bottom: 1px gray solid !important; padding-bottom: 10px !important;">
                                {{ $dia }}</h5>
                            <div class="form-group col-md-12">
                                <div class="form-row">
                                    @if ($eventos->where('diaEvento', $fechas[$diaIndex])->count() > 0)
                                        <ul>
                                            @foreach ($eventos->where('diaEvento', $fechas[$diaIndex]) as $evento)
                                                <li><a href="{{route('presupuestos.edit', $presupuestos->where("id_evento", $evento->id)->first()->id)}}"> (#{{ $presupuestos->where('id_evento', $evento->id)->first()->nPresupuesto }})
                                                    {{ $categorias->find($evento->eventoNombre)->nombre }} -
                                                    {{ $this->getCliente($evento->id) }}</a></li>
                                            @endforeach
                                        </ul>
                                    @else
                                        <h6 class="text-center">No hay eventos para este día.</h6>
                                    @endif
                                </div>
                            </div>
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
                            <input type="week" class="form-control" wire:model="semana" wire:change="cambioSemana">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
