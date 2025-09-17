<dialog>
    <div class="dialog-content">
        <span class="close-button">&times;</span>
        <div class="dialog-container">
            <div class="dialog-left">
                <h3>Detalls de la Habitació</h3>
                <p><strong>ID de l'habitació:</strong> {{ $reservation['roomId'] }}</p>
                <p><strong>Nº d'habitació:</strong> {{ $reservation['roomNumber'] }}</p>
                <p><strong>Nom d'habitació:</strong> {{ $reservation['roomName'] }}</p>
                <p><strong>Tipus d'habitació:</strong> {{ $reservation['roomType'] }}</p>
                <p class="room-extra-beds"></p>
                <p><strong>Estat de l'habitació:</strong> {{ $reservation['roomState'] }}</p>
            </div>
            <div class="dialog-right reservation-details">
                <h3>Detall de la Reserva</h3>
                <p><strong>ID de reserva:</strong> {{ $reservation['reserveId'] }}</p>
                <p><strong>Nom:</strong> {{ $reservation['userName'] }}</p>
                <p><strong>Preu:</strong> {{ $reservation['price'] }} €</p>
                <p><strong>Data d'estancia:</strong> {{ $reservation['dateRange'] }}</p>
                <p><strong>Estat de la reserva:</strong> {{ $reservation['status'] }}</p>
            </div>
        </div>
        <div class="dialog-textb">
            <div class="dialog-buttons">
                <button class="btn-checkin" style="display: none;">CheckIn</button>
                <button class="btn-checkout" style="display: none;">CheckOut</button>
            </div>
        </div>
    </div>
</dialog>

