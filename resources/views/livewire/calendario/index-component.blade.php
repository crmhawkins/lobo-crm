<div class="container-fluid">
    <!-- Estilos de Tui Calendar -->
    <link rel="stylesheet" href="https://uicdn.toast.com/tui.time-picker/latest/tui-time-picker.min.css" />
    <link rel="stylesheet" href="https://uicdn.toast.com/tui.date-picker/latest/tui-date-picker.min.css" />
    <link rel="stylesheet" href="https://uicdn.toast.com/calendar/latest/toastui-calendar.min.css" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0/dist/css/select2.min.css" />

    <style>
        .toastui-calendar-popup-section.toastui-calendar-dropdown-section.toastui-calendar-state-section{
            display: none;
        }
        .toastui-calendar-popup-section-item.toastui-calendar-popup-section-private.toastui-calendar-popup-button{
            display: none;
        }
        .toastui-calendar-ic-location-b,
        .toastui-calendar-icon.toastui-calendar-ic-location {
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 192 512'%3E%3Cpath fill='%23000000' d='M48 80a48 48 0 1 1 96 0A48 48 0 1 1 48 80zM0 224c0-17.7 14.3-32 32-32l64 0c17.7 0 32 14.3 32 32l0 224 32 0c17.7 0 32 14.3 32 32s-14.3 32-32 32L32 512c-17.7 0-32-14.3-32-32s14.3-32 32-32l32 0 0-192-32 0c-17.7 0-32-14.3-32-32z'/%3E%3C/svg%3E");

        }

        .toastui-calendar-detail-item.toastui-calendar-detail-item-indent,
        .toastui-calendar-icon.toastui-calendar-ic-state-b,
        .toastui-calendar-template-popupDetailState
        {
            display: none;
        }

        #miModal .modal-title small {
        color: white;
    }

    </style>

    <div class="page-title-box">
        <div class="row align-items-center">
            <div class="col-sm-6">
                <h4 class="page-title">CALENDARIO DE EVENTOS</h4>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-right">
                    <li class="breadcrumb-item"><a href="javascript:void(0);">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="javascript:void(0);">Marketing</a></li>
                    <li class="breadcrumb-item active">Calendario de eventos</li>
                </ol>
            </div>
        </div>
    </div>



    <!-- Contenedor del calendario -->
    <div class="container-fluid" x-data>
        <div id="calendar" class="mb-5 bg-light" style="height: 800px;" wire:ignore></div>
    </div>

    <!-- Scripts necesarios -->
    <script src="https://cdn.jsdelivr.net/npm/dayjs@1.11.5/dayjs.min.js"></script>
    <script src="https://uicdn.toast.com/select-box/latest/toastui-select-box.js"></script>
    <script src="https://uicdn.toast.com/tui.time-picker/latest/tui-time-picker.min.js"></script>
    <script src="https://uicdn.toast.com/tui.date-picker/latest/tui-date-picker.min.js"></script>
    <script src="https://uicdn.toast.com/calendar/latest/toastui-calendar.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0/dist/js/select2.min.js"></script>

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

            // Inicializar el calendario
            var Calendar = tui.Calendar;
            var calendars = @json($calendars);
            var events = @json($events);

            events = events.map(function (item) {
                item.start = new Date(item.start);
                item.end = new Date(item.end);
                item.calendarId = item.calendarId || item.calendar_id || '1';
                return item;
            });

            var cal = new Calendar('#calendar', {
                defaultView: 'month',
                usageStatistics: false,
                theme: {
                    common: {
                        backgroundColor: 'transparent'
                    }
                },
                calendars: calendars,
                month: {
                    startDayOfWeek: 1,
                    daynames: ['Domingo', 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado']
                },
                useFormPopup: true,
                useDetailPopup: false,
                template: {
                    popupIsAllday: () => '¿Todo el día?',
                    popupStateFree: () => 'Libre',
                    popupStateBusy: () => 'Ocupado',
                    titlePlaceholder: () => 'Título',
                    locationPlaceholder: () => 'Descripción',
                    startDatePlaceholder: () => 'Fecha de inicio',
                    endDatePlaceholder: () => 'Fecha de fin',
                    popupSave: () => 'Agregar Evento',
                    popupUpdate: () => 'Actualizar Evento',
                    popupEdit: () => 'Modificar',
                    popupDelete: () => 'Eliminar',
                    popupDetailTitle: (data) => 'Detalle de ' + data.title
                }
            });

            cal.createEvents(events);

            // Manejar la creación de nuevos eventos
            cal.on('beforeCreateEvent', function (event) {
                var eventData = {
                    title: event.title,
                    calendarId: event.calendarId || '1',
                    location: event.location || '',
                    isPrivate: event.isPrivate || false,
                    isAllday: event.isAllDay || false,
                    state: event.state || 'ocupado',
                    category: event.isAllDay ? 'allday' : 'time',
                    start: event.start,
                    end: event.end
                };

                fetch('{{ route("event.store") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify(eventData)
                })
                .then(response => response.json())
                .then(response => {
                    eventData.id = response.newID;
                    cal.createEvents([eventData]);
                })
                .catch(error => console.error('Error al crear evento:', error));
            });

            // Manejar la actualización de eventos
            cal.on('beforeUpdateEvent', function (event) {
                const updatedEvent = event.event;
                const changes = event.changes;

                Object.assign(updatedEvent, changes);

                cal.updateEvent(updatedEvent.id, updatedEvent.calendarId, updatedEvent);

                var eventData = {
                    id: updatedEvent.id,
                    calendarId: updatedEvent.calendarId,
                    title: updatedEvent.title,
                    location: updatedEvent.location || '',
                    isPrivate: updatedEvent.isPrivate || false,
                    isAllday: updatedEvent.isAllday || false,
                    state: updatedEvent.state || 'ocupado',
                    category: updatedEvent.isAllday ? 'allday' : 'time',
                    start: updatedEvent.start,
                    end: updatedEvent.end
                };

                fetch(`/admin/calendario/event/${eventData.id}`, {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify(eventData)
                })
                .then(response => response.json())
                .catch(error => console.error('Error al actualizar el evento:', error));
            });

            // Manejar la eliminación de eventos
            cal.on('beforeDeleteEvent', function (eventObj) {
                fetch(`/admin/calendario/event/${eventObj.id}`, {
                    method: 'DELETE',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                })
                .then(response => response.json())
                .then(() => {
                    cal.deleteEvent(eventObj.id, eventObj.calendarId);
                })
                .catch(error => console.error('Error al eliminar evento:', error));
            });

            // Manejar el clic en un evento para abrir un modal personalizado
            cal.on('clickEvent', function (event) {
                var eventData = event.event;

                // Formatear las fechas y horas al español
                var opcionesFecha = { year: 'numeric', month: 'long', day: 'numeric' };
                var opcionesHora = { hour: '2-digit', minute: '2-digit' };
                var fechaInicio = new Date(eventData.start).toLocaleDateString('es-ES', opcionesFecha);
                var horaInicio = new Date(eventData.start).toLocaleTimeString('es-ES', opcionesHora);
                var fechaFin = new Date(eventData.end).toLocaleDateString('es-ES', opcionesFecha);
                var horaFin = new Date(eventData.end).toLocaleTimeString('es-ES', opcionesHora);

                var fechaTexto = fechaInicio === fechaFin
                    ? `${fechaInicio} de ${horaInicio} a ${horaFin}`
                    : `De ${fechaInicio} a las ${horaInicio} hasta ${fechaFin} a las ${horaFin}`;

                // Abrir el modal y rellenar los campos con los datos del evento
                $('#miModal').modal('show');
                $('#miModal .modal-title').html(`
                    <input type="text" id="eventTitle" class="form-control" value="${eventData.title}" />
                    <small class="text-white d-block">${fechaTexto}</small>
                `);
                $('#miModal .modal-body').html(`
                    <input type="text" id="eventLocation" class="form-control" value="${eventData.location || ''}" />
                `);

                // Guardar el ID del evento en un atributo de datos del botón de eliminación
                $('#btnEliminarEvento').data('eventId', eventData.id);
                $('#btnGuardarCambios').data('eventId', eventData.id);

                // Deshabilitar el botón de guardar cambios si el rol no es 1
                if (@this.userRole != 1) {
                    $('#btnGuardarCambios').prop('disabled', true);
                } else {
                    $('#btnGuardarCambios').prop('disabled', false);
                }
            });

            // Función para guardar los cambios del evento
            $('#btnGuardarCambios').on('click', function () {
                var eventId = $(this).data('eventId');
                var updatedTitle = $('#eventTitle').val();
                var updatedLocation = $('#eventLocation').val();

                @this.actualizarEvento(eventId, updatedTitle, updatedLocation);
                $('#miModal').modal('hide');
            });

            // Función para eliminar el evento usando Livewire
            $('#btnEliminarEvento').on('click', function () {
                var eventId = $(this).data('eventId');
                @this.eliminarEvento(eventId);
                $('#miModal').modal('hide');
            });

            $('#btnMostrarSelector').on('click', function () {
                $('#userSelector').toggle(); // Mostrar/ocultar el selector de usuarios
            });

            $('#usuariosEmail').select2({
                placeholder: "Seleccionar usuarios",
                allowClear: true
            });

            $('#btnEnviarEmail').off('click').on('click', function () {
                var selectedUsers = $('#usuariosEmail').val();
                var eventId = $('#btnGuardarCambios').data('eventId');
                console.log(selectedUsers);
                if (selectedUsers.length > 0) {
                    @this.enviarEmail(selectedUsers, eventId);
                    $('#emailModal').modal('hide');
                } else {
                    alert('Por favor, selecciona al menos un usuario.');
                }
            });

            // Inicializar Select2 para el modal de email
            $('#usuariosEmail').select2({
                placeholder: "Seleccionar usuarios",
                allowClear: true
            });

            // Mostrar el modal de email al hacer clic en el botón
            $('#btnMostrarSelector').on('click', function () {
                $('#emailModal').modal('show');
            });
        });
    </script>

