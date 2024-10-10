<div class="container-fluid">
    <!-- Estilos de Tui Calendar -->
    <link rel="stylesheet" href="https://uicdn.toast.com/tui.time-picker/latest/tui-time-picker.min.css" />
    <link rel="stylesheet" href="https://uicdn.toast.com/tui.date-picker/latest/tui-date-picker.min.css" />
    <link rel="stylesheet" href="https://uicdn.toast.com/calendar/latest/toastui-calendar.min.css" />
    <link rel="stylesheet" href="https://uicdn.toast.com/select-box/latest/toastui-select-box.css" />
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
        </div> <!-- end row -->
    </div>
    <!-- Contenedor del calendario -->
    <div id="calendar" class="mb-5 bg-light" style="height: 800px;" wire:ignore.self></div>

    <!-- Scripts necesarios -->
    <script src="https://cdn.jsdelivr.net/npm/dayjs@1.11.5/dayjs.min.js"></script>
    <script src="https://uicdn.toast.com/select-box/latest/toastui-select-box.js"></script>
    <script src="https://uicdn.toast.com/tui.time-picker/latest/tui-time-picker.min.js"></script>
    <script src="https://uicdn.toast.com/tui.date-picker/latest/tui-date-picker.min.js"></script>
    <script src="https://uicdn.toast.com/calendar/latest/toastui-calendar.min.js"></script>

    <!-- Script de inicialización del calendario -->
    <script>
        document.addEventListener('livewire:load', function () {
            console.log('load');
            var Calendar = tui.Calendar;

            // Obtener los calendarios desde Livewire
            var calendars = @json($calendars);

            // Inicializar el calendario
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
                    startDayOfWeek: 1, // Comienza la semana en lunes
                    daynames: ['Domingo', 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado'],
                },
                useFormPopup: true, // Activamos el popup de creación
                useDetailPopup: true, // Popup de detalles
                useStatePopup: true, // No mostrar popup de estado
                template: {
                    popupIsAllday: function() {
                        return '¿Todo el día?';
                    },
                    popupStateFree: function() {
                        return 'Libre';
                    },
                    popupStateBusy: function() {
                        return 'Ocupado';
                    },
                    titlePlaceholder: function() {
                        return 'Título';
                    },
                    locationPlaceholder: function() {
                        return 'Descripción';
                    },
                    startDatePlaceholder: function() {
                        return 'Fecha de inicio';
                    },
                    endDatePlaceholder: function() {
                        return 'Fecha de fin';
                    },
                    
                    popupSave: function() {
                        return 'Agregar Evento';
                    },
                    popupUpdate: function() {
                        return 'Actualizar Evento';
                    },
                    popupEdit: function() {
                        return 'Modificar';
                    },
                    popupDelete: function() {
                        return 'Eliminar';
                    },
                    popupDetailTitle: function(data) {
                        return 'Detalle de ' + data.title;
                    },
                },
            });
            cal.setOptions({
                month: {
                    dayNames: ['Domingo', 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado'],
                },
            })
            // Cargar los eventos existentes desde Livewire
            var events = @json($events);

            // Convertir fechas de los eventos a objetos Date
            events = events.map(function(item) {
                item.start = new Date(item.start);
                item.end = new Date(item.end);
                item.calendarId = item.calendarId || item.calendar_id || '1';  // Asigna calendarId o calendar_id desde la base de datos
                console.log('item', item);  // Para depurar y verificar si el calendarId está presente
                return item;
            });
            console.log('events', events);
            cal.createEvents(events);
            // cal.once('beforeCreateEvent', (data) => {
            //     console.log(data);
            //     const event = {
            //         calendarId: data.calendarId,
            //         title: data.title,
            //         location: data.location,
            //         state: data.state,
            //         isAllDay: data.isAllDay,
            //         start: data.start,
            //         end: data.end
            //     }
            //     console.log("event" , event)
            //     Livewire.emit('addItem', event);


            // });

            function sendFetchRequest(url, method, data) {
                return fetch(url, {
                    method: method,
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify(data)
                })
                .then(response => response.json())
                .catch(error => console.error('Error:', error));
            }
            // Manejar la creación de nuevos eventos
            cal.on('beforeCreateEvent', function(event) {
                console.log('beforeCreateEvent', event);
                var eventData = {
                    title: event.title,
                    calendarId: event.calendarId || '1',
                    location: event.location || '',
                    isPrivate: event.isPrivate || false,
                    isAllday: event.isAllDay || false,
                    state: event.state || 'ocupado',
                    category: event.isAllDay ? 'allday' : 'time',
                    start: event.start,
                    end: event.end,
                };

                //cal.createEvents([eventData]);
                sendFetchRequest('{{ route("event.store") }}', 'POST', eventData)
                .then(response => {
                    console.log('Evento creado:', response);
                    eventData.id = response.newID;  // Asignar nuevo ID desde el servidor
                    cal.createEvents([eventData]);  // Añadir el evento al calendario
                })
                .catch(error => console.error('Error al crear evento:', error));
                //console.log('createSchedule', eventData);
                // Emitir evento a Livewire para agregarlo
                //Livewire.emit('addItem', eventData);
            });

            // Manejar la actualización de eventos
            // cal.on('beforeUpdateEvent', ({ event, change }) => {
            //     console.log('beforeUpdateEvent', event);
            //     console.log('change', change);
            //     cal.updateEvent(event.id, event.calendarId, change);
            // });
            cal.on('beforeUpdateEvent', function(event) {
                console.log('beforeUpdateEvent', event);

                // Extraemos el evento actual y los cambios
                const updatedEvent = event.event;
                const changes = event.changes;

                console.log('changes', changes);

                // Aplicamos los cambios a los valores del evento original si existen
                if (changes.title) updatedEvent.title = changes.title;
                if (changes.start) updatedEvent.start = changes.start;
                if (changes.end) updatedEvent.end = changes.end;
                if (changes.location) updatedEvent.location = changes.location;
                if (changes.state) updatedEvent.state = changes.state;
                if (changes.isAllday !== undefined) updatedEvent.isAllday = changes.isAllday;
                if (changes.isPrivate !== undefined) updatedEvent.isPrivate = changes.isPrivate;

                // Ahora, actualizamos el evento en el calendario
                cal.updateEvent(updatedEvent.id, updatedEvent.calendarId, updatedEvent);

                // Preparar los datos para el envío al servidor
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
                    end: updatedEvent.end,
                };

                console.log('updateSchedule', eventData);

                // Usar fetch para enviar los datos al servidor
                sendFetchRequest(`/admin/calendario/event/${eventData.id}`, 'PUT', eventData)
                    .then(response => {
                        console.log('Evento actualizado en el servidor:', response);
                    })
                    .catch(error => console.error('Error al actualizar el evento:', error));
            });


            // Manejar la eliminación de eventos
            cal.on('beforeDeleteEvent', function(eventObj) {

                sendFetchRequest(`/admin/calendario/event/${eventObj.id}`, 'DELETE', {})
                    .then(response => {
                        console.log('Evento eliminado:', response);
                        cal.deleteEvent(eventObj.id, eventObj.calendarId);  // Eliminar del calendario
                    })
                    .catch(error => console.error('Error al eliminar evento:', error));
            });
            

            // Escuchar eventos desde Livewire para actualizar el calendario
            Livewire.on('eventoAgregado', function(eventData) {
                eventData.start = new Date(eventData.start);
                eventData.end = new Date(eventData.end);
                console.log('eventData', eventData);
                cal.createEvents([eventData]);
            });

            Livewire.on('eventoActualizado', function(eventData) {
                eventData.start = new Date(eventData.start);
                eventData.end = new Date(eventData.end);
                cal.updateEvent(eventData.id, eventData.calendarId, eventData);
            });

            Livewire.on('eventoEliminado', function(eventId) {
                cal.deleteEvent(eventId, cal.getEvent(eventId).calendarId);
            });
        });
    </script>
</div>
