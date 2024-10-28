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
@endsection
