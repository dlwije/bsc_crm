<?php

namespace App\Http\Controllers\v1;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;

class UserController extends Controller
{
    public function index()
    {

        try {
            $query = User::select('id', 'first_name', 'last_name', 'display_name', 'email', 'phone', 'is_active', 'is_sys_user', 'updated_at');

            return DataTables::of($query)
                ->addColumn('action', function ($row) {
                    return '<button type="button" onclick="{() => { setIsDeleteConfirmOpen(true); setDeletingId('.$row->id.'); }}" >Update Status</button>';
                })
                ->rawColumns(['action'])
                ->editColumn('updated_at', function ($user) {
                    return $user->updated_at->format('Y-m-d');
                })
                ->addIndexColumn()
                ->filterColumn('updated_at', function ($query, $keyword) {
                    $query->whereRaw("DATE_FORMAT(updated_at, '%Y-%m-%d') like ?", ["%$keyword%"]);
                })
                ->removeColumn('email_verified_at', 'created_at')
                ->make(true);
        } catch (\Exception $e) {

            Log::error('User List Getting Error:',['error' => $e->getMessage()]);
            return self::error(__('messages.something_went_wrong'), 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Define the base validation rules
        $validationRules = [
            'first_name' => 'required|string|max:255',
            'email' => 'required|email|unique:users|regex:/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,6}$/|max:255',
            'password' => 'required|string|confirmed|min:5',
            'roles' => 'required'
        ];

        // Add phone validation rule if phone is present and not empty
        if ($request->has('phone') && !empty($request->input('phone'))) {
            $validationRules['phone'] = 'nullable|string|regex:/^\+?[0-9]{10,14}$/';
        }

        // Validate the request data
        $validator = Validator::make($request->all(), $validationRules);

        // Check if validation fails
        if ($validator->fails()) {
            return self::error(__('messages.validation_error'), 422, $validator->errors());
        }

        DB::beginTransaction();
        try {
            // Prepare user data
            $userData = [
                'first_name' => $request->input('first_name'),
                'email' => $request->input('email'),
                'password' => bcrypt($request->input('password')), // Hash the password
                'ip' => $request->ip(),
                'is_sys_user' => 1,
            ];

            // Add phone if it is not empty
            if ($request->has('phone') && !empty($request->input('phone'))) {
                $userData['phone'] = $request->input('phone');
            }

            // Check if last_name is not empty and add it to the array
            if ($request->has('last_name') && !empty($request->input('last_name'))) {
                $userData['last_name'] = $request->input('last_name');
            }

            $user = User::create($userData);
            $user->syncRoles($request->input('roles'));

            // Prepare the response data
            $data = [
                'user' => $user,
            ];

            DB::commit();
            return self::success($data, 'User '.__('messages.registered_successfully'), 201); // Use HTTP 201 for resource creation
        } catch (\Exception $e) {
            // Log the error and return a generic response
            Log::error('User Registration Error:', ['error' => $e->getMessage()]);
            DB::rollBack();

            return self::error(__('messages.something_went_wrong'), 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try {

            $user = User::find($id);

            if(!$user) {
                return self::error('User '.__('messages.notfound'), 404);
            }

            // Get roles with only 'id' and 'name'
            $roles = $user->roles()->select('id', 'name')->get();

// Prepare the response data
            $data = [
                'user' => $user,
                'roles' => $roles,
            ];

            return self::success($data, 'Success', 200);
        }catch (\Exception $e) {

            return self::error(__('messages.something_went_wrong'), 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        // Define the base validation rules
        $validationRules = [
            'first_name' => 'required|string|max:255',
            'email' => 'required|email|regex:/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,6}$/|unique:users,email,'.$id,
            'password' => 'nullable|string|confirmed|min:5',
            'roles' => 'required'
        ];

        // Add phone validation rule if phone is present and not empty
        if ($request->has('phone') && !empty($request->input('phone'))) {
            $validationRules['phone'] = 'nullable|string|regex:/^\+?[0-9]{10,14}$/';
        }

        // Validate the request data
        $validator = Validator::make($request->all(), $validationRules);

        // Check if validation fails
        if ($validator->fails()) {
            return self::error(__('messages.validation_error'), 422, $validator->errors());
        }

        $input = $request->all();

        if(!empty($input['password'])){
            $input['password'] = bcrypt($input['password']);
        }else{
            $input = Arr::except($input,array('password'));
        }

        $user = User::find($id);

        if(!$user) {
            return self::error('User '.__('messages.notfound'), 404);
        }

        $user->update($input);
        DB::table('model_has_roles')->where('model_id', $id)->delete();

        $user->assignRole($request->input('roles'));

        // Prepare the response data
        $data = [
            'user' => $user,
        ];

        return self::success($data, 'User '.__('messages.updated_successfully'), 201); // Use HTTP 201 for resource creation
    }

    public function updateIsActive(Request $request, $id)
    {

        // Validate the request data
        $validator = Validator::make($request->all(), [
            'is_active' => 'required|boolean',
        ]);

        // Check if validation fails
        if ($validator->fails()) {
            return self::error(__('messages.validation_error'), 422, $validator->errors());
        }

        // Find the user by ID
        $user = User::find($id);

        if (!$user) {
            return self::error(__('messages.user_not_found'), 404);
        }

        // Update the is_active status
        $user->is_active = $request->is_active;
        $user->save();

        return self::success($user, __('messages.user_status_updated'), 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $resp = User::find($id)->delete();
        if($resp){

            return self::success('', 'User '.__('messages.deleted_successfully'), 204);
        }else{

            return self::error(__('messages.something_went_wrong'), 500);
        }
    }

    public function changePassword(Request $request)
    {
        $validatedData = $request->validate([
            'current_password' => 'required',
            'password' => ['required', 'confirmed',
                Password::min(5)
            ],
            'id'=>'required|exists:users,id'

        ],);
        if (!$validatedData) {
            return self::error(__('messages.validation_error'), 422, $validatedData->errors());
        }
        else
        {
            $user=User::findOrfail($request->id);
            if($user)
            {
                if(Hash::check($validatedData['current_password'], $user->password)) {
                    $user->password = Hash::make($request->password);
                    $user->save();
                    return self::success('', 'Password ' . __('messages.updated_successfully'), 201); // Use HTTP 201 for resource creation
                }
                else{
                    return self::error(__('messages.incorrect_password'), 422);
                }
            }
            else
            {
                return self::error(__('messages.something_went_wrong'), 500);
            }
        }
    }
}
