<?php

namespace App\Http\Controllers\v1;

use App\Http\Controllers\Controller;
use App\Models\InquirySource;
use Illuminate\Http\Request;

class InquerySourceController extends Controller
{
    public function index()
    {
        return response()->json(InquirySource::all());
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            // ✅ Add your validation rules here
        ]);

        $item = InquirySource::create($validated);
        return response()->json($item, 201);
    }

    public function show(InquirySource $modelName)
    {
        return response()->json($modelName);
    }

    public function update(Request $request, InquirySource $modelName)
    {
        $validated = $request->validate([
            // ✅ Add your validation rules here
        ]);

        $modelName->update($validated);
        return response()->json($modelName);
    }

    public function destroy(InquirySource $modelName)
    {
        $modelName->delete();
        return response()->json(null, 204);
    }
}
