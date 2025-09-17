@extends('layouts.login')

@section('title', 'Página del Login')

@section('content')

    <div class="login__container">
        <div class="login__section--left">
            <img 
                alt="Imagen decorativa de un sofá de espera" 
                src="{{ asset('images/fondo-create.svg') }}" 
                class="login__image" 
            />
        </div>
        <div class="login__section--right">
            <div class="login__form">
                <h2 class="login__form-title">Iniciar Sessió</h2>
                <form action="{{ route('login') }}" method="POST" class="login__form-fields">
                    @csrf
                    <label for="name" class="login__label">Nom</label>
                    <input 
                        id="name" 
                        name="name" 
                        type="text" 
                        placeholder="Introdueix el teu nom" 
                        class="login__input"
                    />
                    @if ($errors->has('name'))
                        <div class="login__error-message">
                            {{ $errors->first('name') }}
                        </div>
                    @endif
                    <label for="password" class="login__label">Contrasenya</label>
                    <input 
                        id="password" 
                        name="password" 
                        type="password" 
                        placeholder="Introdueix la teva contrasenya" 
                        class="login__input"
                    />
                    @if ($errors->has('password'))
                        <div class="login__error-message">
                            {{ $errors->first('password') }}
                        </div>
                    @endif
                    <button type="submit" class="login__button">Enviar</button>
                </form>
            </div>
        </div>
    </div>

@endsection
