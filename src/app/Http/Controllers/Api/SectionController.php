<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Section;
use App\Models\Hotel;

class SectionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index($identifier)
    {
        // Buscar el hotel por su código
        $hotel = Hotel::where('code', $identifier)->first();

        

        if (!$hotel) {
            return response()->json(['error' => 'Hotel not found'], 404);
        }

        $sections = $hotel->sections;

        return response()->json($sections);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $sections = Section::create($request->all());
        return response()->json($sections, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $sections = Section::find($id);
        return response()->json($sections);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $sections = Section::find($id);
        $sections->update($request->all());
        return response()->json($sections);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $sections = Section::find($id);
        $sections->delete();
        return response()->json(null, 204);
    }
}
