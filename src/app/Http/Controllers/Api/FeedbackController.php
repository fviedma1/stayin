<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Feedback;
use App\Models\Reservation;
use App\Mail\FeedbackRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class FeedbackController extends Controller
{
    /**
     * Enviar correo de solicitud de feedback
     */
    public function sendFeedbackRequest(Reservation $reservation)
    {
        // Generar token único
        $token = Str::random(64);
        
        // Guardar token en la reserva
        $reservation->update([
            'feedback_token' => $token,
            'feedback_token_expires_at' => now()->addDays(7)
        ]);

        // Enviar correo
        Mail::to($reservation->email)->send(new FeedbackRequest([
            'userName' => $reservation->guest_name,
            'hotelName' => $reservation->hotel->name,
            'feedbackUrl' => config('app.frontend_url') . "/feedback?token=" . $token
        ]));

        return response()->json(['message' => 'Correo de feedback enviado']);
    }

    /**
     * Validar token de feedback
     */
    public function validateToken($token)
    {
        $reservation = Reservation::where('feedback_token', $token)
            ->where('feedback_token_expires_at', '>', now())
            ->whereNull('feedback_submitted_at')
            ->first();

        if (!$reservation) {
            return response()->json(['message' => 'Token inválido o expirado'], 400);
        }

        return response()->json(['valid' => true]);
    }

    /**
     * Guardar feedback
     */
    public function store(Request $request)
    {
        $request->validate([
            'token' => 'required|string',
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'required|string|max:1000',
            'images.*' => 'image|mimes:jpeg,png,jpg|max:2048'
        ]);

        $reservation = Reservation::where('feedback_token', $request->token)
            ->where('feedback_token_expires_at', '>', now())
            ->whereNull('feedback_submitted_at')
            ->firstOrFail();

        // Procesar imágenes
        $imageUrls = [];
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $path = $image->store('feedback-images', 'public');
                $imageUrls[] = Storage::url($path);
            }
        }

        // Crear feedback
        $feedback = Feedback::create([
            'reservation_id' => $reservation->id,
            'rating' => $request->rating,
            'comment' => $request->comment,
            'images' => $imageUrls
        ]);

        // Marcar feedback como enviado
        $reservation->update([
            'feedback_submitted_at' => now()
        ]);

        return response()->json([
            'message' => 'Feedback guardado correctamente',
            'feedback' => $feedback
        ]);
    }

    /**
     * Obtener todos los feedbacks de un hotel
     */
    public function index(Request $request)
    {
        $feedbacks = Feedback::with('reservation')
            ->whereHas('reservation.hotel', function($query) use ($request) {
                $query->where('id', $request->hotel_id);
            })
            ->latest()
            ->paginate(10);

        return response()->json($feedbacks);
    }
} 