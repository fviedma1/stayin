@extends('layouts.master')

@section('title', 'Gestor de Habitacions')

@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">
@if (session('status'))
<div id="flash-message" class="flash-message success message-content">
    {{ session('status') }}
</div>
@endif

<!-- Mostrar el missatge d'éxit -->
@if (session('success'))
<div class="message-content message-content--info" id="status-message">
    {{ session('success') }}
</div>
@endif


<div class="calennav-container">
    <div class="calendar-navigation">
        <!-- Leyenda de colores -->
        <div class="nav-legend">
            <span class="legend-item">
                <span class="color-box reserved"></span> Reservada
            </span>
            <span class="legend-item">
                <span class="color-box checkin"></span> Checkin
            </span>
            <span class="legend-item">
                <span class="color-box checkout"></span> Checkout
            </span>
        </div>

        <!-- Botones de navegación -->
        <div class="nav-buttons">
            <button id="prev-week" class="nav-button">
                <i class="fas fa-chevron-left"></i> Anterior
            </button>
            <p class="nav-text">Setmana Actual</p>
            <button id="next-week" class="nav-button">
                Següent <i class="fas fa-chevron-right"></i>
            </button>
        </div>
        <!-- Botón de crear reserva -->
        <div class="nav-right">
            <a href="{{ route('rooms.show', ['hotelId' => $hotelId]) }}" class="nav-button green-button">
                +Reserva
            </a>
        </div>
    </div>
    <!-- Contenedor para la barra de meses -->
    <div id="month-bar" class="month-bar"></div>
    <div id="room-calendar" class="room-calendar" data-hotel-id="{{ $hotelId }}"></div>

    <template id="room-calendar-template">
        <div class="room-header">Nº</div>
    </template>

    <template id="day-header-template">
        <div class="day-header"></div>
    </template>

    <template id="room-cell-template">
        <div class="room-cell"></div>
    </template>

    <template id="reservation-cell-template">
        <div class="reservation-cell">
            <div class="reservation-details"></div>
        </div>
    </template>

    <template id="reservation-dialog-template">
        <dialog>
            <div class="dialog-content">
                <span class="close-button">&times;</span>
                <div class="dialog-container">
                    <div class="dialog-left">
                        <h3>Detalls de la Habitació</h3>
                        <p><strong>ID de l'habitació:</strong> <span class="room-id"></span></p>
                        <p><strong>Nº d'habitació:</strong> <span class="room-number"></span></p>
                        <p><strong>Nom d'habitació:</strong> <span class="room-name"></span></p>
                        <p><strong>Tipus d'habitació:</strong> <span class="room-type"></span> (Llits: <span
                                class="room-beds"></span>)</p>
                        <p class="room-extra-beds"></p>
                        <p><strong>Estat de l'habitació:</strong> <span class="room-state"></span></p>
                    </div>
                    <div class="dialog-right reservation-details">
                        <h3>Detall de la Reserva</h3>
                        <p><strong>ID de reserva:</strong> <span class="reserve-id"></span></p>
                        <p><strong>Nom:</strong> <span class="user-name"></span></p>
                        <p><strong>Preu:</strong> <span class="price"></span> €</p>
                        <p><strong>Data d'estancia:</strong> <span class="date-range"></span></p>
                        <p><strong>Estat de la reserva:</strong> <span class="status"></span></p>
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
    </template>
    <template id="room-dialog-template">
        <dialog>
            <div class="dialog-room">
                <span class="close-button">&times;</span>
                <h3>Detalls de la Habitació</h3>
                <div class="dialog-container-room">
                    <div class="dialog-left">
                        <p><strong>Nº d'habitació:</strong> <span class="room-number"></span></p>
                        <p><strong>Nom d'habitació:</strong> <span class="room-name"></span></p>
                        <p><strong>Tipus d'habitació:</strong> <span class="room-type"></span></p>
                        <p><strong>Llits:</strong> <span class="room-beds"></span></p>
                        <p><strong>Llits extra:</strong> <span class="extra-beds"></span></p>
                    </div>
                    <div class="dialog-right">
                        <button class="block-room-btn">
                            <i>Desbloqueja/Bloqueja</i>
                        </button>
                    </div>
                </div>
            </div>
        </dialog>
    </template>

    <template id="create-dialog-template">
        <dialog>
            <div class="dialog-content">
                <span class="close-button">&times;</span>
                <div class="dialog-container">
                    <div class="dialog-left">
                        <h3>Detalls de la Habitació</h3>
                        <p><strong>ID de l'habitació:</strong> <span class="room-id"></span></p>
                        <p><strong>Nº d'habitació:</strong> <span class="room-number"></span></p>
                        <p><strong>Nom d'habitació:</strong> <span class="room-name"></span></p>
                        <p><strong>Tipus d'habitació:</strong> <span class="room-type"></span> (Llits: <span class="room-beds"></span>)</p>
                        <p class="room-extra-beds"></p>
                        <p><strong>Estat de l'habitació:</strong> <span class="room-state"></span></p>
                        <p><strong>Data d'entrada:</strong> <span class="date-in"></span></p>
                    </div>
                </div>
                <div class="dialog-buttons">
                    <button class="create-reservation-button">Reservar</button>
                </div>
            </div>
        </dialog>
    </template>

    <template id="message-container-template">
        <div class="message-container"></div>
    </template>
</div>
@endsection

@section('scripts')
<script src="{{ asset('js/app.js') }}"></script>
@endsection