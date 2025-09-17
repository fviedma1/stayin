<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\News;
use App\Models\Hotel;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class NewsController extends Controller
{
    public function index()
    {
        $news = News::with(['images' => function ($query) {
            $query->latest()->take(1);
        }, 'hotels'])->paginate(6);

        $hotels = Hotel::all();
        return view('news.news', compact('news', 'hotels'));
    }
    
    public function create()
    {
        $hotels = Hotel::all();
        return view('news.create', compact('hotels'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|max:255',
            'short_description' => 'required',  // Cambiado de 'description' a 'short_description'
            'long_description' => 'nullable',
            'images' => 'sometimes|array',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
            'published' => 'sometimes|boolean',
            'hotels' => 'sometimes|array',
            'hotels.*' => 'exists:hotels,id',
        ]);

        Log::info('Validación exitosa', ['data' => $validated]);

        // Crear la noticia con los campos correctos
        $news = News::create([
            'title' => $validated['title'],
            'short_description' => $validated['short_description'],  // Nombre correcto
            'long_description' => $validated['long_description'] ?? null,
            'published' => $request->has('published')
        ]);
        
        Log::info('Noticia creada en la base de datos', ['news_id' => $news->id]);

        // Procesar imágenes
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                try {
                    $filename = $news->id . '_' . time() . '_' . $image->getClientOriginalName();
                    $path = Storage::disk('public')->putFileAs('news', $image, $filename);

                    // Guardar en images_news
                    $news->images()->create([
                        'image' => Storage::url($path)
                    ]);
                } catch (\Exception $e) {
                    Log::error('Error al guardar la imagen', ['error' => $e->getMessage()]);
                    $news->delete();
                    return redirect()->route('news.news')->with('error', 'Error al guardar las imágenes.');
                }
            }
        }
        
        // Sincronizar hoteles (si aplica)
        if (isset($validated['hotels'])) {
            $news->hotels()->sync($validated['hotels']);
        }

        return redirect()->route('news.news')->with('success', 'Noticia creada exitosamente.');
    }

    public function edit(News $news)
    {
        $hotels = Hotel::all();
        
        // Preparar URLs de imágenes para la vista
        $news->image_urls = $news->images->pluck('image')->toArray();
        
        return view('news.edit', compact('news', 'hotels'));
    }

    public function update(Request $request, News $news)
    {
        $validated = $request->validate([
            'title' => 'required|max:255',
            'short_description' => 'required',  // Cambiado de 'description' a 'short_description'
            'long_description' => 'nullable',
            'images' => 'sometimes|array',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
            'published' => 'sometimes|boolean',
            'hotels' => 'sometimes|array',
            'hotels.*' => 'exists:hotels,id',
        ]);

        // Actualizar campos básicos
        $news->update([
            'title' => $validated['title'],
            'short_description' => $validated['short_description'],  // Nombre correcto
            'long_description' => $validated['long_description'] ?? null,
            'published' => $request->has('published')
        ]);

        // Procesar nuevas imágenes
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $filename = $news->id . '_' . time() . '_' . $image->getClientOriginalName();
                $path = Storage::disk('public')->putFileAs('news', $image, $filename);
                $news->images()->create(['image' => Storage::url($path)]);
            }
        }

        // Sincronizar hoteles
        $news->hotels()->sync($validated['hotels'] ?? []);

        return redirect()->route('news.news')->with('success', 'Noticia actualizada con éxito.');
    }

    public function destroy(News $news)
    {
        try {
            // Eliminar todas las imágenes asociadas
            foreach ($news->images as $image) {
                $imagePath = str_replace('/storage/', '', $image->image);
                Storage::disk('public')->delete($imagePath);
            }
            
            // Eliminar la noticia
            $news->delete();
            
            return redirect()->route('news.news')->with('success', 'Noticia eliminada con éxito.');
        } catch (\Exception $e) {
            Log::error('Error al eliminar la noticia', ['error' => $e->getMessage()]);
            return redirect()->route('news.news')->with('error', 'Error al eliminar la noticia.');
        }
    }
}