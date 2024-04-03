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

{{-- <script src="../plugins/datatables/jquery.dataTables.min.js"></script> --}}
<script src="../plugins/datatables/dataTables.bootstrap4.min.js"></script>
<!-- Buttons examples -->
{{-- <script src="../plugins/datatables/dataTables.buttons.min.js"></script> --}}
<script src="../plugins/datatables/buttons.bootstrap4.min.js"></script>
{{-- <script src="../plugins/datatables/jszip.min.js"></script> --}}
{{-- <script src="../plugins/datatables/pdfmake.min.js"></script> --}}
 {{-- <script src="../plugins/datatables/vfs_fonts.js"></script> --}}
{{-- <script src="../plugins/datatables/buttons.html5.min.js"></script> --}}
<script src="../plugins/datatables/buttons.colVis.min.js"></script>
<!-- Responsive examples -->
<script src="../plugins/datatables/dataTables.responsive.min.js"></script>
<script src="../plugins/datatables/responsive.bootstrap4.min.js"></script>
<script src="../assets/pages/datatables.init.js"></script>
<!-- test examples -->
<script src="https://cdn.datatables.net/2.0.3/js/dataTables.js"></script>
<script src="https://cdn.datatables.net/buttons/3.0.1/js/dataTables.buttons.js"></script>
<script src="https://cdn.datatables.net/buttons/3.0.1/js/buttons.dataTables.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>
<script src="https://cdn.datatables.net/buttons/3.0.1/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/3.0.1/js/buttons.print.min.js"></script>

@endsection
