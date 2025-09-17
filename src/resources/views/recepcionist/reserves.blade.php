@extends('layouts.master')

@section('title', 'Reserves de les Habitacions')

@section('content')
    <div class="dashboard">
        <h2 class="dashboard__heading">Habitacions pendents de fer check-in</h2>

        <!-- Mostrar mensaje de éxito -->
        @if (session('success'))
            <div class="message message--info" id="status-message">
                {{ session('success') }}
            </div>
        @endif

        <!-- Mostrar mensaje de error -->
        @if ($errors->any())
            <div class="message message--error" id="status-message">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Formulario de filtros -->
        <form method="GET" action="{{ route('recepcionist.reserves', ['hotelId' => $hotelId]) }}" class="dashboard__filters">
            <div class="dashboard__filters__group">
                <label for="filter_client_name" class="dashboard__filters__group__label">Nom del client:</label>
                <input type="text" id="filter_client_name" name="client_name" placeholder="Nom del client"
                    class="dashboard__filters__group__input" value="{{ request('client_name') }}">
            </div>
            <div class="dashboard__filters__group">
                <label for="filter_reserve_id" class="dashboard__filters__group__label">ID de reserva:</label>
                <input type="text" id="filter_reserve_id" name="reserve_id" placeholder="ID de reserva"
                    class="dashboard__filters__group__input" value="{{ request('reserve_id') }}">
            </div>
            <div class="dashboard__filters__group">
                <label for="filter_start_date" class="dashboard__filters__group__label">Data d'inici:</label>
                <input type="date" id="filter_start_date" name="start_date"
                    class="dashboard__filters__group__input" value="{{ request('start_date') }}">
            </div>
            <div class="dashboard__filters__group">
                <label for="filter_end_date" class="dashboard__filters__group__label">Data de fi:</label>
                <input type="date" id="filter_end_date" name="end_date"
                    class="dashboard__filters__group__input" value="{{ request('end_date') }}">
            </div>
            <button type="submit" class="filter-form__btn">Filtrar</button>
        </form>

        <!-- Listado de reservas -->
        @if ($reserves->isEmpty())
            <p class="dashboard__no-reserves">No hi ha reserves pendents per a aquesta data.</p>
        @else
            <div class="dashboard__list" id="reserves_list">
                @foreach ($reserves as $reserve)
                    <div class="dashboard__reserve">
                        <h3 class="dashboard__reserve__title">Reserva ID: {{ $reserve->id }}</h3>
                        <p class="dashboard__reserve__detail"><strong class="dashboard__reserve__detail--strong">Client:</strong> {{ $reserve->user->name ?? 'Sense nom' }}</p>
                        <p class="dashboard__reserve__detail"><strong class="dashboard__reserve__detail--strong">Duració de l'Estada:</strong> {{ $reserve->date_in }} - {{ $reserve->date_out }}</p>
                        <p class="dashboard__reserve__detail"><strong class="dashboard__reserve__detail--strong">Tipus d'Habitació:</strong> {{ $reserve->typeRoom->name }}</p>
                        <p class="dashboard__reserve__detail"><strong class="dashboard__reserve__detail--strong">Persones:</strong> {{ $reserve->people }}</p>
                        <p class="dashboard__reserve__detail"><strong class="dashboard__reserve__detail--strong">Habitació Assignada:</strong> {{ $reserve->room->number ?? 'Sense habitació' }}</p>
                        <p class="dashboard__reserve__detail"><strong class="dashboard__reserve__detail--strong">Estat:</strong> {{ ucfirst($reserve->status) }}</p>
                        <p class="dashboard__reserve__detail"><strong class="dashboard__reserve__detail--strong">Preu:</strong> {{ $reserve->price }}</p>
                        <p class="dashboard__reserve__detail"><strong class="dashboard__reserve__detail--strong">Notes:</strong> {{ $reserve->notes ?? 'Sense notes' }}</p>
                    </div>
                    <hr class="dashboard__separator">
                @endforeach
            </div>
        @endif
    </div>
@endsection
