<?php

namespace App\Http\Controllers\v1;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index()
    {
        return response()->json(Product::all());
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            // ✅ Add your validation rules here
        ]);

        $item = Product::create($validated);
        return response()->json($item, 201);
    }

    public function show(Product $modelName)
    {
        return response()->json($modelName);
    }

    public function update(Request $request, Product $modelName)
    {
        $validated = $request->validate([
            // ✅ Add your validation rules here
        ]);

        $modelName->update($validated);
        return response()->json($modelName);
    }

    public function destroy(Product $modelName)
    {
        $modelName->delete();
        return response()->json(null, 204);
    }
}
