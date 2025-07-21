<?php

namespace App\Http\Controllers\v1;

use App\Http\Controllers\Controller;
use App\Models\Industry;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class IndustryController extends Controller
{
    public function index()
    {
        return self::success(Industry::all(), 'success', 200);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'Name' => 'required|string|max:200|unique:industry,Name',
            'description' => 'nullable|string|max:255',
            'status' => 'nullable|integer'
        ]);

        try {
            $industry = Industry::create($validated);

            // Prepare the response data
            $data = ['industry' => $industry];

            return self::success($data, 'Industry ' . __('messages.created_successfully'), 201);
        } catch (\Exception $e) {

            // Log the error and return a generic response
            Log::error('Industry Create Error:', ['error' => $e]);
            return self::error(__('messages.something_went_wrong'), 500);
        }
    }

    public function show(string $id)
    {
        $industry = Industry::find($id);
        if (!$industry) return self::error('Industry ' . __('messages.notfound'), 404);

        try {

            // Prepare the response data
            $data = ['industry' => $industry];

            return self::success($data, 'Success', 200);
        } catch (\Exception $e) {

            return self::error(__('messages.something_went_wrong'), 500);
        }
    }

    public function update(Request $request, string $id)
    {
        $validated = $request->validate([
            'Name' => 'sometimes|string|max:200|unique:industry,Name,' . $id,
            'description' => 'nullable|string|max:255',
            'status' => 'nullable|integer'
        ]);


        $industry = Industry::find($id);
        if (!$industry) return self::error('Industry ' . __('messages.notfound'), 404);

        try {

            $industry->update($validated);

            // Prepare the response data
            $data = ['industry' => $industry];

            return self::success($data, 'Industry ' . __('messages.updated_successfully'), 201);
        } catch (\Exception $e) {

            // Log the error and return a generic response
            Log::error('Industry Update Error:', ['error' => $e]);
            return self::error(__('messages.something_went_wrong'), 500);
        }
    }

    public function destroy(string $id)
    {
        $resp = Industry::find($id);
        if (!$resp) return self::error(__('messages.record_not_found'), 404);

        try {
            $resp->delete();
            return self::success('', 'Industry ' . __('messages.deleted_successfully'), 200);
        } catch (\Exception $e) {
            Log::error('Industry delete Error:', ['error' => $e]);
            return self::error(__('messages.something_went_wrong'), 500);
        }
    }
}
