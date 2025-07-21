<?php

namespace App\Http\Controllers\v1;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ProductController extends Controller
{
    public function index()
    {
        return self::success(Product::all(), 'success', 200);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'code' => 'nullable|string|max:255',
            'name' => 'required|string|max:200|unique:products,name',
            'price' => 'required|numeric',
            'description' => 'nullable|string|max:255',
            'status' => 'nullable|integer'
        ]);

        try {
            $product = Product::create($validated);

            // Prepare the response data
            $data = ['product' => $product];

            return self::success($data, 'Product ' . __('messages.created_successfully'), 201);
        } catch (\Exception $e) {

            // Log the error and return a generic response
            Log::error('Product Create Error:', ['error' => $e]);
            return self::error(__('messages.something_went_wrong'), 500);
        }
    }

    public function show(string $id)
    {
        $product = Product::find($id);
        if (!$product) return self::error('Record ' . __('messages.notfound'), 404);

        try {

            // Prepare the response data
            $data = ['product' => $product];

            return self::success($data, 'Success', 200);
        } catch (\Exception $e) {

            return self::error(__('messages.something_went_wrong'), 500);
        }
    }

    public function update(Request $request, string $id)
    {
        $validated = $request->validate([
            'code' => 'nullable|string|max:255',
            'name' => 'required|string|max:200|unique:products,name,' . $id,
            'price' => 'required|numeric',
            'description' => 'nullable|string|max:255',
            'status' => 'nullable|integer'
        ]);


        $product = Product::find($id);
        if (!$product) return self::error('Record ' . __('messages.notfound'), 404);

        try {

            $product->update($validated);

            // Prepare the response data
            $data = ['product' => $product];

            return self::success($data, 'Product ' . __('messages.updated_successfully'), 201);
        } catch (\Exception $e) {

            // Log the error and return a generic response
            Log::error('Product Update Error:', ['error' => $e]);
            return self::error(__('messages.something_went_wrong'), 500);
        }
    }

    public function destroy(string $id)
    {
        $resp = Product::find($id);
        if (!$resp) return self::error(__('messages.record_not_found'), 404);

        try {
            $resp->delete();
            return self::success('', 'Product ' . __('messages.deleted_successfully'), 200);
        } catch (\Exception $e) {
            Log::error('Product delete Error:', ['error' => $e]);
            return self::error(__('messages.something_went_wrong'), 500);
        }
    }
}
