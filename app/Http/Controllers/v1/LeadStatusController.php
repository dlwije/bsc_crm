<?php

namespace App\Http\Controllers\v1;

use App\Http\Controllers\Controller;
use App\Models\LeadStatus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class LeadStatusController extends Controller
{
    public function index()
    {
        return self::success(LeadStatus::all(), 'success', 200);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'Name' => 'required|string|max:200|unique:lead_statuses,Name',
            'description' => 'nullable|string|max:255',
            'status' => 'nullable|integer'
        ]);

        try {
            $lead_status = LeadStatus::create($validated);

            // Prepare the response data
            $data = ['lead_status' => $lead_status];

            return self::success($data, 'Lead Status ' . __('messages.created_successfully'), 201);
        } catch (\Exception $e) {

            // Log the error and return a generic response
            Log::error('Lead Status Create Error:', ['error' => $e]);
            return self::error(__('messages.something_went_wrong'), 500);
        }
    }

    public function show(string $id)
    {
        $lead_status = LeadStatus::find($id);
        if (!$lead_status) return self::error('Record ' . __('messages.notfound'), 404);

        try {

            // Prepare the response data
            $data = ['lead_status' => $lead_status];

            return self::success($data, 'Success', 200);
        } catch (\Exception $e) {

            return self::error(__('messages.something_went_wrong'), 500);
        }
    }

    public function update(Request $request, string $id)
    {
        $validated = $request->validate([
            'Name' => 'sometimes|string|max:200|unique:lead_statuses,Name,' . $id,
            'description' => 'nullable|string|max:255',
            'status' => 'nullable|integer'
        ]);


        $lead_status = LeadStatus::find($id);
        if (!$lead_status) return self::error('Record ' . __('messages.notfound'), 404);

        try {

            $lead_status->update($validated);

            // Prepare the response data
            $data = ['lead_status' => $lead_status];

            return self::success($data, 'Lead Status ' . __('messages.updated_successfully'), 201);
        } catch (\Exception $e) {

            // Log the error and return a generic response
            Log::error('Lead Status Update Error:', ['error' => $e]);
            return self::error(__('messages.something_went_wrong'), 500);
        }
    }

    public function destroy(string $id)
    {
        $resp = LeadStatus::find($id);
        if (!$resp) return self::error(__('messages.record_not_found'), 404);

        try {
            $resp->delete();
            return self::success('', 'Lead Status ' . __('messages.deleted_successfully'), 200);
        } catch (\Exception $e) {
            Log::error('Lead Status delete Error:', ['error' => $e]);
            return self::error(__('messages.something_went_wrong'), 500);
        }
    }
}
