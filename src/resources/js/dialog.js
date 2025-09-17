document.addEventListener("DOMContentLoaded", function () {
    // Selección de plantillas para los diálogos
    const reservationDialogTemplate = document.querySelector(
        "#reservation-dialog-template"
    );
    const roomDialogTemplate = document.querySelector("#room-dialog-template");
    const messageContainerTemplate = document.querySelector(
        "#message-container-template"
    );

    // Función para adjuntar eventos a las celdas del calendario
    function attachEventListeners() {
        const reservationCells = document.querySelectorAll(".reservation-cell");

        reservationCells.forEach((cell) => {
            cell.addEventListener("click", function () {
                // Extraer información de la celda seleccionada
                const hotelId = this.dataset.hotelId;
                const reserveId = this.dataset.reserveId;
                const roomId = this.dataset.roomId;
                const roomNumber = this.dataset.roomNumber;
                const roomName = this.dataset.roomName;
                const roomState = this.dataset.roomState;
                const roomType = this.dataset.roomType;
                const roomBeds = this.dataset.roomBeds;
                const roomExtraBeds = this.dataset.roomExtraBeds;
                const userName = this.dataset.userName;
                const dateIn = this.dataset.dateIn;
                const dateOut = this.dataset.dateOut;
                const price = this.dataset.price;
                const status = this.dataset.status;

                // Si hay una reserva, mostrar el diálogo de reserva
                if (reserveId) {
                    showReservationDialog(
                        userName,
                        dateIn,
                        dateOut,
                        price,
                        status,
                        reserveId,
                        roomId,
                        roomNumber,
                        roomName,
                        roomState,
                        roomType,
                        roomBeds,
                        roomExtraBeds
                    );
                } else {
                    // Si no hay reserva, mostrar el diálogo de la habitación
                    showRoomDialog({
                        hotelId: hotelId,
                        roomId: roomId,
                        roomNumber: roomNumber,
                        roomName: roomName,
                        roomType: roomType,
                        roomBeds: roomBeds,
                        roomExtraBeds: roomExtraBeds,
                        roomState: roomState,
                        dateIn: dateIn,
                    });
                }
            });
        });
    }

    // Función para mostrar el diálogo de la habitación
function showRoomDialog(room) {
    if (room.roomState === "bloquejada") {
        const messageContainer = messageContainerTemplate.content
            .cloneNode(true)
            .querySelector("div");
        messageContainer.textContent = `No es poden fer reserves en l'habitacio ${room.roomNumber} ja que esta en manteniment`;
        document.body.appendChild(messageContainer);

        setTimeout(() => {
            messageContainer.remove();
        }, 3000);

        return;
    }

    // Seleccionar la plantilla "create-dialog-template"
    const createDialogTemplate = document.querySelector("#create-dialog-template");
    const dialog = createDialogTemplate.content.cloneNode(true);
    const dialogElement = dialog.querySelector("dialog");

    // Verifica y asigna los valores solo si los elementos existen
    const roomIdElement = dialogElement.querySelector(".room-id");
    const roomNumberElement = dialogElement.querySelector(".room-number");
    const roomNameElement = dialogElement.querySelector(".room-name");
    const roomTypeElement = dialogElement.querySelector(".room-type");
    const roomBedsElement = dialogElement.querySelector(".room-beds");
    const roomExtraBedsElement =
        dialogElement.querySelector(".room-extra-beds");
    const roomStateElement = dialogElement.querySelector(".room-state");
    const dateInElement = dialogElement.querySelector(".date-in");

    if (roomIdElement) roomIdElement.textContent = room.roomId;
    if (roomNumberElement) roomNumberElement.textContent = room.roomNumber;
    if (roomNameElement) roomNameElement.textContent = room.roomName;
    if (roomTypeElement) roomTypeElement.textContent = room.roomType;
    if (roomBedsElement) roomBedsElement.textContent = room.roomBeds;
    if (roomExtraBedsElement && room.roomExtraBeds > 0) {
        roomExtraBedsElement.textContent = `(+ ${room.roomExtraBeds} llit/s extra)`;
    }
    if (roomStateElement) roomStateElement.textContent = room.roomState;
    if (dateInElement) dateInElement.textContent = room.dateIn;

    const createReservationButton = dialogElement.querySelector(
        ".create-reservation-button"
    );
    if (createReservationButton) {
        createReservationButton.addEventListener("click", () => {
            const url = `/hotel/${
                room.hotelId
            }/reserves/create/${encodeURIComponent(room.roomId)}?date_in=${encodeURIComponent(room.dateIn)}`;
            window.location.href = url;
        });
    }

    const closeButton = dialogElement.querySelector(".close-button");
    if (closeButton) {
        closeButton.addEventListener("click", () => closeDialog(dialogElement));
    }

    document.body.appendChild(dialogElement);
    document.body.classList.add("no-scroll");
    dialogElement.showModal();
}

    // Función para mostrar el diálogo de la reserva
    function showReservationDialog(
        userName,
        dateIn,
        dateOut,
        price,
        status,
        reserveId,
        roomId,
        roomNumber,
        roomName,
        roomState,
        roomType,
        roomBeds,
        roomExtraBeds
    ) {
        // Clonar la plantilla del diálogo de la reserva
        const dialog = reservationDialogTemplate.content
            .cloneNode(true)
            .querySelector("dialog");
        const dialogContent = dialog.querySelector(".dialog-content");

        // Rellenar los datos de la habitación en el diálogo
        dialogContent.querySelector(".room-id").textContent = roomId;
        dialogContent.querySelector(".room-number").textContent = roomNumber;
        dialogContent.querySelector(".room-name").textContent = roomName;
        dialogContent.querySelector(".room-type").textContent = roomType;
        dialogContent.querySelector(".room-beds").textContent = roomBeds;
        dialogContent.querySelector(".room-extra-beds").textContent =
            roomExtraBeds > 0 ? `(+ ${roomExtraBeds} llit/s extra)` : "";
        dialogContent.querySelector(".room-state").textContent = roomState;

        // Rellenar los datos de la reserva en el diálogo
        if (reserveId) {
            dialogContent.querySelector(".reserve-id").textContent = reserveId;
            dialogContent.querySelector(".user-name").textContent = userName;
            dialogContent.querySelector(".price").textContent = `${price} €`;
            dialogContent.querySelector(
                ".date-range"
            ).textContent = `${dateIn} - ${dateOut}`;
            dialogContent.querySelector(".status").textContent = status;

            // Mostrar botones de CheckIn y CheckOut según el estado de la reserva
            const checkinButton = dialogContent.querySelector(".btn-checkin");
            const checkoutButton = dialogContent.querySelector(".btn-checkout");

            if (status === "pendent" || status === "reservada") {
                checkinButton.style.display = "block";
                checkinButton.addEventListener("click", async () => {
                    await updateReservationStatus(
                        reserveId,
                        "checkin",
                        "ocupada"
                    );
                    updateUIAfterStatusChange(reserveId, "checkin", "ocupada");
                    closeDialog(dialog);
                });
            }

            if (status === "checkin") {
                checkoutButton.style.display = "block";
                checkoutButton.addEventListener("click", async () => {
                    await updateReservationStatus(
                        reserveId,
                        "checkout",
                        "lliure"
                    );
                    updateUIAfterStatusChange(reserveId, "checkout", "lliure");
                    closeDialog(dialog);
                });
            }
        }

        // Configurar el botón de cierre del diálogo
        dialogContent
            .querySelector(".close-button")
            .addEventListener("click", () => closeDialog(dialog));

        // Mostrar el diálogo
        document.body.appendChild(dialog);
        document.body.classList.add("no-scroll");
        dialog.showModal();
    }

    // Función para actualizar la UI después de cambiar el estado de la reserva
    function updateUIAfterStatusChange(reserveId, newStatus, newState) {
        const reservationCell = document.querySelector(
            `.reservation-cell[data-reserve-id="${reserveId}"]`
        );
        if (reservationCell) {
            reservationCell.dataset.status = newStatus;
            reservationCell.dataset.state = newState;
            applyReservationStatusClass(reservationCell, newStatus);
        }
    }

    // Función para aplicar clases CSS según el estado de la reserva
    function applyReservationStatusClass(cell, status) {
        cell.classList.remove("pendent", "checkin", "checkout");
        if (status === "pendent") cell.classList.add("pendent");
        if (status === "checkin") cell.classList.add("checkin");
        if (status === "checkout") cell.classList.add("checkout");
    }

    // Función para actualizar el estado de la reserva en el servidor
    async function updateReservationStatus(reserveId, newStatus, newState) {
        try {
            const csrfToken = document
                .querySelector('meta[name="csrf-token"]')
                .getAttribute("content");
            const response = await fetch(`/api/reserves/${reserveId}/status`, {
                method: "PUT",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": csrfToken,
                },
                body: JSON.stringify({ status: newStatus, state: newState }),
            });
            const data = await response.json();
            if (!data.success) {
                console.error("Error al actualizar el estado:", data.message);
            }
        } catch (error) {
            console.error("Error en la petición:", error);
        }
    }

    // Función para cerrar el diálogo
    function closeDialog(dialog) {
        dialog.close();
        document.body.removeChild(dialog);
        document.body.classList.remove("no-scroll");
    }

    // Inicialización de eventos
    attachEventListeners();
    document.addEventListener("calendarRendered", attachEventListeners);
});
