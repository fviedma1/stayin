@extends('layouts.master')

@section('title', 'Detalls de l\'hotel')

@section('content')
<div class="dashboard">
    <!-- Encabezado principal con título y botón de configuración -->
    <div class="dashboard__header">
        <h1>{{ $hotel->name }}</h1>
        <a href="{{ route('section.index', ['hotelId' => $hotel->id]) }}" class="hotel__config-button">
            <i class="fas fa-cog"></i> Configurar
        </a>
    </div>
    
    <!-- Selector de hotel -->
    <div class="hotel__header">
        <form action="{{ route('hotel.showOne', ['id' => $hotel->id]) }}" method="GET">
            <label for="hotel-selector" class="hotel__label">Selecciona un hotel:</label>
            <select name="hotel_id" id="hotel-selector" class="hotel__selector" onchange="this.form.submit()">
                <option value="">Selecciona un hotel</option>
                @foreach ($allHotels as $availableHotel)
                <option value="{{ $availableHotel->id }}" {{ $hotel->id == $availableHotel->id ? 'selected' : '' }}>
                    {{ $availableHotel->name }}
                </option>
                @endforeach
            </select>
        </form>
    </div>
    
    <!-- Información de habitaciones (centrada) -->
    <div class="hotel__info-centered">
        <p>Reserves Totals: {{ $totalRooms }} habitacions</p>
    </div>

    <!-- Tarjetas de estado -->
    <div class="hotel__cards-status">
        <div class="hotel__status hotel__status--free">
            <p>Lliures: {{ $roomsLliures }} de {{ $totalRooms }}
                ({{ $totalRooms > 0 ? round(($roomsLliures / $totalRooms) * 100, 2) : 0 }}%)</p>
        </div>
        <div class="hotel__status hotel__status--occupied">
            <p>Ocupades: {{ $roomsOcupades }} de {{ $totalRooms }}
                ({{ $totalRooms > 0 ? round(($roomsOcupades / $totalRooms) * 100, 2) : 0 }}%)</p>
        </div>
        <div class="hotel__status hotel__status--pending">
            <p>Reservades: {{ $roomsReservades }} de {{ $totalRooms }}
                ({{ $totalRooms > 0 ? round(($roomsReservades / $totalRooms) * 100, 2) : 0 }}%)</p>
        </div>
    </div>
</div>
@endsection