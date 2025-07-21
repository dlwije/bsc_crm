<?php

namespace App\Http\Controllers\v1;

use App\Http\Controllers\Controller;
use App\Models\Contact;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class ContactController extends Controller
{
    public function index()
    {
        return self::success(Contact::all(), 'success', 200);
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
            $contact = new Contact();

            $contact->serialNo = $request->input('serialNo');
            $contact->date = $request->input('date');
            $contact->customerName = $request->input('customerName');
            $contact->companyName = $request->input('companyName');
            $contact->phone = $request->input('phone');
            $contact->email = $request->input('email');
            $contact->reason = $request->input('reason');
            $contact->description = $request->input('description');
            $contact->responsiblePerson = $request->input('responsiblePerson');
            $contact->username = auth()->user()->userName;
            $contact->datetime = date('Y-m-d H:i:s');

            $contact->save();

            // Prepare the response data
            $data = [
                'contact' => $contact,
            ];

            return self::success($data, 'Contact '.__('messages.created_successfully'), 201);
        }catch (\Exception $e){

            // Log the error and return a generic response
            Log::error('Contact Update Error:', ['error' => $e]);
            return self::error(__('messages.something_went_wrong'), 500);
        }
    }

    public function show(string $id)
    {
        try {

            $contact = Contact::find($id);

            if(!$contact) {
                return self::error('Contact '.__('messages.notfound'), 404);
            }

// Prepare the response data
            $data = [
                'contact' => $contact,
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

            $contact = Contact::find($id);

            if(!$contact) {
                return self::error('Contact '.__('messages.notfound'), 404);
            }

            $contact->serialNo = $request->input('serialNo');
            $contact->date = $request->input('date');
            $contact->customerName = $request->input('customerName');
            $contact->companyName = $request->input('companyName');
            $contact->phone = $request->input('phone');
            $contact->email = $request->input('email');
            $contact->reason = $request->input('reason');
            $contact->description = $request->input('description');
            $contact->responsiblePerson = $request->input('responsiblePerson');
//            $contact->username = $request->input('username');
            $contact->datetime = date('Y-m-d H:i:s');
            $contact->updateusername = auth('api')->user()->userName;
            $contact->updatedatetime = now();
            $contact->save();

            DB::commit();
            // Prepare the response data
            $data = [
                'contact' => $contact,
            ];

            return self::success($data, 'Contact '.__('messages.updated_successfully'), 201);
        }catch (\Exception $e){

            // Log the error and return a generic response
            Log::error('Contact Update Error:', ['error' => $e]);
            DB::rollBack();
            return self::error(__('messages.something_went_wrong'), 500);
        }
    }

    public function destroy(string $id)
    {
        $resp = Contact::find($id);
        if (!$resp) {
            return self::error(__('messages.contact_not_found'), 404);
        }
        try {
            $resp->delete();
            return self::success('', 'Contact '.__('messages.deleted_successfully'), 200);
        }catch (\Exception $e) {
            Log::error('Contact delete Error:', ['error' => $e]);
            return self::error(__('messages.something_went_wrong'), 500);
        }
    }
}
