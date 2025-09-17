<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ApiController;
use App\Http\Controllers\Api\RoomController;
use App\Http\Controllers\Api\HotelController;
use App\Http\Controllers\Api\ReserveController;
use App\Http\Controllers\Api\SectionController;
use App\Http\Controllers\Api\NewsController;
use App\Http\Controllers\Api\ReviewController;
use App\Http\Controllers\Api\ReservationController;
use App\Http\Controllers\Api\FeedbackController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::get('/rooms/{hotelId}', [ApiController::class, 'getRooms']);
Route::put('/reserves/{reserve}/status', [ApiController::class, 'updateStatus']);
Route::put('/hotel/{roomNumber}/bloquejar', [ApiController::class, 'updateRoomStatus']);
Route::prefix('v1')->group(function () {
    // Para el buscador del frontend
    Route::get('/rooms/filter', [RoomController::class, 'getRoomsWithFilters']);
    Route::post('/reservacions', [ReserveController::class, 'storeReserve']);
    Route::get('/reservacions/{id}', [ReserveController::class, 'getRoomByHotelId']);
});


Route::prefix('v2')->group(function () {
    Route::get('/hotels/{identifier}', [HotelController::class, 'getHotelByIdentifier']);
    Route::get('/hotels/{identifier}/typerooms', [HotelController::class, 'getTypeRooms']);
    Route::get('/hotels/{identifier}/search', [HotelController::class, 'getSearch']);

    // Rutas para feedback
    Route::get('/verify-token/{token}', [ReviewController::class, 'verifyToken']);
    Route::post('/submit-feedback/{token}', [ReviewController::class, 'submitReview']);
    Route::apiResource('/hotels/{identifier}/feedbacks', ReviewController::class);

    // Ruta para reservas
    Route::post('/send-token', [ReservationController::class, 'sendToken']);
    Route::post('/verify-token', [ReservationController::class, 'verifyToken']);
    Route::post('/create-reserve', [ReservationController::class, 'createReservation']);

    // Ruta per sections
    Route::apiResource('/hotels/{identifier}/sections', SectionController::class);

    // Rutas para noticias
    Route::get('/hotels/{identifier}/noticies', [NewsController::class, 'index']);
    Route::get('/hotels/{identifier}/noticies/all', [NewsController::class, 'allNews']);
});

// Rutas de feedback
Route::prefix('v2/feedback')->group(function () {
    Route::get('validate/{token}', [FeedbackController::class, 'validateToken']);
    Route::post('/', [FeedbackController::class, 'store']);
    Route::get('/', [FeedbackController::class, 'index']);
});

// Ruta para enviar solicitud de feedback (protegida)
Route::post('v2/reservations/{reservation}/request-feedback', [FeedbackController::class, 'sendFeedbackRequest'])
    ->middleware('auth:sanctum');

// Ruta para enviar solicitud de review después del checkout
Route::post('v2/reserves/{reserve}/request-review', [ReviewController::class, 'sendReviewRequest']);
