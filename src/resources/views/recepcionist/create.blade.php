@extends('layouts.master')

@section('title', 'Crear una reserva')

@section('content')

    <div class="dashboard">
        <h2>Crea la teva reserva</h2>

        <!-- Mostrar el missatge d'èxit -->
        @if (session('success'))
            <div class="message-content message-content--info" id="status-message">
                {{ session('success') }}
            </div>
        @endif

        <!-- Formulari -->
        <form action="{{ route('recepcionist.store', ['roomId' => $room->id]) }}" method="POST">
            @csrf
            <div class="form">
                <!-- Columna 1: Camps principals -->
                <div class="form__column">
                    <h3 class="form__heading">Informació de l'Habitació</h3>
                    <label for="room" class="form__label">Habitació Nº {{ $room->number }}</label>
                    <label for="room" class="form__label">Tipus d'habitació: {{ $room->typeRoom->name }}</label>
                    @if ($room->services->isNotEmpty())
                        <ul class="form__services-list">
                            @foreach ($room->services as $service)
                                <li class="form__service-item">{{ $service->name ?? 'Sin servicio' }} -
                                    {{ $service->price ?? '0' }} €</li>
                            @endforeach
                        </ul>
                    @else
                        <p>No hi ha serveis en aquesta habitació.</p>
                    @endif
                </div>

                <div class="form__column">
                    <h3 class="form__heading">Dades del Client</h3>
                    <label for="name" class="form__label">Nom del client</label>
                    <input type="text" name="name" id="name" class="form__input" value="{{ old('name') }}"
                        placeholder="Nom del client">
                    @error('name')
                        <p class="form__error">{{ $message }}</p>
                    @enderror

                    <label for="email" class="form__label">Correu Electrònic</label>
                    <input type="email" name="email" id="email" class="form__input" value="{{ old('email') }}"
                        placeholder="Correu del client">
                    @error('email')
                        <p class="form__error">{{ $message }}</p>
                    @enderror

                    <label for="dni" class="form__label">Document Identificatiu</label>
                    <input type="text" name="dni" id="dni" class="form__input" value="{{ old('dni') }}"
                        placeholder="Document Identificatiu del client">
                    @error('dni')
                        <p class="form__error">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Columna 3: Dates de la reserva -->
                <div class="form__column">
                    <h3 class="form__heading">Data de la reserva</h3>

                    <label for="date_in" class="form__label">Data d'entrada</label>
                    <input type="date" name="date_in" id="date_in" class="form__input"
                        value="{{ old('date_in', request('date_in', \Carbon\Carbon::now()->format('Y-m-d'))) }}">
                    @error('date_in')
                        <p class="form__error">{{ $message }}</p>
                    @enderror

                    <label for="date_out" class="form__label">Data de sortida</label>
                    <input type="date" name="date_out" id="date_out" class="form__input"
                        value="{{ old('date_out', request('date_in') ? \Carbon\Carbon::parse(request('date_in'))->addDay()->format('Y-m-d') : \Carbon\Carbon::now()->addDay()->format('Y-m-d')) }}">
                    @error('date_out')
                        <p class="form__error">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Botons a sota del formulari -->
            <div class="form__actions">
                <a href="{{ route('recepcionist.gestor', ['hotelId' => $hotelId]) }}"
                    class="btn btn--cancel">Cancel·lar</a>
                <button type="submit" class="btn btn--submit">Crear reserva</button>
            </div>
        </form>

    </div>
@endsection
