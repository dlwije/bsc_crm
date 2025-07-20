<?php

namespace App\Http\Controllers\v1;

use App\Http\Controllers\Controller;
use App\Models\Lead;
use Illuminate\Http\Request;

class LeadController extends Controller
{
    public function index()
    {
        return response()->json(Lead::all());
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            // ✅ Add your validation rules here
        ]);

        $item = Lead::create($validated);
        return response()->json($item, 201);
    }

    public function show(Lead $modelName)
    {
        return response()->json($modelName);
    }

    public function update(Request $request, Lead $modelName)
    {
        $validated = $request->validate([
            // ✅ Add your validation rules here
        ]);

        $modelName->update($validated);
        return response()->json($modelName);
    }

    public function destroy(Lead $modelName)
    {
        $modelName->delete();
        return response()->json(null, 204);
    }
}
