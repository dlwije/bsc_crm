<?php

namespace App\Http\Controllers\v1;

use App\Http\Controllers\Controller;
use App\Models\LeadStatus;
use Illuminate\Http\Request;

class LeadStatusController extends Controller
{
    public function index()
    {
        return response()->json(LeadStatus::all());
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            // ✅ Add your validation rules here
        ]);

        $item = LeadStatus::create($validated);
        return response()->json($item, 201);
    }

    public function show(LeadStatus $modelName)
    {
        return response()->json($modelName);
    }

    public function update(Request $request, LeadStatus $modelName)
    {
        $validated = $request->validate([
            // ✅ Add your validation rules here
        ]);

        $modelName->update($validated);
        return response()->json($modelName);
    }

    public function destroy(LeadStatus $modelName)
    {
        $modelName->delete();
        return response()->json(null, 204);
    }
}
