<div class="container-fluid">
    <div class="page-title-box">
        <div class="row align-items-center">
            <div class="col-sm-6">
                <h4 class="page-title">Alertas</h4>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-right">
                    <li class="breadcrumb-item"><a href="javascript:void(0);">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="javascript:void(0);">Alertas</a></li>
                    <li class="breadcrumb-item active">Todas las alertas</li>
                </ol>
            </div>
        </div> <!-- end row -->
    </div>
    <!-- end page-title -->
    <div class="row">
        <div class="col-12">
            <div class="card m-b-30">
                <div class="card-body">
                    <h4 class="mt-0 header-title">Listado de todas las alertas</h4>
                    <p class="sub-title">Listado completo de todas las alertas.</p>

                    {{-- Aqui va la tabla de alertas con los campos titulo, descripcion e imagen--}}
 <!-- Botón para abrir el modal -->
 <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#enviarAlertaModal">
    Enviar Alerta
</button>

<!-- Modal para enviar alerta -->
<div class="modal fade" id="enviarAlertaModal" tabindex="-1" aria-labelledby="enviarAlertaModalLabel" aria-hidden="true" wire:ignore.self>
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="enviarAlertaModalLabel">Enviar Alerta</h5>
                <button type="button" class="btn-close" data-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form wire:submit.prevent="enviarAlerta">
                    <div class="mb-3">
                        <label for="tituloAlerta" class="form-label">Título</label>
                        <input type="text" class="form-control" id="tituloAlerta" wire:model="titulo" required>
                    </div>

                    <div class="mb-3">
                        <label for="descripcionAlerta" class="form-label">Descripción</label>
                        <textarea class="form-control" id="descripcionAlerta" rows="3" wire:model="descripcion" required></textarea>
                    </div>

                    <div class="mb-3">
                        <label for="imagenAlerta" class="form-label">Imagen (opcional)</label>
                        <input type="file" class="form-control" id="imagenAlerta" wire:model="imagen">
                    </div>

                    <div class="mb-3">
                        <label for="usuariosAlerta" class="form-label">Enviar a</label>
                        <select id="usuariosAlerta" class="form-control select2" multiple="multiple" wire:model="usuariosSeleccionados" wire:ignore.self>
                            @foreach($usuarios as $usuario)
                                <option value="{{ $usuario->id }}">{{ $usuario->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary">Enviar Alerta</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
                    <table id="alertasTable" class="table table-striped table-bordered dt-responsive nowrap" style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                        <thead>
                            <tr>
                                <th scope="col">Titulo</th>
                                <th scope="col">Descripcion</th>
                                <th scope="col">Imagen</th>
                                <th scope="col">Usuario</th>
                                <th scope="col">Fecha</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($alertas as $alerta)
                                <tr>
                                    <td>{{ $alerta->titulo }}</td>
                                    <td>{{ $alerta->descripcion }}</td>
                                    <td><img class="table-img" src="{{ asset('storage/' . $alerta->imagen) }}" alt="Imagen de la alerta" style="width: 100px; height: 100px;"></td>
                                    <td>{{ $this->getNombreUsuario($alerta->user_id) }}</td>
                                    <td>{{ $alerta->created_at->format('d/m/Y H:i') }}</td>
                                    {{-- Eliminar --}}

                                </tr>
                            @endforeach
                        </tbody>
                    </table>



                </div>
            </div>
        </div>

    </div> <!-- end row -->
    <div id="imageModal" class="image-modal">
        <span class="close" onclick="closeModal()">&times;</span>
        <img class="modal-content" id="modalImage">
    </div>
    <style>

        .table-img {
            cursor: pointer;
            height: 100px;
            width: 100px;
            object-fit: cover;
            border-radius: 5px;
        }

      .image-modal {
    display: none;
    position: fixed;
    z-index: 1000;
    padding-top: 50px;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    overflow: auto;
    background-color: rgba(0, 0, 0, 0.9);
}

.image-modal .close {
    position: absolute;
    top: 15px;
    right: 35px;
    color: #fff;
    font-size: 40px;
    font-weight: bold;
    cursor: pointer;
}

.modal-content {
    display: block;
    margin: auto;
    max-width: 90%;
    max-height: 90%;
    animation-name: zoom;
    animation-duration: 0.6s;
}

@keyframes zoom {
    from {transform: scale(0)}
    to {transform: scale(1)}
}

    </style>
</div>




@section('scripts')
<script src="../assets/js/jquery.slimscroll.js"></script>
<link href="https://cdn.datatables.net/v/bs5/jq-3.7.0/jszip-3.10.1/dt-2.0.3/b-3.0.1/b-colvis-3.0.1/b-html5-3.0.1/b-print-3.0.1/r-3.0.1/datatables.min.css" rel="stylesheet">
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>
<script src="https://cdn.datatables.net/v/bs5/jq-3.7.0/jszip-3.10.1/dt-2.0.3/b-3.0.1/b-colvis-3.0.1/b-html5-3.0.1/b-print-3.0.1/r-3.0.1/datatables.min.js"></script>
<!-- Responsive examples -->
{{-- <script src="../assets/pages/datatables.init.js"></script> --}}
<script>
    // Abrir modal cuando se hace clic en la imagen
    document.querySelectorAll('.table-img').forEach(img => {
        img.addEventListener('click', function() {
            openModal(this.src);
        });
    });

    function openModal(src) {
        const modal = document.getElementById("imageModal");
        const modalImage = document.getElementById("modalImage");
        modal.style.display = "block";
        modalImage.src = src;
    }

    function closeModal() {
        const modal = document.getElementById("imageModal");
        modal.style.display = "none";
    }

    // Cerrar modal si se hace clic fuera de la imagen
    window.onclick = function(event) {
        const modal = document.getElementById("imageModal");
        if (event.target == modal) {
            modal.style.display = "none";
        }
    }
</script>

<script>
    document.addEventListener('livewire:load', function () {
        // Inicializar Select2
        $('#usuariosAlerta').select2({
            placeholder: "Seleccionar usuarios",
            allowClear: true
        });

        // Escuchar cambios en el selector y sincronizarlos con Livewire
        $('#usuariosAlerta').on('change', function () {
            var data = $(this).val();
            @this.set('usuariosSeleccionados', data);
        });

        // Actualizar select2 cuando Livewire cambie el estado
        Livewire.hook('message.processed', () => {
            $('#usuariosAlerta').select2({
                placeholder: "Seleccionar usuarios",
                allowClear: true
            });
        });


    });
</script>
@endsection
