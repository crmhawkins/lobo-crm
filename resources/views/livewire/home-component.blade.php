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
    <div class="row">
        <div class="col-12 col-xl-12">
            <div class="card m-b-30">
                <div class="card-body">
                    <h4 class="mt-0 header-title mb-4">Pedidos por completar</h4>
                    <div class="table-responsive">
                        <table class="table table-hover" style="white-space: nowrap !important">
                            <thead>
                                <tr>
                                    <th scope="col">Name</th>
                                    <th scope="col">Status</th>
                                    <th scope="col">Amount</th>
                                    <th scope="col">Contact</th>
                                    <th scope="col">Location</th>
                                    <th scope="col" colspan="2">Date</th>

                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>Philip Smead</td>
                                    <td><span class="badge badge-success">Delivered</span></td>
                                    <td>$9,420,000</td>
                                    <td>
                                        <div>
                                            <img src="assets/images/users/user-2.jpg" alt=""
                                                class="thumb-md rounded-circle mr-2"> Philip Smead
                                        </div>
                                    </td>
                                    <td>Houston, TX 77074</td>
                                    <td>15/1/2018</td>

                                    <td>
                                        <div>
                                            <a href="#" class="btn btn-primary btn-sm">Edit</a>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Brent Shipley</td>
                                    <td><span class="badge badge-warning">Pending</span></td>
                                    <td>$3,120,000</td>
                                    <td>
                                        <div>
                                            <img src="assets/images/users/user-3.jpg" alt=""
                                                class="thumb-md rounded-circle mr-2"> Brent Shipley
                                        </div>
                                    </td>
                                    <td>Oakland, CA 94612</td>
                                    <td>16/1/2019</td>

                                    <td>
                                        <div>
                                            <a href="#" class="btn btn-primary btn-sm">Edit</a>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Robert Sitton</td>
                                    <td><span class="badge badge-success">Delivered</span></td>
                                    <td>$6,360,000</td>
                                    <td>
                                        <div>
                                            <img src="assets/images/users/user-4.jpg" alt=""
                                                class="thumb-md rounded-circle mr-2"> Robert Sitton
                                        </div>
                                    </td>
                                    <td>Hebron, ME 04238</td>
                                    <td>17/1/2019</td>

                                    <td>
                                        <div>
                                            <a href="#" class="btn btn-primary btn-sm">Edit</a>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Alberto Jackson</td>
                                    <td><span class="badge badge-danger">Cancel</span></td>
                                    <td>$5,200,000</td>
                                    <td>
                                        <div>
                                            <img src="assets/images/users/user-5.jpg" alt=""
                                                class="thumb-md rounded-circle mr-2"> Alberto Jackson
                                        </div>
                                    </td>
                                    <td>Salinas, CA 93901</td>
                                    <td>18/1/2019</td>

                                    <td>
                                        <div>
                                            <a href="#" class="btn btn-primary btn-sm">Edit</a>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>David Sanchez</td>
                                    <td><span class="badge badge-success">Delivered</span></td>
                                    <td>$7,250,000</td>
                                    <td>
                                        <div>
                                            <img src="assets/images/users/user-6.jpg" alt=""
                                                class="thumb-md rounded-circle mr-2"> David Sanchez
                                        </div>
                                    </td>
                                    <td>Cincinnati, OH 45202</td>
                                    <td>19/1/2019</td>

                                    <td>
                                        <div>
                                            <a href="#" class="btn btn-primary btn-sm">Edit</a>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                </div>
            </div>
        </div>

    </div>
</div>
</div>


@section('scripts')
    <script src="../assets/js/jquery.slimscroll.js"></script>

    <script src="../plugins/datatables/jquery.dataTables.min.js"></script>
    <script src="../plugins/datatables/dataTables.bootstrap4.min.js"></script>
    <!-- Buttons examples -->
    <script src="../plugins/datatables/dataTables.buttons.min.js"></script>
    <script src="../plugins/datatables/buttons.bootstrap4.min.js"></script>
    <script src="../plugins/datatables/jszip.min.js"></script>
    <script src="../plugins/datatables/pdfmake.min.js"></script>
    <script src="../plugins/datatables/vfs_fonts.js"></script>
    <script src="../plugins/datatables/buttons.html5.min.js"></script>
    <script src="../plugins/datatables/buttons.print.min.js"></script>
    <script src="../plugins/datatables/buttons.colVis.min.js"></script>
    <!-- Responsive examples -->
    <script src="../plugins/datatables/dataTables.responsive.min.js"></script>
    <script src="../plugins/datatables/responsive.bootstrap4.min.js"></script>
    <script src="../assets/pages/datatables.init.js"></script>
@endsection
