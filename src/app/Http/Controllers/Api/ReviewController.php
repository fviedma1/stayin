<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Review;
use App\Models\Hotel;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;
use App\Mail\ReviewRequest;
use App\Models\Reserve;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class ReviewController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index($identifier)
    {
        $hotel = Hotel::where('code', $identifier)->first();

        if (!$hotel) {
            return response()->json(['error' => 'Hotel no encontrado'], 404);
        }

        // Obtener todas las IDs de reservas para el hotel dado
        $reserveIdsForHotel = Reserve::whereHas('room', function ($query) use ($hotel) {
            $query->where('hotel_id', $hotel->id);
        })->pluck('id');

        // Obtener todas las reseñas para esas reservas, cargando relaciones necesarias
        $allReviews = Review::with(['reserve.user', 'reserve.room.typeRoom', 'images'])
                            ->whereIn('reserve_id', $reserveIdsForHotel)
                            ->orderBy('id', 'desc') // Ordenar por el ID de la review (reviews.id)
                            ->get();

        $reviews = $allReviews->map(function ($review) {
            $reviewData = [
                'id' => $review->id, // Añadir el ID de la review
                'reserve_id' => $review->reserve_id,
                'stars' => $review->stars,
                'comment' => $review->comment,
                'user' => $review->reserve->user->name ?? 'Usuario Desconocido',
                'room' => $review->reserve->room->typeRoom->name ?? 'Habitación Desconocida',
                'date_in' => $review->reserve->date_in,
                'date_out' => $review->reserve->date_out,
                'images' => []
            ];
            if ($review->images) {
                foreach ($review->images as $image) {
                    $reviewData['images'][] = Storage::url($image->image_path);
                }
            }
            return $reviewData;
        });

        return response()->json($reviews);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $Review = Review::create($request->all());
        return response()->json($Review, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $review = review::find($id);
        return response()->json($review);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $review = Review::find($id);
        $review->update($request->all());
        return response()->json($review);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $review = Review::find($id);
        $review->delete();
        return response()->json($review);
    }

    public function verifyToken($token)
    {
        $review = Review::where('token', $token)->first();

        if (!$review) {
            return response()->json([
                'success' => false,
                'message' => 'Token invalido.',
            ], 404);
        }

        if ($review->used) {
            return response()->json([
                'success' => false,
                'message' => 'Este token ya ha sido utilizado.',
            ], 400);
        }

        return response()->json([
            'success' => true,
            'message' => 'Token valido.',
            'reserve_id' => $review->reserve_id,
        ]);
    }

    public function submitReview(Request $request, $token)
    {
        $validatedData = $request->validate([
            'stars' => 'required|integer|min:1|max:5',
            'comment' => 'required|string|max:1000',
            'images' => 'nullable|array',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        $review = Review::where('token', $token)->where('used', false)->first();

        if (!$review) {
            return response()->json([
                'success' => false,
                'message' => 'Token invàlid o ja utilitzat.',
            ], 404);
        }

        $review->stars = $validatedData['stars'];
        $review->comment = $validatedData['comment'];
        $review->used = true;
        $review->save();

        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $imageFile) {
                $filename = uniqid('review_') . '-' . time() . '.' . $imageFile->getClientOriginalExtension();
                $path = $imageFile->storeAs('public/review_images', $filename);
                
                $webPath = 'review_images/' . $filename;

                $review->images()->create([
                    'image_path' => $webPath
                ]);
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Resenya enviada correctament.',
        ]);
    }

    /**
     * Enviar correo de solicitud de review después del checkout
     */
    public function sendReviewRequest(Reserve $reserve)
    {
        try {
            // Crear token para la review
            $review = Review::create([
                'reserve_id' => $reserve->id,
                'token' => Str::random(64),
                'used' => false
            ]);

            // Obtener datos para el correo
            $user = $reserve->user;
            $room = $reserve->room;
            $hotel = $room->hotel;

            // Enviar correo
            Mail::to($user->email)->send(new ReviewRequest([
                'userName' => $user->name,
                'hotelName' => $hotel->name,
                'reviewUrl' => env('FRONTEND_URL', 'http://localhost:5174') . "/feedback/" . $review->token
            ]));

            return response()->json(['message' => 'Correo de review enviado correctamente']);
        } catch (\Exception $e) {
            Log::error('Error enviando correo de review: ' . $e->getMessage());
            return response()->json(['error' => 'Error enviando correo de review'], 500);
        }
    }
}
