<?php

namespace App\Http\Controllers;

use App\Models\Feedback;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class CheckoutController extends Controller
{
    public function checkout(Request $request, $userId)
    {
        $user = User::findOrFail($userId);
        $hotelId = $request->input('hotel_id');

        // Crear un nuevo feedback con un token único
        $feedback = Feedback::create([
            'hotel_id' => $hotelId,
            'user_id' => $user->id,
            'token' => Str::random(32),
        ]);

        // Enviar correo al cliente
        Mail::to($user->email)->send(new \App\Mail\FeedbackRequestMail($feedback));

        return response()->json(['message' => 'Checkout realizado y correo enviado.']);
    }
}