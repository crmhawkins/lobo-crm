@php
$EsAdmin = Auth::user()->isAdmin();
$canEdit = $EsAdmin; //|| $estado == 1;
@endphp
{{-- {{ var_dump($eventoServicios) }} --}}
<div class="container-fluid">
    <div class="page-title-box">
        <div class="row align-items-center">
            <div class="col-sm-6">
                <h4 class="page-title">Opciones </h4>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-right">
                    <li class="breadcrumb-item"><a href="javascript:void(0);">Dashboard</a></li>
                    <li class="breadcrumb-item active">Opciones</li>
                </ol>
            </div>
        </div> <!-- end row -->
    </div>
    <!-- end page-title -->
    @php
    $mostrarElemento = Auth::user()->isAdmin();
    @endphp
    <div class="row" style="align-items: start !important">
        <div class="col-md-9">
            <div class="card m-b-30">
                <div class="card-body">
                    <form wire:submit.prevent="">
                        <input type="hidden" name="csrf-token" value="{{ csrf_token() }}">
                        <div class="form-group row">
                            <br>
                            <div class="col-sm-12">
                                <h5 class="ms-3"
                                    style="border-bottom: 1px gray solid !important; padding-bottom: 10px !important;">
                                    Opciones de Configuración</h5>
                            </div>
                            <div class="form-group col-sm-1 invisible">
                                &nbsp;
                            </div>
                            <div class="col-md-5">
                                <label for="example-text-input" class="col-sm-12 col-form-label">Cuenta del banco</label>
                                <div class="col-sm-12">
                                    <input type="text" wire:model="cuenta" class="form-control" name="cuenta"
                                        id="cuenta" placeholder="cuenta" @if(!$canEdit) disabled @endif>
                                    @error('cuenta')
                                        <span class="text-danger">{{ $message }}</span>
                                        <style>
                                            .nombre {
                                                color: red;
                                            }
                                        </style>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-5">
                                <label for="example-text-input" class="col-sm-12 col-form-label">Departamentos Proveedores</label>
                                <div class="col-sm-12">
                                    <div class="d-flex gap-2">
                                        <input type="text" wire:model="nombreDepartamento" class="form-control mb-1" name="nombreDepartamento"
                                            id="nombreDepartamento" placeholder="nombre Departamento" @if(!$canEdit) disabled @endif>
                                        @error('departamento')
                                            <span class="text-danger">{{ $message }}</span>
                                            <style>
                                                .nombre {
                                                    color: red;
                                                }
                                            </style>
                                        @enderror
                                    

                                        <button class="btn btn-primary" wire:click="addDepartamento">Añadir</button>
                                    </div>
                                    @if(count($departamentos) > 0)
                                        <table class="table table-striped">
                                            <thead>
                                                <tr>
                                                    <th>Nombre</th>
                                                    <th>Acciones</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($departamentos as $departamento)
                                                    <tr>
                                                        <td>{{ $departamento->nombre }}</td>
                                                        <td>
                                                            <button class="btn btn-danger"
                                                                wire:click="removeDepartamento({{ $departamento->id }})">Eliminar</button>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    @endif
                                </div>

                            </div>
                            
                        </div>
                        
                            
                       
                            
                       
                    </form>
                </div>
            </div>
        
        </div>
        @if($canEdit)
            <div class="col-md-3">
                <div class="card m-b-30">
                    <div class="card-body">
                        <h5>Acciones</h5>
                        <div class="row">
                            <div class="col-12">
                                <button class="w-100 btn btn-success mb-2" id="alertaGuardar">Guardar
                                    Opciones</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>

    @section('scripts')
    <script src="../assets/js/jquery.slimscroll.js"></script>
    <script>
        $("#alertaGuardar").on("click", () => {
            Swal.fire({
                title: '¿Estás seguro?',
                text: 'Pulsa el botón de confirmar para cambiar los datos del cliente.',
                icon: 'warning',
                showConfirmButton: true,
                showCancelButton: true
            }).then((result) => {
                if (result.isConfirmed) {
                    window.livewire.emit('update');
                }
            });
        });
    </script>
    @endsection