<!-- Modal personalizado -->
<div class="modal fade" id="miModal" tabindex="-1" role="dialog" aria-labelledby="miModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="miModalLabel">Detalles del Evento</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <!-- Detalles del evento se actualizarán aquí -->
                <div id="userSelector" style="display: none;">
                    <label for="usuariosEmail">Seleccionar usuarios para enviar email:</label>
                    <select id="usuariosEmail" class="form-control" multiple>
                        @foreach($usuarios as $usuario)
                            <option value="{{ $usuario->id }}">{{ $usuario->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger" id="btnEliminarEvento">Eliminar Evento</button>
                <button type="button" class="btn btn-primary" id="btnGuardarCambios">Guardar Cambios</button>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                <button type="button" class="btn btn-info" id="btnMostrarSelector">Seleccionar Usuarios</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal para seleccionar usuarios y enviar email -->
<div class="modal fade" id="emailModal" tabindex="-1" role="dialog" aria-labelledby="emailModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title" id="emailModalLabel">Enviar Email a Usuarios</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <label for="usuariosEmail">Seleccionar usuarios para enviar email:</label>
                <select id="usuariosEmail" class="form-control" multiple>
                    @foreach($usuarios as $usuario)
                        <option value="{{ $usuario->id }}">{{ $usuario->email }}</option>
                    @endforeach
                </select>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                <button type="button" class="btn btn-info" id="btnEnviarEmail">Enviar Email</button>
            </div>
        </div>
    </div>
</div>
</div>

