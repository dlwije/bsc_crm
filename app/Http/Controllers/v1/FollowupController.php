<?php

namespace App\Http\Controllers\v1;

use App\Http\Controllers\Controller;
use App\Models\Followup;
use Illuminate\Http\Request;

class FollowupController extends Controller
{
    public function index()
    {
        return response()->json(Followup::all());
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            // ✅ Add your validation rules here
        ]);

        $item = Followup::create($validated);
        return response()->json($item, 201);
    }

    public function show(Followup $modelName)
    {
        return response()->json($modelName);
    }

    public function update(Request $request, Followup $modelName)
    {
        $validated = $request->validate([
            // ✅ Add your validation rules here
        ]);

        $modelName->update($validated);
        return response()->json($modelName);
    }

    public function destroy(Followup $modelName)
    {
        $modelName->delete();
        return response()->json(null, 204);
    }
}
