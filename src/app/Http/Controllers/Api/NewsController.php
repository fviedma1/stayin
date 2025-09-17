<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\News;
use App\Models\Hotel;

class NewsController extends Controller
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

        // Obtener las 3 últimas noticias publicadas
        $news = $hotel->news()
            ->where('published', 1)
            ->orderBy('created_at', 'desc')
            ->take(3)
            ->with('images', 'hotels')
            ->get();

        return response()->json($news);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $news = News::create($request->all());
        return response()->json($news, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $news = News::find($id);
        return response()->json($news);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $news = News::find($id);
        $news->update($request->all());
        return response()->json($news);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $news = News::find($id);
        $news->delete();
        return response()->json($news);
    }

    public function allNews($identifier)
    {
        $hotel = Hotel::where('code', $identifier)->first();

        if (!$hotel) {
            return response()->json(['error' => 'Hotel no encontrado'], 404);
        }

        // Obtener la configuración de la sección de noticias
        $newsSection = $hotel->sections()->where('slug', 'news')->first();
        $displayCount = $newsSection ? ($newsSection->pivot->display_count ?? 10) : 10;

        // Obtener todas las noticias publicadas
        $news = $hotel->news()
            ->where('published', 1)
            ->orderBy('created_at', 'desc')
            ->with('images', 'hotels')
            ->paginate($displayCount);

        // Devolver directamente el resultado de la paginación
        // Laravel automáticamente lo convertirá al formato correcto
        return $news;
    }
}
