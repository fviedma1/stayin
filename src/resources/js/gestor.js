
const templates = {
    roomHeader: document.querySelector('#room-calendar-template'),
    dayHeader: document.querySelector('#day-header-template'),
    roomCell: document.querySelector('#room-cell-template'),
    reservationCell: document.querySelector('#reservation-cell-template'),
    roomDialog: document.querySelector('#room-dialog-template')
};

const buttons = {
    prevWeek: document.querySelector('#prev-week'),
    nextWeek: document.querySelector('#next-week')
};

const flashMessage = document.querySelector('flash-message');


document.addEventListener('DOMContentLoaded', function () {
    // Variables y elementos del DOM
    const roomCalendar = document.querySelector('#room-calendar');

    if (!roomCalendar) {
        console.log('El calendario no está presente en esta vista.');
        return; // Detener la ejecución del código si no existe
    }
    
    const hotelId = roomCalendar.dataset.hotelId;
    let currentStartDate = new Date();

    // Funciones auxiliares declaradas primero
    function clearCalendar() {
        roomCalendar.innerHTML = '';
    }

    function renderCalendarHeaders(days) {
        const roomHeader = templates.roomHeader.content.cloneNode(true);
        roomCalendar.appendChild(roomHeader);

        const monthBar = document.getElementById('month-bar');
        const months = days.map(day => new Date(day).toLocaleDateString('ca', { month: 'long' }));
        const uniqueMonths = [...new Set(months)];
        monthBar.textContent = uniqueMonths.join(' - ');

        days.forEach(day => {
            const dayHeader = templates.dayHeader.content.cloneNode(true);
            dayHeader.querySelector('.day-header').textContent = new Date(day).toLocaleDateString('ca', { day: '2-digit' });
            roomCalendar.appendChild(dayHeader);
        });
    }

    function createRoomCell(room) {
        const roomCell = templates.roomCell.content.cloneNode(true);
        const roomCellElement = roomCell.querySelector('.room-cell');
        roomCellElement.textContent = room.number;
        roomCellElement.addEventListener('click', () => showRoomDialog(room));
        roomCalendar.appendChild(roomCell);
    }

    function setReservationData(cell, reservation, room) {
        cell.dataset.hotelId = hotelId;
        cell.dataset.userName = reservation.user.name;
        cell.dataset.dateIn = reservation.date_in;
        cell.dataset.dateOut = reservation.date_out;
        cell.dataset.price = reservation.price;
        cell.dataset.status = reservation.status;
        cell.dataset.state = room.state;
        cell.dataset.reserveId = reservation.id;
        cell.dataset.roomId = room.id;
        cell.dataset.roomNumber = room.number;
        cell.dataset.roomName = room.name;
        cell.dataset.roomState = room.state;
        if (room.type_room) {
            cell.dataset.roomType = room.type_room.name;
            cell.dataset.roomBeds = room.type_room.bed;
        }
        cell.dataset.roomExtraBeds = room.extra_bed;
    }

    function setAvailableRoomData(cell, room, currentDay) {
        cell.dataset.hotelId = hotelId;
        cell.dataset.roomId = room.id;
        cell.dataset.roomNumber = room.number;
        cell.dataset.roomName = room.name;
        cell.dataset.roomState = 'lliure';
        if (room.type_room) {
            cell.dataset.roomType = room.type_room.name;
            cell.dataset.roomBeds = room.type_room.bed;
        }
        cell.dataset.roomExtraBeds = room.extra_bed;
        cell.dataset.dateIn = currentDay;
    }

    function applyReservationStatusClass(cell, status) {
        cell.classList.remove('checkin', 'checkout', 'pendent');
        if (status === 'checkin') cell.classList.add('checkin');
        if (status === 'checkout') cell.classList.add('checkout');
        if (status === 'pendent') cell.classList.add('pendent');
    }

    function createReservationCell(reservation, room, colspan, currentDay) {
        const reservationCell = templates.reservationCell.content.cloneNode(true);
        const reservationCellElement = reservationCell.querySelector('.reservation-cell');
        reservationCellElement.style.gridColumn = `span ${colspan}`;

        if (reservation) {
            setReservationData(reservationCellElement, reservation, room);
            const reservationDetails = reservationCellElement.querySelector('.reservation-details');
            reservationDetails.innerHTML = `<strong>${reservation.user.name}</strong>`;
            applyReservationStatusClass(reservationCellElement, reservation.status);
        } else {
            setAvailableRoomData(reservationCellElement, room, currentDay);
        }

        return reservationCell;
    }

    function renderRoomReservations(room, days) {
        let remainingDays = [...days];

        while (remainingDays.length > 0) {
            const currentDay = remainingDays[0];
            const reservation = room.reserves.find(reserve =>
                currentDay >= reserve.date_in && currentDay <= reserve.date_out
            );

            let colspan = 1;
            if (reservation) {
                const lastDayOfReservation = new Date(reservation.date_out);
                const lastDayIndex = remainingDays.findIndex(day =>
                    new Date(day).toISOString().split('T')[0] === lastDayOfReservation.toISOString().split('T')[0]
                );

                colspan = lastDayIndex + 1;
                remainingDays = remainingDays.slice(colspan);
            } else {
                remainingDays.shift();
            }

            roomCalendar.appendChild(createReservationCell(reservation, room, colspan, currentDay));
        }
    }

    function renderRooms(rooms, days) {
        rooms.forEach(room => {
            createRoomCell(room);
            renderRoomReservations(room, days);
        });
    }

    function closeDialog(dialogElement) {
        dialogElement.close();
        document.body.removeChild(dialogElement);
        document.body.classList.remove('no-scroll');
    }

    async function blockRoom(room) {
        try {
            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            const url = `/api/hotel/${room.number}/bloquejar`;

            const response = await fetch(url, {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: JSON.stringify({ bloquejada: 'bloquejada' })
            });

            const data = await response.json();

            if (data.success) {
                window.location.reload();
            } else {
                console.error('Error al actualizar el estado:', data.message);
            }
        } catch (error) {
            console.error('Error en la solicitud:', error);
        }
    }

    function showRoomDialog(room) {
        const dialog = templates.roomDialog.content.cloneNode(true);
        const dialogElement = dialog.querySelector('dialog');

        dialogElement.querySelector('.room-number').textContent = room.number;
        dialogElement.querySelector('.room-name').textContent = room.name;
        dialogElement.querySelector('.room-type').textContent = room.type_room.name;
        dialogElement.querySelector('.room-beds').textContent = room.type_room.bed;
        if (room.extra_bed > 0) {
            dialogElement.querySelector('.extra-beds').textContent = `(+ ${room.extra_bed} llit/s extra)`;
        }

        const closeButton = dialogElement.querySelector('.close-button');
        closeButton.addEventListener('click', () => closeDialog(dialogElement));

        const blockRoomBtn = dialogElement.querySelector('.block-room-btn');
        blockRoomBtn.addEventListener('click', () => blockRoom(room));

        document.body.appendChild(dialogElement);
        document.body.classList.add('no-scroll');
        dialogElement.showModal();
    }

    // Función principal
    async function fetchAndRenderCalendar(startDate) {
        try {
            const response = await fetch(`/api/rooms/${hotelId}?startDate=${startDate.toISOString().split('T')[0]}`);
            const data = await response.json();
            const { rooms, days } = data;

            clearCalendar();
            renderCalendarHeaders(days);
            renderRooms(rooms, days);

            document.dispatchEvent(new Event('calendarRendered'));
        } catch (error) {
            console.error('Error fetching room data:', error);
        }
    }

    // Event listeners y lógica de inicialización
    buttons.prevWeek.addEventListener('click', () => {
        currentStartDate.setDate(currentStartDate.getDate() - 7);
        fetchAndRenderCalendar(currentStartDate);
    });

    buttons.nextWeek.addEventListener('click', () => {
        currentStartDate.setDate(currentStartDate.getDate() + 7);
        fetchAndRenderCalendar(currentStartDate);
    });

    if (flashMessage) {
        flashMessage.classList.add('show');
        setTimeout(() => {
            flashMessage.classList.remove('show');
        }, 3000);
    }

    // Inicializar
    fetchAndRenderCalendar(currentStartDate);
});