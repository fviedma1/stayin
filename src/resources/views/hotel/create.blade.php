@extends('layouts.master')

@section('title', 'Afegir hotel')

@section('content')
<div class="wizard">
    <div class="dashboard">
        <div class="dashboard__header">
            <h1>Crea el teu hotel</h1>
        </div>
        <!-- Barra de Progreso -->
        <div class="wizard__progress-bar">
            <div class="wizard__progress-step" data-step="1">1. Informació Bàsica</div>
            <div class="wizard__progress-step" data-step="2">2. Ubicació</div>
            <div class="wizard__progress-step" data-step="3">3. Contacte</div>
            <div class="wizard__progress-step" data-step="4">4. Configuració</div>
            <div class="wizard__progress-step" data-step="5">5. Recepcionista</div>
        </div>

        <!-- Formulario -->
        <form action="{{ route('hotel.store') }}" method="POST" id="hotelForm">
            @csrf

            <!-- Paso 1: Información Básica -->
            <div class="wizard__form-step wizard__form-step--active" data-step="1">
                <div class="form__column">
                    <h3 class="form__heading">Camps Principals</h3>
                    <label for="name" class="form__label">Nom de l'hotel</label>
                    <input type="text" name="name" id="name" class="form__input" value="{{ old('name') }}"
                        required>
                    @error('name')
                    <p class="form__error">{{ $message }}</p>
                    @enderror

                    <label for="address" class="form__label">Adreça</label>
                    <input type="text" name="address" id="address" class="form__input" value="{{ old('address') }}"
                        required>
                    @error('address')
                    <p class="form__error">{{ $message }}</p>
                    @enderror

                    <label for="code" class="form__label">Id Hotel</label>
                    <input type="text" name="code" id="code" class="form__input" value="{{ old('code') }}"
                        required>
                    @error('code')
                    <p class="form__error">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Paso 2: Ubicación -->
            <div class="wizard__form-step" data-step="2">
                <div class="form__column">
                    <h3 class="form__heading">Ubicació</h3>

                    <label for="country" class="form__label">País</label>
                    <input type="text" name="country" id="country" class="form__input" value="{{ old('country') }}"
                        required>
                    @error('country')
                    <p class="form__error">{{ $message }}</p>
                    @enderror

                    <label for="city" class="form__label">Ciutat</label>
                    <input type="text" name="city" id="city" class="form__input" value="{{ old('city') }}"
                        required>
                    @error('city')
                    <p class="form__error">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Paso 3: Contacto -->
            <div class="wizard__form-step" data-step="3">
                <div class="form__column">
                    <h3 class="form__heading">Contacte</h3>

                    <label for="telephone" class="form__label">Telèfon</label>
                    <input type="tel" name="telephone" id="telephone" class="form__input"
                        value="{{ old('telephone') }}" required>
                    @error('telephone')
                    <p class="form__error">{{ $message }}</p>
                    @enderror

                    <label for="email" class="form__label">Correu electrònic</label>
                    <input type="email" name="email" id="email" class="form__input" value="{{ old('email') }}"
                        required>
                    @error('email')
                    <p class="form__error">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Paso 4: Configuración -->
            <div class="wizard__form-step" data-step="4">
                <div class="form__column">
                    <h3 class="form__heading">Configuració</h3>
                    <div class="form__grid">
                        <!-- Columna izquierda -->
                        <div class="form__grid-column">
                            <label for="num_rooms" class="form__label">Número d'habitacions</label>
                            <input type="number" name="num_rooms" id="num_rooms" class="form__input"
                                value="{{ old('num_rooms', 10) }}" required>
                            @error('num_rooms')
                            <p class="form__error">{{ $message }}</p>
                            @enderror

                            <label for="num_users" class="form__label">Número d'usuaris</label>
                            <input type="number" name="num_users" id="num_users" class="form__input"
                                value="{{ old('num_users', 10) }}" required>
                            @error('num_users')
                            <p class="form__error">{{ $message }}</p>
                            @enderror

                            <label for="num_reserves" class="form__label">Número de reserves</label>
                            <input type="number" name="num_reserves" id="num_reserves" class="form__input"
                                value="{{ old('num_reserves', 10) }}" required>
                            @error('num_reserves')
                            <p class="form__error">{{ $message }}</p>
                            @enderror
                        </div>
                        <!-- Columna derecha -->
                        <div class="form__grid-column">

                            <label for="num_reviews" class="form__label">Número de FeedBack</label>
                            <input type="number" name="num_reviews" id="num_reviews" class="form__input"
                                value="{{ old('num_reviews', 20) }}" required>
                            @error('num_reviews')
                            <p class="form__error">{{ $message }}</p>
                            @enderror

                            <label for="num_news" class="form__label">Número de Noticies</label>
                            <input type="number" name="num_news" id="num_news" class="form__input"
                                value="{{ old('num_news', 5) }}" required>
                            @error('num_news')
                            <p class="form__error">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>

            <!-- Paso 5: Recepcionista -->
            <div class="wizard__form-step" data-step="5">
                <div class="form__column">
                    <h3 class="form__heading">Recepcionista</h3>

                    <label for="receptionist_name" class="form__label">Nom d'usuari</label>
                    <input type="text" name="receptionist_name" id="receptionist_name" class="form__input"
                        value="{{ old('receptionist_name') }}" required>
                    @error('receptionist_name')
                    <p class="form__error">{{ $message }}</p>
                    @enderror

                    <label for="receptionist_password" class="form__label">Contrasenya</label>
                    <input type="password" name="receptionist_password" id="receptionist_password" class="form__input"
                        required>
                    @error('receptionist_password')
                    <p class="form__error">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Controles de Navegación -->
            <div class="wizard__form-actions">
                <button type="button" class="wizard__btn wizard__btn--prev">Anterior</button>
                <button type="button" class="wizard__btn wizard__btn--next">Següent</button>
                <button type="submit" class="wizard__btn wizard__btn--submit" style="display:none;">Crear hotel</button>
            </div>
        </form>
    </div>
</div>
@endsection