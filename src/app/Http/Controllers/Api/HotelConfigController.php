<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Hotel;
use App\Models\HotelConfig;
use Illuminate\Http\Request;

class HotelConfigController extends Controller
{
    public function showByCode($identifier)
    {
        // Buscar el hotel por el campo "code"
        $hotel = Hotel::where('code', $identifier)->firstOrFail();
        $config = $hotel->config;

        if (!$config) {
            // Crear configuración predeterminada si no existe
            $config = $hotel->config()->create([
                'news_limit' => 5,
                'comments_limit' => 5,
                'sections_order' => json_encode(['Habitacions' => 1, 'Noticies' => 2, 'Feedbacks' => 3]),
                'sections_visibility' => json_encode(['Habitacions' => 1, 'Noticies' => 1, 'Feedbacks' => 1])
            ]);
        }

        return response()->json([
            'news_limit' => $config->news_limit,
            'comments_limit' => $config->comments_limit,
            'sections_order' => json_decode($config->sections_order, true),
            'sections_visibility' => json_decode($config->sections_visibility, true)
        ]);
    }
}