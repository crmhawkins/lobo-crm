<div class="container-fluid">
    <div class="page-title-box">
        <div class="row align-items-center">
            <div class="col-sm-6">
                <h4 class="page-title">Bienvenido, {{ Auth::user()->name }}</h4>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-right">
                    <li class="breadcrumb-item"><a href="javascript:void(0);">Dashboard</a></li>
                </ol>
            </div>
        </div> <!-- end row -->
    </div>
    <!-- end page-title -->
    <div class="row">
        <div class="col-12 mb-3">
            @if ($jornada_activa == 1)
                <button class="btn btn-lg btn-danger w-100" wire:click="finalizarJornada">FINALIZAR JORNADA</button>
                @if ($pausa_activa == 1)
                    <br><button class="btn btn-lg btn-danger w-100" wire:click="finalizarPausa">FINALIZAR PAUSA</button>
                @else
                    <button class="btn btn-lg btn-primary mt-3 w-100" wire:click="iniciarPausa">INICIAR PAUSA</button>
                @endif
            @else
                <button class="btn btn-lg btn-primary w-100" wire:click="iniciarJornada">INICIAR JORNADA</button>
            @endif
        </div>
    </div>
    <div class="row d-flex justify-content-around">
        <div class="col-12 col-xl-3">
            <div class="card">
                <div class="card-heading p-4">
                    <div>
                        <h5 class="font-18">Horas trabajadas hoy</h5>
                    </div>
                    <h5 class="font-24 mt-4">{{ $this->getHorasTrabajadas('Hoy') }}</h3>
                </div>
            </div>
        </div>
        <div class="col-12 col-xl-3">
            <div class="card">
                <div class="card-heading p-4">
                    <div>
                        <h5 class="font-18">Horas trabajadas esta semana</h5>
                    </div>
                    <h5 class="font-24 mt-4">{{ $this->getHorasTrabajadas('Semana') }}</h3>
                </div>
            </div>
        </div>
    </div>
</div>
</div>


@section('scripts')
<script src="../assets/js/jquery.slimscroll.js"></script>
<link href="https://cdn.datatables.net/v/bs5/jq-3.7.0/jszip-3.10.1/dt-2.0.3/b-3.0.1/b-colvis-3.0.1/b-html5-3.0.1/b-print-3.0.1/r-3.0.1/datatables.min.css" rel="stylesheet">
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>
<script src="https://cdn.datatables.net/v/bs5/jq-3.7.0/jszip-3.10.1/dt-2.0.3/b-3.0.1/b-colvis-3.0.1/b-html5-3.0.1/b-print-3.0.1/r-3.0.1/datatables.min.js"></script>
<!-- Responsive examples -->
<script src="../assets/pages/datatables.init.js"></script>
@endsection
