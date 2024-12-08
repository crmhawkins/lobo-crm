<div class="container-fluid">
    <div class="page-title-box">
        <div class="row align-items-center">
            <div class="col-sm-6">
                <h4 class="page-title">Control Presupuestario</h4>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-right">
                    <li class="breadcrumb-item"><a href="javascript:void(0);">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="javascript:void(0);">Control Presupuestario</a></li>
                </ol>
            </div>
        </div> <!-- end row -->
    </div>
    <!-- Menú de botones estilizado -->
    <div class="d-flex justify-content-center" style="padding-top: 50px;">
        <div class="btn-group-vertical" role="group" aria-label="Control Presupuestario" style="width: 300px;">
            <a href="{{ route('control-presupuestario.index') }}" class="btn btn-primary btn-lg mb-2">General</a>
            <a href="{{ route('control-presupuestario.ventas') }}" class="btn btn-primary btn-lg mb-2">Ventas</a>
            <a href="{{ route('control-presupuestario.gastos') }}" class="btn btn-primary btn-lg mb-2">Gastos</a>
            <a href="{{ route('control-presupuestario.compras') }}" class="btn btn-primary btn-lg mb-2">Compras</a>
            <a href="{{ route('control-presupuestario.logistica') }}" class="btn btn-primary btn-lg mb-2">Logística</a>
            <a href="{{ route('control-presupuestario.comerciales') }}" class="btn btn-primary btn-lg mb-2">Comerciales</a>
            <a href="{{ route('control-presupuestario.marketing') }}" class="btn btn-primary btn-lg mb-2">Marketing</a>
            <a href="{{ route('control-presupuestario.patrocinios') }}" class="btn btn-primary btn-lg mb-2">Patrocinios</a>
            <a href="{{ route('control-presupuestario.presupuestos-delegacion') }}" class="btn btn-primary btn-lg mb-2">Presupuestos Delegación</a>
            <a href="{{ route('control-presupuestario.ventas-delegaciones') }}" class="btn btn-primary btn-lg mb-2">Ventas Delegaciones</a>
            <a href="{{ route('control-presupuestario.ventas-por-productos') }}" class="btn btn-primary btn-lg mb-2">Ventas por Productos</a>
            <a href="{{ route('control-presupuestario.analisis-global') }}" class="btn btn-primary btn-lg mb-2">Análisis Global</a>
            <a href="{{ route('control-presupuestario.analisis-ventas') }}" class="btn btn-primary btn-lg mb-2">Análisis Ventas</a>
            <a href="{{ route('control-presupuestario.proyeccion') }}" class="btn btn-primary btn-lg mb-2">Proyección</a>
        </div>
    </div>

    <!-- Aquí puedes añadir el contenido específico de la vista show -->
    <div>
        <!-- Contenido de la vista -->
    </div>
</div>