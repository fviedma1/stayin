<?php

use Illuminate\Support\Facades\Route;
use App\Http\Middleware\CheckYourRole;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\RoomController;
use App\Http\Controllers\NewsController;
use App\Http\Controllers\HotelController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\GestorController;
use App\Http\Controllers\ReserveController;
use App\Http\Controllers\HotelConfigController;
use App\Http\Controllers\SectionController;

// Ruta principal
Route::get('/', [HomeController::class, 'home'])->name('home');

Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login'])->name('auth.login.validate');
Route::get('/logout', [LoginController::class, 'logout'])->name('auth.logout');

// Rutas que requieren autenticación y rol admin
Route::middleware(['auth', CheckYourRole::class . ':admin'])->group(function () {
    Route::get('/hotel', [HotelController::class, 'index'])->name('hotel.index');
    Route::get('/hotel/show', [HotelController::class, 'show'])->name('hotel.show');
    Route::get('/hotel/show/{id}', [HotelController::class, 'showOne'])->name('hotel.showOne');
    Route::get('/hotel/create', [HotelController::class, 'create'])->name('hotel.create');
    Route::post('/hotels', [HotelController::class, 'store'])->name('hotel.store');
    Route::get('/hotel/{id}/description', [HotelController::class, 'description'])->name('hotel.description');

    // Noticias del hotel
    Route::get('/hotel/news', [NewsController::class, 'index'])->name('news.news');
    Route::get('/hotel/news/create', [NewsController::class, 'create'])->name('news.create');
    Route::post('/hotel/news/', [NewsController::class, 'store'])->name('news.store');
    Route::get('/hotel/news/{news}/edit', [NewsController::class, 'edit'])->name('news.edit');
    Route::put('/hotel/news/{news}', [NewsController::class, 'update'])->name('news.update');
    Route::delete('/hotel/news/{news}', [NewsController::class, 'destroy'])->name('news.destroy');
    Route::delete('/news/images/{image}', [NewsController::class, 'deleteImage'])->name('news.images.destroy');

    // Configuración del hotel
    Route::get('/hotel/{id}/config', [HotelController::class, 'config'])->name('hotel.config');
    Route::put('/hotel/{id}/config', [HotelController::class, 'updateConfig'])->name('hotel.config.update');

    // Secciones del hotel
    Route::get('/hotel/{hotelId}/section', [SectionController::class, 'index'])->name('section.index');
    Route::put('/hotel/{hotelId}/section/update', [SectionController::class, 'update'])->name('section.update');
});

// Gestor (recepcionista)
Route::middleware('auth')->group(function () {
    Route::get('/hotel/{hotelId}/gestor', [GestorController::class, 'index'])->name('recepcionist.gestor');
    Route::get('/hotel/{hotelId}/reserves', [GestorController::class, 'reserves'])->name('recepcionist.reserves');

    // Habitaciones y reservas
    Route::get('/hotel/{hotelId}/room/{id}', [RoomController::class, 'showOne'])->name('rooms.showOne');
    Route::get('/hotel/{hotelId}/reserves/create', [RoomController::class, 'show'])->name('rooms.show');
    Route::get('/hotel/{hotelId}/reserves/create/{roomId}', [ReserveController::class, 'create'])->name('recepcionist.create');
    Route::post('/reserves/store/{roomId}', [ReserveController::class, 'store'])->name('recepcionist.store');
});

// Accesos directos sin middleware
Route::get('/gestor/{hotelId}', [GestorController::class, 'index']);
Route::get('/reserves/{hotelId}', [GestorController::class, 'reserves']);
