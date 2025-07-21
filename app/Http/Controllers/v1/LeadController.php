<?php

namespace App\Http\Controllers\v1;

use App\Http\Controllers\Controller;
use App\Models\Lead;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class LeadController extends Controller
{
    public function index()
    {
        return self::success(Lead::all(), 'success', 200);
    }

    public function store(Request $request)
    {
        // Define the base validation rules
        $validationRules = [
            'customerName' => 'required',
        ];

        // Validate the request data
        $validator = Validator::make($request->all(), $validationRules);

        // Check if validation fails
        if ($validator->fails()) {
            return self::error(__('messages.validation_error'), 422, $validator->errors());
        }

        try{
            $lead = new Lead();

            $lead->serialNo = $request->input('serialNo');
            $lead->date = $request->input('date');
            $lead->customerName = $request->input('customerName');
            $lead->companyName = $request->input('companyName');
            $lead->phone = $request->input('phone');
            $lead->industry = $request->input('industry');
            $lead->leadSource = $request->input('leadSource');
            $lead->leadStatus = $request->input('leadStatus');
            $lead->revenue = $request->input('revenue');
            $lead->product = $request->input('product');
            $lead->description = $request->input('description');
            $lead->responsiblePerson = $request->input('responsiblePerson');
            $lead->username = auth()->user()->userName;
            $lead->datetime = date('Y-m-d H:i:s');

            $lead->save();

            // Prepare the response data
            $data = [
                'lead' => $lead,
            ];

            return self::success($data, 'Lead '.__('messages.created_successfully'), 201);
        }catch (\Exception $e){

            // Log the error and return a generic response
            Log::error('Lead Create Error:', ['error' => $e]);
            return self::error(__('messages.something_went_wrong'), 500);
        }
    }

    public function show(string $id)
    {
        try {

            $lead = Lead::find($id);

            if(!$lead) {
                return self::error('Lead '.__('messages.notfound'), 404);
            }

// Prepare the response data
            $data = [
                'lead' => $lead,
            ];

            return self::success($data, 'Success', 200);
        }catch (\Exception $e) {

            return self::error(__('messages.something_went_wrong'), 500);
        }
    }

    public function update(Request $request, string $id)
    {
        // Define the base validation rules
        $validationRules = [
            'customerName' => 'required',
        ];

        // Validate the request data
        $validator = Validator::make($request->all(), $validationRules);

        // Check if validation fails
        if ($validator->fails()) {
            return self::error(__('messages.validation_error'), 422, $validator->errors());
        }

        DB::beginTransaction();
        try {

            $lead = Lead::find($id);

            if(!$lead) {
                return self::error('Lead '.__('messages.notfound'), 404);
            }

            $lead->serialNo = $request->input('serialNo');
            $lead->date = $request->input('date');
            $lead->customerName = $request->input('customerName');
            $lead->companyName = $request->input('companyName');
            $lead->phone = $request->input('phone');
            $lead->industry = $request->input('industry');
            $lead->leadSource = $request->input('leadSource');
            $lead->leadStatus = $request->input('leadStatus');
            $lead->revenue = $request->input('revenue');
            $lead->product = $request->input('product');
            $lead->description = $request->input('description');
            $lead->responsiblePerson = $request->input('responsiblePerson');
            $lead->datetime = date('Y-m-d H:i:s');
            $lead->updateusername = auth('api')->user()->userName;
            $lead->updatedatetime = now();
            $lead->save();

            DB::commit();
            // Prepare the response data
            $data = [
                'lead' => $lead,
            ];

            return self::success($data, 'Lead '.__('messages.updated_successfully'), 201);
        }catch (\Exception $e){

            // Log the error and return a generic response
            Log::error('Lead Update Error:', ['error' => $e]);
            DB::rollBack();
            return self::error(__('messages.something_went_wrong'), 500);
        }
    }

    public function destroy(string $id)
    {
        $resp = Lead::find($id);
        if (!$resp) {
            return self::error(__('messages.record_not_found'), 404);
        }
        try {
            $resp->delete();
            return self::success('', 'Lead '.__('messages.deleted_successfully'), 200);
        }catch (\Exception $e) {
            Log::error('Lead delete Error:', ['error' => $e]);
            return self::error(__('messages.something_went_wrong'), 500);
        }
    }
}
