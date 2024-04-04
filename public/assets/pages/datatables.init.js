/*
 Template Name: Stexo - Responsive Bootstrap 4 Admin Dashboard
 Author: Themesdesign
 Website: www.themesdesign.in
 File: Datatable js
 */

$(document).ready(function() {
    $('#datatable').DataTable();

    var tableEnviados = $('#datatable-buttons_enviados').DataTable({
        layout: {
            topStart: 'buttons'
        },
        lengthChange: false,
        pageLength: 30,
        buttons: ['copy', 'excel', 'pdf', 'colvis'],
        responsive: true,
        "language": {
            "lengthMenu": "Mostrando _MENU_ registros por página",
            "zeroRecords": "Nothing found - sorry",
            "info": "Mostrando página _PAGE_ of _PAGES_",
            "infoEmpty": "No hay registros disponibles",
            "infoFiltered": "(filtrado de _MAX_ total registros)",
            "search": "Buscar:",
            "paginate": {
                "first": "Primero",
                "last": "Ultimo",
                "next": "<i class='fa-solid fa-arrow-right w-100'></i>",
                "previous": "<i class='fa-solid fa-arrow-left w-100'></i>"
            },
            "zeroRecords": "No se encontraron registros coincidentes",
        }
    });
    var tablePreparacion = $('#datatable-buttons_preparacion').DataTable({
        layout: {
            topStart: 'buttons'
        },
        lengthChange: false,
        pageLength: 30,
        buttons: ['copy', 'excel', 'pdf', 'colvis'],
        responsive: true,
        "language": {
            "lengthMenu": "Mostrando _MENU_ registros por página",
            "zeroRecords": "Nothing found - sorry",
            "info": "Mostrando página _PAGE_ of _PAGES_",
            "infoEmpty": "No hay registros disponibles",
            "infoFiltered": "(filtrado de _MAX_ total registros)",
            "search": "Buscar:",
            "paginate": {
                "first": "Primero",
                "last": "Ultimo",
                "next": "<i class='fa-solid fa-arrow-right w-100'></i>",
                "previous": "<i class='fa-solid fa-arrow-left w-100'></i>"
            },
            "zeroRecords": "No se encontraron registros coincidentes",
        }
    });

    var table = $('#datatable-buttons').DataTable({
        layout: {
        topStart: 'buttons'
    },
        lengthChange: false,
        pageLength: 30,
        buttons: ['copy', 'excel', 'pdf', 'colvis'],
        responsive: true,
        "language": {
            "lengthMenu": "Mostrando _MENU_ registros por página",
            "zeroRecords": "Nothing found - sorry",
            "info": "Mostrando página _PAGE_ of _PAGES_",
            "infoEmpty": "No hay registros disponibles",
            "infoFiltered": "(filtrado de _MAX_ total registros)",
            "search": "Buscar:",
            "paginate": {
                "first": "Primero",
                "last": "Ultimo",
                "next": "<i class='fa-solid fa-arrow-right w-100'></i>",
                "previous": "<i class='fa-solid fa-arrow-left w-100'></i>"
            },
            "zeroRecords": "No se encontraron registros coincidentes",
        }
    });

    table.buttons().container().appendTo('#datatable-buttons_wrapper .col-md-6:eq(0)');
    tableEnviados.buttons().container().appendTo('#datatable-buttons_enviados_wrapper .col-md-6:eq(0)');
    tablePreparacion.buttons().container().appendTo('#datatable-buttons_preparacion_wrapper .col-md-6:eq(0)');


    // Variable para almacenar el índice de la columna actual para el filtrado
    var currentFilterColumn = -1;
    var currentFilterColumnEnviados = -1;
    var currentFilterColumnPreparacion = -1;

    // Escuchar los cambios en el buscador de DataTables
    $('#datatable-buttons_preparacion_wrapper').on('input', 'input[type="search"]', function() {
        var searchTermpre = this.value;

        // Reinicia la búsqueda en todas las columnas
        tablePreparacion.columns().search('');

        // Aplica la búsqueda a la columna seleccionada o a todas si no hay ninguna seleccionada
        if (currentFilterColumnPreparacion !== -1) {
            tablePreparacion.column(currentFilterColumnPreparacion).search(searchTermpre).draw();
        } else {
            tablePreparacion.search(searchTermpre).draw();
        }
    });
    $('#datatable-buttons_enviados_wrapper').on('input', 'input[type="search"]', function() {
        var searchTermenv = this.value;

        // Reinicia la búsqueda en todas las columnas

        tableEnviados.columns().search('');

        // Aplica la búsqueda a la columna seleccionada o a todas si no hay ninguna seleccionada


        if (currentFilterColumnEnviados !== -1) {
            tableEnviados.column(currentFilterColumnEnviados).search(searchTermenv).draw();
        } else {
            tableEnviados.search(searchTermenv).draw();
        }


    });
    $('#datatable-buttons_wrapper').on('input', 'input[type="search"]', function() {
        var searchTerm = this.value;

        // Reinicia la búsqueda en todas las columnas
        table.columns().search('');

        // Aplica la búsqueda a la columna seleccionada o a todas si no hay ninguna seleccionada
        if (currentFilterColumn !== -1) {
            table.column(currentFilterColumn).search(searchTerm).draw();
        } else {
            table.search(searchTerm).draw();
        }

    });

    // Manejar la selección de columna del menú desplegable
    $('#Botonesfiltros-enviados .dropdown-item').on('click', function(e) {
        e.preventDefault();

        // Establecer la columna actual para el filtro
        currentFilterColumnEnviados = $(this).data('column');

        // Aplicar la búsqueda actual a la nueva columna seleccionada
        var searchTerm = tableEnviados.search();
        if (searchTerm) {
            tableEnviados.columns().search('');
            tableEnviados.column(currentFilterColumnEnviados).search(searchTerm).draw();
        }
    });

    $('#Botonesfiltros-preparacion .dropdown-item').on('click', function(e) {
        e.preventDefault();

        // Establecer la columna actual para el filtro
        currentFilterColumnPreparacion = $(this).data('column');

        // Aplicar la búsqueda actual a la nueva columna seleccionada
        var searchTerm = tablePreparacion.search();
        if (searchTerm) {
            tablePreparacion.columns().search('');
            tablePreparacion.column(currentFilterColumnPreparacion).search(searchTerm).draw();
        }
    });

    $('#Botonesfiltros .dropdown-item').on('click', function(e) {
        e.preventDefault();

        // Establecer la columna actual para el filtro
        currentFilterColumn = $(this).data('column');

        // Aplicar la búsqueda actual a la nueva columna seleccionada
        var searchTerm = table.search();
        if (searchTerm) {
            table.columns().search('');
            table.column(currentFilterColumn).search(searchTerm).draw();
        }
    });

    // Manejar clic en el botón de eliminar filtro
    $('#clear-filter-preparacion').on('click', function() {
        // Restablecer el índice de la columna de filtro
        currentFilterColumnPreparacion = -1;

       // Limpiar la búsqueda en todas las columnas y en la barra de búsqueda
       tablePreparacion.search('').columns().search('').draw();

       // Limpia el texto en la barra de búsqueda de DataTables
       $('#datatable-buttons_preparacion_filter input[type="search"]').val('');
    });

    $('#clear-filter-enviados').on('click', function() {
        // Restablecer el índice de la columna de filtro
        currentFilterColumnEnviados = -1;

       // Limpiar la búsqueda en todas las columnas y en la barra de búsqueda
       tableEnviados.search('').columns().search('').draw();

       // Limpia el texto en la barra de búsqueda de DataTables
       $('datatable-buttons_enviados_filter input[type="search"]').val('');
    });

    $('#clear-filter').on('click', function() {
        // Restablecer el índice de la columna de filtro
        currentFilterColumn = -1;

       // Limpiar la búsqueda en todas las columnas y en la barra de búsqueda
       table.search('').columns().search('').draw();

       // Limpia el texto en la barra de búsqueda de DataTables
       $('#datatable-buttons_filter input[type="search"]').val('');
    });

    $('#Botonesfiltros-preparacion').appendTo('#datatable-buttons_preparacion_wrapper .dataTables_filter');
    $('#Botonesfiltros-enviados').appendTo('#datatable-buttons_enviados_wrapper .dataTables_filter');
    $('#Botonesfiltros').appendTo('#datatable-buttons_wrapper .dataTables_filter');
});
