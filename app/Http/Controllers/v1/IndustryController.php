<?php

namespace App\Http\Controllers\v1;

use App\Http\Controllers\Controller;
use App\Models\Industry;
use Illuminate\Http\Request;

class IndustryController extends Controller
{
    public function index()
    {
        return response()->json(Industry::all());
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'Name' => 'required|string|max:200|unique:industry,Name',
            'description' => 'nullable|string|max:255',
            'status' => 'nullable|integer'
        ]);

        $industry = Industry::create($validated);
        return response()->json($industry, 201);
    }

    public function show(Industry $industry)
    {
        return response()->json($industry);
    }

    public function update(Request $request, Industry $industry)
    {
        $validated = $request->validate([
            'Name' => 'sometimes|string|max:200|unique:industry,Name,' . $industry->id,
            'description' => 'nullable|string|max:255',
            'status' => 'nullable|integer'
        ]);

        $industry->update($validated);
        return response()->json($industry);
    }

    public function destroy(Industry $industry)
    {
        $industry->delete();
        return response()->json(null, 204);
    }
}
