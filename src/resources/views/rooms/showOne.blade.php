@extends('layouts.master')

@section('title', 'Detalls habitació')

@section('content')
    <div class="dashboard">
        <!-- Título Principal -->
        <h1 class="main-title">Detalls de la Habitació</h1>

        <!-- Contenedor principal con dos columnas (y línea vertical) -->
        <div class="container-room">
            <!-- Columna izquierda -->
            <div class="left-section">
                <h2>Nº{{ $room->number ?? 'Sense nom' }}</h2>
                <p><strong>Nom:</strong> {{ $room->name }}</p>
                <p><strong>Estat:</strong> {{ ucfirst($room->state ?? 'Sense estat') }}</p>
                <p><strong>Tipus d'habitació:</strong>
                    {{ $room->typeRoom->name }}
                    (Llits: {{ $room->typeRoom->bed }})
                    @if ($room->extra_bed > 0)
                        (+ {{ $room->extra_bed }} llit/s extra)
                    @endif
                </p>
                @if ($currentReserve)
                    <h2>Reserva Actual</h2>
                    <p><strong>Nom del Client:</strong> {{ $currentReserve->user->name ?? 'No disponible' }}</p>
                    <p><strong>ID de la Reserva:</strong> {{ $currentReserve->id ?? 'No disponible' }}</p>
                    <p><strong>Data d'Entrada:</strong> {{ $currentReserve->date_in }}</p>
                    <p><strong>Data de Sortida:</strong> {{ $currentReserve->date_out }}</p>
                @else
                    <p class="text-muted"><em>La habitació no té una reserva feta.</em></p>
                @endif
            </div>

            <div class="right-section">
                <h3>Serveis Disponibles</h3>
                @if ($room->services->isNotEmpty())
                    <ul>
                        @foreach ($room->services as $service)
                            <li>{{ $service->name ?? 'Sin servicio' }} - {{ $service->price ?? '0' }} €</li>
                        @endforeach
                    </ul>
                @else
                    <p>No hi ha serveis en aquesta habitació.</p>
                @endif

            </div>
        </div>
        </div>
    </div>
@endsection
