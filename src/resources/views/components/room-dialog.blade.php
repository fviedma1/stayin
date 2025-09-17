<dialog>
    <div class="dialog-room">
        <span class="close-button">&times;</span>
        <h3>Detalls de la Habitació</h3>
        <div class="dialog-container-room">
            <div class="dialog-left">
                <p><strong>Nº d'habitació:</strong> {{ $room['roomNumber'] }}</p>
                <p><strong>Nom d'habitació:</strong> {{ $room['roomName'] }}</p>
                <p><strong>Tipus d'habitació:</strong> {{ $room['roomType'] }} (Llits: {{ $room['roomBeds'] }})</p>
                <p class="extra-beds"></p>
            </div>
            <div class="dialog-right">
                <button class="block-room-btn">
                    <i class="fas"></i>
                </button>
            </div>
        </div>
    </div>
</dialog>
