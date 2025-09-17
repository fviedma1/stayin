<a href="{{ route('recepcionist.create', ['hotelId' => $hotelId, 'roomId' => $room->id]) }}" class="card__link">
    <div class="card {{ empty($room->image) ? 'card--no-image' : '' }}">
        <div class="card__content">
            <h2 class="card__title">Habitació Nº {{ $room->number }}</h2>
            <p class="card__type">
                Tipus d'habitació: {{ $room->typeRoom->name ?? 'No especificado' }}
            </p>
            <div class="card__details">
                <p class="card__capacity">
                    {{ $room->typeRoom->bed ?? 'No especificado' }} persones
                </p>
                <p class="card__price">
                    {{ $room->state }}
                </p>
            </div>
        </div>
    </div>
</a>
