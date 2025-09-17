@extends('layouts.master')

@section('title', 'Listado de Habitaciones')

@section('content')
    <div class="container-card">
        <h2 class="container-card__main-title">Sel·lecciona la habitació</h2>

        <!-- Formulario de Filtros -->
        <form method="GET" action="{{ route('rooms.show', ['hotelId' => $hotelId]) }}" class="filter-form">
            <div class="filter-form__group">
                <label class="filter-form__label" for="room_number">Número de habitació:</label>
                <input class="filter-form__input" type="text" name="room_number" id="room_number" value="{{ request('room_number') }}">
            </div>
            <div class="filter-form__group">
                <label class="filter-form__label" for="room_type">Tipus d'habitació:</label>
                <select class="filter-form__select" name="room_type" id="room_type">
                    <option value="">Tots</option>
                    @foreach ($roomTypes as $type)
                        <option value="{{ $type->id }}" {{ request('room_type') == $type->id ? 'selected' : '' }}>
                            {{ $type->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="filter-form__group">
                <label class="filter-form__label" for="room_state">Estat de l'habitació:</label>
                <select class="filter-form__select" name="room_state" id="room_state">
                    <option value="">Tots</option>
                    <option value="lliure" {{ request('room_state') == 'lliure' ? 'selected' : '' }}>Lliure</option>
                    <option value="reservada" {{ request('room_state') == 'reservada' ? 'selected' : '' }}>Reservada</option>
                    <option value="ocupada" {{ request('room_state') == 'ocupada' ? 'selected' : '' }}>Ocupada</option>
                </select>
            </div>
            <button type="submit" class="filter-form__btn">Filtrar</button>
        </form>

        <!-- Lista de Habitaciones -->
        <div class="container-card__grid">
            @forelse ($rooms as $room)
                @include('components.room-component', ['room' => $room])
            @empty
                <p>No hi ha habitacions que coincideixin amb els filtres.</p>
            @endforelse
        </div>
    </div>
@endsection
