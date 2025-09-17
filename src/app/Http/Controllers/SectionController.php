<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\Hotel;
use App\Models\Section;

class SectionController extends Controller
{
    // Método para mostrar las secciones
    public function index($hotelId)
    {
        // Obtener el hotel y sus secciones
        $hotel = Hotel::findOrFail($hotelId);
        $sections = $hotel->sections()->orderBy('order')->get();

        // Pasar los datos a la vista
        return view('section.index', [
            'hotel' => $hotel,
            'sections' => $sections,
        ]);
    }

    // Método para actualizar las secciones
    public function update(Request $request, $hotelId)
    {
        try {
            // Registrar los datos recibidos
            Log::info('Datos recibidos:', $request->all());

            // Validar los datos recibidos
            $request->validate([
                'sections' => 'required|array',
                'sections.*.id' => 'required|exists:sections,id',
                'sections.*.order' => 'required|integer|max:3',
                'sections.*.is_visible' => 'required|boolean',
                'sections.*.display_count' => 'nullable|integer|min:1',
            ]);

            // Obtener el hotel
            $hotel = Hotel::findOrFail($hotelId);

            // Obtener la sección "Tipus d'habitacions"
            $roomTypesSection = Section::where('slug', 'room_types')->first();

            // Actualizar cada sección
            foreach ($request->sections as $sectionData) {
                $updateData = [
                    'order' => $sectionData['order'],
                    'is_visible' => $sectionData['is_visible'],
                ];

                // Si es la sección "Tipus d'habitacions", forzar display_count a null
                if ($sectionData['id'] == $roomTypesSection->id) {
                    $updateData['display_count'] = null;
                } else {
                    // Para otras secciones, actualizar display_count si está presente
                    if (isset($sectionData['display_count'])) {
                        $updateData['display_count'] = $sectionData['display_count'];
                    }
                }

                $hotel->sections()->updateExistingPivot($sectionData['id'], $updateData);
            }

            return redirect()->route('section.index', $hotelId)->with('success', 'Seccions actualitzades correctament');
        } catch (\Exception $e) {
            Log::error('Error al actualizar las secciones', ['error' => $e->getMessage()]);
            return response()->json(['error' => 'Ha ocurrido un error al actualizar las secciones'], 500);
        }
    }
}
