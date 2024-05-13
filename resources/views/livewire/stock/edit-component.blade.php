<div class="container-fluid">
    <style>
        @media(max-width: 760px){
                .unidades > div > div{
                    min-width: 100px;
                }
        }
        </style>
    <div class="page-title-box">
        <div class="row align-items-center">
            <div class="col-sm-6">
                <h4 class="page-title">EDITAR STOCK</span></h4>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-right">
                    <li class="breadcrumb-item"><a href="javascript:void(0);">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="javascript:void(0);">Stock</a></li>
                    <li class="breadcrumb-item active">Editar stock</li>
                </ol>
            </div>
        </div>
    </div>
    <div class="row" style="align-items: start !important">
        <div class="col-md-9">
            <div class="card m-b-30">
                <div class="card-body">
                    <div class="form-row justify-content-center">
                        <div class="form-group col-md-12">
                            <h5 class="ms-3"
                                style="border-bottom: 1px gray solid !important; padding-bottom: 10px !important;">Datos
                                básicos </h5>
                        </div>
                    </div>
                    <div class="form-row justify-content-center">
                        <div class="form-group col-md-11">
                            @if (isset($almacenActual))
                            <h4> Almacén: {{ $almacenActual->almacen }}
                            @else
                            <h4> Almacén: No asignado
                            @endif
                            </h4>
                        </div>
                    </div>
                    <div class="form-row justify-content-center">
                        <div class="form-group col-md-3">
                            <label for="Qr">Identificador del QR</label>
                            <input type="text" wire:model="qr_id" class="form-control" disabled>
                        </div>
                        <div class="form-group col-md-3" wire:ignore>
                            <div x-data="" x-init="$('#select2-estado').select2();
                            $('#select2-estado').on('change', function(e) {
                                var data = $('#select2-estado').select2('val');
                                @this.set('estado', data);
                            });">
                                <label for="Estado">Estado</label>
                                <select class="form-control" name="estado" id="select2-estado" wire:model="estado">
                                    <option value="0">Stock completo</option>
                                    <option value="1">Stock parcial</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group col-md-3">
                            <label for="fecha">Fecha</label>
                            <input type="date" wire:model="fecha" class="form-control" disabled>
                        </div>
                    </div>
                    <div class="form-row justify-content-center">
                        <div class="form-group col-md-10">
                            <label for="fecha">Observaciones</label>
                            <textarea wire:model="observaciones" class="form-control"></textarea>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card m-b-30">
                <div class="card-body">
                    <div class="form-row justify-content-center">
                        <div class="form-group col-md-12">
                            <h5 class="ms-3"
                                style="border-bottom: 1px gray solid !important;padding-bottom: 10px !important;display: flex !important;flex-direction: row;justify-content: space-between;">
                                Stock</h5>
                            <div class="form-group col-md-12">
                                <div class="table-responsive">
                                    <table class="table  table-striped table-bordered dt-responsive nowrap">
                                        <thead>
                                            <tr>
                                                <th>Producto</th>
                                                <th>N.º interno</th>
                                                <th>N.º Lote</th>
                                                <th>Cantidad</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                                <tr>
                                                    <td width="25%">
                                                        {{$this->getNombreTabla($this->stockentrante->producto_id) }}
                                                    </td>
                                                    <td width="20%">
                                                        {{$this->stockentrante->lote_id  }}
                                                    </td>
                                                    <td width="20%">
                                                        {{$this->stockentrante->orden_numero }}
                                                    </td>
                                                    <td width="35%" class="unidades">
                                                        <div class="row align-items-center">
                                                            <div class="col-8 text-end">
                                                                <input type="number" class="form-control" wire:model="cantidad">
                                                            </div>
                                                            <div class="col-4 text-start">
                                                                <p class="my-auto">Unidades</p>
                                                            </div>
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
        <div class="col-md-3" style="width: 23vw !important;">
            <div class="card m-b-30 position-fixed" style="width: -webkit-fill-available">
                <div class="card-body">
                    <h5>Opciones </h5>
                    <div class="row">
                        <div class="col-12">
                            <button class="w-100 btn btn-success mb-2" wire:click.prevent="update">Actualizar stock</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <style>
            fieldset.scheduler-border {
                border: 1px groove #ddd !important;
                padding: 0 1.4em 1.4em 1.4em !important;
                margin: 0 0 1.5em 0 !important;
                -webkit-box-shadow: 0px 0px 0px 0px #000;
                box-shadow: 0px 0px 0px 0px #000;
            }

            table {
                border: 1px black solid !important;
            }

            th {
                border-bottom: 1px black solid !important;
                border: 1px black solid !important;
                border-top: 1px black solid !important;
            }

            th.header {
                border-bottom: 1px black solid !important;
                border: 1px black solid !important;
                border-top: 2px black solid !important;
            }

            td.izquierda {
                border-left: 1px black solid !important;

            }

            td.derecha {
                border-right: 1px black solid !important;

            }

            td.suelo {}
        </style>
        <script>
            window.addEventListener('initializeMapKit', () => {
                fetch('/admin/service/jwt')
                    .then(response => response.json())
                    .then(data => {
                        mapkit.init({
                            authorizationCallback: function(done) {
                                done(data.token);
                            }
                        });
                        // Aquí puedes inicializar tu mapa u otras funcionalidades relacionadas
                    });
            });
        </script>
    </div>

    @section('scripts')

        <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>

        </script>
    @endsection
