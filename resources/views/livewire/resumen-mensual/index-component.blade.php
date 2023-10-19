<div class="container-fluid">
    <script src="//unpkg.com/alpinejs" defer></script>
    <div class="page-title-box">
        <div class="row align-items-center">
            <div class="col-sm-6">
                <h4 class="page-title">CUADRANTE MENSUAL</span></h4>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-right">
                    <li class="breadcrumb-item"><a href="javascript:void(0);">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="javascript:void(0);">Cuadrante</a></li>
                    <li class="breadcrumb-item active">Cuadrante mensual</li>
                </ol>
            </div>
        </div> <!-- end row -->
    </div>
    <div class="row">
        <div class="col-md-9">
            <div class="card m-b-30">
                <div class="card-body">
                    <div class="form-row">
                        <div class="form-group col-md-12">
                            <h4 class="ms-3" style="border-bottom: 1px gray solid !important; padding-bottom: 10px !important">Cuadrantes por semana</h4>
                        </div>
                        @if ($mes != null)
                            @foreach ($intervalosSemanas as $intervaloIndex => $intervalo)
                                <div class="col-md-4">
                                    <h5 class="ms-3" style="border-bottom: 1px gray solid !important; padding-bottom: 10px !important">({{$intervalosSemanas[$intervaloIndex]['inicio']}} a {{$intervalosSemanas[$intervaloIndex]['fin']}})</h5>
                                    <table class="table table-striped table-bordered">
                                        <tr width="100%">
                                            <th colspan="2" class="bg-primary text-white">
                                                <h5>Balance Eventos {{ $intervaloIndex + 1 }}ª Semana</h5>
                                            </th>
                                        </tr>
                                        <tr width="100%">
                                            <th width="50%">Total Ingresos Brutos </th>
                                            <th class="text-primary">{{ $this->ingresos_brutos[$intervaloIndex] }}
                                                €</th>
                                        </tr>
                                        <tr width="100%">
                                            <th width="50%">Total Líquido semanal </th>
                                            <th class="text-success">{{ $this->liquido_semanal[$intervaloIndex] }}
                                                €</th>
                                        </tr>
                                        <tr width="100%">
                                            <th width="50%">Adelanto de los eventos</th>
                                            <th class="text-warning">{{ $this->adelantos[$intervaloIndex] }}
                                                €</th>
                                        </tr>
                                        <tr width="100%">
                                            <th width="50%">Pendiente de cobro</th>
                                            <th class="text-danger">{{ $this->pendiente_cobrar[$intervaloIndex] }}
                                                €</th>
                                        </tr>
                                        <tr width="100%">
                                            <th width="50%">Salario monitores</th>
                                            <th class="text-danger">{{ $this->salario_monitores[$intervaloIndex] }}
                                                €</th>
                                        </tr>
                                        <tr width="100%">
                                            <th width="50%">Pte pago monitores</th>
                                            <th class="text-danger">{{ $this->pte_pago_monitores[$intervaloIndex] }}
                                                €</th>
                                        </tr>
                                        <tr width="100%">
                                            <th width="50%">Desplazamientos/Gasolina</th>
                                            <th class="text-success">{{ $this->gastos_gasoil[$intervaloIndex] }}
                                                €</th>
                                        </tr>
                                        <tr width="100%">
                                            <th width="50%">Seguros Sociales</th>
                                            <th class="text-success">{{ $this->seguros_sociales[$intervaloIndex] }}
                                                €</th>
                                        </tr>
                                        <tr width="100%">
                                            <th width="50%">Material fungible</th>
                                            <th class="text-success">{{ $this->material_fungible[$intervaloIndex] }}
                                                €</th>
                                        </tr>
                                        <tr width="100%">
                                            <th width="50%">Total gastos</th>
                                            <th class="text-success">{{ $this->total_gastos[$intervaloIndex] }}
                                                €</th>
                                        </tr>
                                        <tr width="100%" class="bg-primary text-white">
                                            <th width="50%">Rentabilidad</th>
                                            <th>{{ $this->liquido_semanal[$intervaloIndex] - $this->total_gastos[$intervaloIndex] }}
                                                €</th>
                                        </tr>
                                        <tr width="100%" class="bg-warning">
                                            <th width="50%">Otros gastos</th>
                                            <th>{{ $this->otros_gastos_total[$intervaloIndex] }}
                                                €</th>
                                        </tr>
                                        @foreach ($this->otros_gastos[$intervaloIndex] as $gasto)
                                            <tr width="100%">
                                                <th width="50%">
                                                    {{ $this->tipos_gasto->where('id', $gasto->tipo_gasto)->first()->nombre }}
                                                </th>
                                                <th>{{ $gasto->total }}
                                                    €</th>
                                            </tr>
                                        @endforeach
                                        <tr width="100%" class="bg-primary text-white border-primary">
                                            <th width="50%" class="text-white">Balance Neto Semanal</th>
                                            <th class="text-white">
                                                {{ $this->liquido_semanal[$intervaloIndex] - $this->total_gastos[$intervaloIndex] - $this->otros_gastos_total[$intervaloIndex] }}
                                                €</th>
                                        </tr>
                                    </table>
                                </div>
                            @endforeach
                            <div class="col-md-6">
                                <table class="table table-striped table-bordered">
                                    <tr width="100%">
                                        <th colspan="2" class="bg-primary text-white">
                                            <h5>Balance Eventos del Mes</h5>
                                        </th>
                                    </tr>
                                    <tr width="100%">
                                        <th width="50%">Total Ingresos Brutos </th>
                                        <th class="text-primary">{{ array_sum($this->ingresos_brutos) }}
                                            €</th>
                                    </tr>
                                    <tr width="100%">
                                        <th width="50%">Total Líquido semanal </th>
                                        <th class="text-success">{{ array_sum($this->liquido_semanal) }}
                                            €</th>
                                    </tr>
                                    <tr width="100%">
                                        <th width="50%">Adelanto de los eventos</th>
                                        <th class="text-warning">{{ array_sum($this->adelantos) }}
                                            €</th>
                                    </tr>
                                    <tr width="100%">
                                        <th width="50%">Pendiente de cobro</th>
                                        <th class="text-danger">{{ array_sum($this->pendiente_cobrar) }}
                                            €</th>
                                    </tr>
                                    <tr width="100%">
                                        <th width="50%">Salario monitores</th>
                                        <th class="text-danger">{{ array_sum($this->salario_monitores) }}
                                            €</th>
                                    </tr>
                                    <tr width="100%">
                                        <th width="50%">Pte pago monitores</th>
                                        <th class="text-danger">{{ array_sum($this->pte_pago_monitores) }}
                                            €</th>
                                    </tr>
                                    <tr width="100%">
                                        <th width="50%">Desplazamientos/Gasolina</th>
                                        <th class="text-success">{{ array_sum($this->gastos_gasoil) }}
                                            €</th>
                                    </tr>
                                    <tr width="100%">
                                        <th width="50%">Seguros Sociales</th>
                                        <th class="text-success">{{ array_sum($this->seguros_sociales) }}
                                            €</th>
                                    </tr>
                                    <tr width="100%">
                                        <th width="50%">Material fungible</th>
                                        <th class="text-success">{{ array_sum($this->material_fungible) }}
                                            €</th>
                                    </tr>
                                    <tr width="100%">
                                        <th width="50%">Total gastos</th>
                                        <th class="text-success">{{ array_sum($this->total_gastos) }}
                                            €</th>
                                    </tr>
                                    <tr width="100%" class="bg-primary text-white">
                                        <th width="50%">Rentabilidad</th>
                                        <th>{{ array_sum($this->liquido_semanal) - array_sum($this->total_gastos) }}
                                            €</th>
                                    </tr>
                                    <tr width="100%" class="bg-warning">
                                        <th width="50%">Otros gastos</th>
                                        <th>{{ array_sum($this->otros_gastos_total) }}
                                            €</th>
                                    </tr>
                                    @foreach ($this->otros_gastos as $gastoIntervalo)
                                        @foreach ($gastoIntervalo as $gasto)
                                            <tr width="100%">
                                                <th width="50%">
                                                    {{ $this->tipos_gasto->where('id', $gasto->tipo_gasto)->first()->nombre }}
                                                </th>
                                                <th>{{ $gasto->total }}
                                                    €</th>
                                            </tr>
                                        @endforeach
                                    @endforeach
                                    <tr width="100%" class="bg-primary text-white border-primary">
                                        <th width="50%" class="text-white">Balance Neto Mensual</th>
                                        <th class="text-white">
                                            {{ array_sum($this->liquido_semanal) - array_sum($this->total_gastos) - array_sum($this->otros_gastos_total) }}
                                            €</th>
                                    </tr>
                                </table>
                            </div>
                            <div class="col-md-6">
                                <table class="table table-striped table-bordered">
                                    <tr width="100%" class="bg-warning">
                                        <th colspan="2"><h5>GASTOS MENSUALES</h5></th>
                                    </tr>
                                    <tr width="100%">
                                        <th width="50%">SEMANAS DEL MES EN CURSO</th>
                                        <th style="background-color: lightblue !important;">{{ $this->semana_curso }}</th>
                                    </tr>
                                @foreach ($this->otros_gastos as $gastoIntervalo)
                                        @foreach ($gastoIntervalo as $gasto)
                                            <tr width="100%">
                                                <th width="50%">
                                                    {{ $this->tipos_gasto->where('id', $gasto->tipo_gasto)->first()->nombre }}
                                                </th>
                                                <th>{{ $gasto->total }}
                                                    €</th>
                                            </tr>
                                        @endforeach
                                    @endforeach
                                </table>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card m-b-30 position-fixed">
                <div class="card-body">
                    <h5>Selecciona un mes</h5>
                    <div class="row">
                        <div class="col-12">
                            <input type="month" class="form-control" wire:model='mes' wire:change='cambioMes'>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
