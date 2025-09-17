<dialog>
    <div class="dialog-content">
        <span class="close-button">&times;</span>
        <div class="dialog-container">
            <div class="dialog-left">
                <h3>Detalls de la Habitació</h3>
                <p><strong>ID de l'habitació:</strong> {{ $room['roomId'] }}</p>
                <p><strong>Nº d'habitació:</strong> {{ $room['roomNumber'] }}</p>
                <p><strong>Nom d'habitació:</strong> {{ $room['roomName'] }}</p>
                <p><strong>Tipus d'habitació:</strong> {{ $room['roomType'] }} (Llits: {{ $room['roomBeds'] }})</p>
                <p class="room-extra-beds"></p>
                <p><strong>Estat de l'habitació:</strong> {{ $room['roomState'] }}</p>
                <p><strong>Data d'entrada:</strong> {{ $dateIn }}</p>
            </div>
        </div>
        <div class="dialog-buttons">
            <button class="create-reservation-button">Reservar</button>
        </div>
    </div>
</dialog>
