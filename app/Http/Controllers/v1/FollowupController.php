<?php

namespace App\Http\Controllers\v1;

use App\Http\Controllers\Controller;
use App\Models\Followup;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class FollowupController extends Controller
{
    public function index()
    {
        return self::success(Followup::all(), 'success', 200);
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
            $followup = new Followup();

            $followup->serialNo = $request->input('serialNo');
            $followup->lead_serialNo = $request->input('lead_serialNo');
            $followup->date = $request->input('date');
            $followup->customerName = $request->input('customerName');
            $followup->companyName = $request->input('companyName');
            $followup->phone = $request->input('phone');
            $followup->industry = $request->input('industry');
            $followup->description = $request->input('description');
            $followup->responsiblePerson = $request->input('responsiblePerson');
            $followup->username = auth()->user()->userName;
            $followup->datetime = date('Y-m-d H:i:s');

            $followup->save();

            // Prepare the response data
            $data = [
                'followup' => $followup,
            ];

            return self::success($data, 'Followup '.__('messages.created_successfully'), 201);
        }catch (\Exception $e){

            // Log the error and return a generic response
            Log::error('Followup Update Error:', ['error' => $e]);
            return self::error(__('messages.something_went_wrong'), 500);
        }
    }

    public function show(string $id)
    {
        try {

            $followup = Followup::find($id);

            if(!$followup) {
                return self::error('Followup '.__('messages.notfound'), 404);
            }

// Prepare the response data
            $data = [
                'followup' => $followup,
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

            $followup = Followup::find($id);

            if(!$followup) {
                return self::error('Followup '.__('messages.notfound'), 404);
            }

            $followup->serialNo = $request->input('serialNo');
            $followup->lead_serialNo = $request->input('lead_serialNo');
            $followup->date = $request->input('date');
            $followup->customerName = $request->input('customerName');
            $followup->companyName = $request->input('companyName');
            $followup->phone = $request->input('phone');
            $followup->industry = $request->input('industry');
            $followup->description = $request->input('description');
            $followup->responsiblePerson = $request->input('responsiblePerson');
            $followup->datetime = date('Y-m-d H:i:s');
            $followup->updateusername = auth('api')->user()->userName;
            $followup->updatedatetime = now();
            $followup->save();

            DB::commit();
            // Prepare the response data
            $data = [
                'followup' => $followup,
            ];

            return self::success($data, 'Followup '.__('messages.updated_successfully'), 201);
        }catch (\Exception $e){

            // Log the error and return a generic response
            Log::error('Followup Update Error:', ['error' => $e]);
            DB::rollBack();
            return self::error(__('messages.something_went_wrong'), 500);
        }
    }

    public function destroy(string $id)
    {
        $resp = Followup::find($id);
        if (!$resp) {
            return self::error(__('messages.record_not_found'), 404);
        }
        try {
            $resp->delete();
            return self::success('', 'Followup '.__('messages.deleted_successfully'), 200);
        }catch (\Exception $e) {
            Log::error('Followup delete Error:', ['error' => $e]);
            return self::error(__('messages.something_went_wrong'), 500);
        }
    }
}
