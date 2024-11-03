<div class="container-fluid">
    <!-- Estilos de Tui Calendar -->
    <link rel="stylesheet" href="https://uicdn.toast.com/tui.time-picker/latest/tui-time-picker.min.css" />
    <link rel="stylesheet" href="https://uicdn.toast.com/tui.date-picker/latest/tui-date-picker.min.css" />
    <link rel="stylesheet" href="https://uicdn.toast.com/calendar/latest/toastui-calendar.min.css" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0/dist/css/select2.min.css" />

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
                useDetailPopup: true,
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
        });
    </script>
</div>
