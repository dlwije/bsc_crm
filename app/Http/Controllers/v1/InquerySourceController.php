<?php

namespace App\Http\Controllers\v1;

use App\Http\Controllers\Controller;
use App\Models\InquirySource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class InquerySourceController extends Controller
{
    public function index()
    {
        return self::success(InquirySource::all(), 'success', 200);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'Name' => 'required|string|max:200|unique:inquiry_source,Name',
            'description' => 'nullable|string|max:255',
            'status' => 'nullable|integer'
        ]);

        try {
            $inquiry_source = InquirySource::create($validated);

            // Prepare the response data
            $data = ['inquiry_source' => $inquiry_source];

            return self::success($data, 'Inquiry Source ' . __('messages.created_successfully'), 201);
        } catch (\Exception $e) {

            // Log the error and return a generic response
            Log::error('Inquiry Source Create Error:', ['error' => $e]);
            return self::error(__('messages.something_went_wrong'), 500);
        }
    }

    public function show(string $id)
    {
        $inquiry_source = InquirySource::find($id);
        if (!$inquiry_source) return self::error('Record ' . __('messages.notfound'), 404);

        try {

            // Prepare the response data
            $data = ['inquiry_source' => $inquiry_source];

            return self::success($data, 'Success', 200);
        } catch (\Exception $e) {

            return self::error(__('messages.something_went_wrong'), 500);
        }
    }

    public function update(Request $request, string $id)
    {
        $validated = $request->validate([
            'Name' => 'sometimes|string|max:200|unique:inquiry_source,Name,' . $id,
            'description' => 'nullable|string|max:255',
            'status' => 'nullable|integer'
        ]);


        $inquiry_source = InquirySource::find($id);
        if (!$inquiry_source) return self::error('Record ' . __('messages.notfound'), 404);

        try {

            $inquiry_source->update($validated);

            // Prepare the response data
            $data = ['inquiry_source' => $inquiry_source];

            return self::success($data, 'Inquiry Source ' . __('messages.updated_successfully'), 201);
        } catch (\Exception $e) {

            // Log the error and return a generic response
            Log::error('Inquiry Source Update Error:', ['error' => $e]);
            return self::error(__('messages.something_went_wrong'), 500);
        }
    }

    public function destroy(string $id)
    {
        $resp = InquirySource::find($id);
        if (!$resp) return self::error(__('messages.record_not_found'), 404);

        try {
            $resp->delete();
            return self::success('', 'Inquiry Source ' . __('messages.deleted_successfully'), 200);
        } catch (\Exception $e) {
            Log::error('Inquiry Source delete Error:', ['error' => $e]);
            return self::error(__('messages.something_went_wrong'), 500);
        }
    }
}
