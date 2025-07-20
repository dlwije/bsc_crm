<?php

namespace App\Http\Controllers\v1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class RoleController extends Controller
{
    public function index()
    {
        try {
            $query = Role::query();

            return DataTables::of($query)
                ->editColumn('updated_at', function ($user) {
                    return $user->updated_at->format('Y-m-d');
                })
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    return '<button type="button" onclick="deleteConfirm(' . $row->id . ')" class="btn btn-danger btn-sm">Delete</button>';
                })
                ->rawColumns(['action'])
                ->filterColumn('updated_at', function ($query, $keyword) {
                    $query->whereRaw("DATE_FORMAT(updated_at, '%Y-%m-%d') like ?", ["%$keyword%"]);
                })
                ->removeColumn('guard_name', 'created_at')
                ->make(true);
        } catch (\Exception $e) {

            Log::error('Role List Getting Error:',['error' => $e]);
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
            'name' => 'required|unique:roles,name',
            'permissions' => 'required|array',
            'permissions.*' => 'string|exists:permissions,name'
        ];

        // Validate the request data
        $validator = Validator::make($request->all(), $validationRules);

        // Check if validation fails
        if ($validator->fails()) {
            return self::error(__('messages.validation_error'), 422, $validator->errors());
        }

        DB::beginTransaction();
        try{

            $role = Role::create(['name' => $request->input('name')]);
            $role->syncPermissions($request->input('permissions'));

            // Prepare the response data
            $data = [
                'role' => $role,
            ];

            DB::commit();
            return self::success($data, 'Role '.__('messages.created_successfully'), 201); // Use HTTP 201 for resource creation
        }catch (\Exception $e){

            // Log the error and return a generic response
            Log::error('Role Creation Error:', ['error' => $e]);
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
            $permission_head = PermissionHead::query()->select('id', 'permission_title')->get();
            $role = Role::select('id','name','guard_name')->findOrFail($id);
            $rolePermissions = Permission::join("role_has_permissions","role_has_permissions.permission_id","=","permissions.id")
                ->where("role_has_permissions.role_id",$id)
                ->select("permissions.id","permissions.header_id","permissions.name","permissions.guard_name")
                ->get();

            if(!$role) {
                return self::error('Role '.__('messages.notfound'), 404);
            }

            // Prepare the response data
            $data = [
                'role' => $role,
                'permission_head' => $permission_head,
                'role_permissions' => $rolePermissions
            ];

            return self::success($data, 'Success', 200);
        }catch (\Exception $e) {

            // Log the error and return a generic response
            Log::error('Role Showing Error:', ['error' => $e]);
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
            'name' => 'required|unique:roles,name,' . $id, // Exclude the current role from uniqueness check
            'permissions' => 'required|array',
            'permissions.*' => 'string|exists:permissions,name'
        ];

        // Validate the request data
        $validator = Validator::make($request->all(), $validationRules);

        // Check if validation fails
        if ($validator->fails()) {
            return self::error(__('messages.validation_error'), 422, $validator->errors());
        }

        DB::beginTransaction();
        try {

            $role = Role::find($id);

            if(!$role) {
                return self::error('Role '.__('messages.notfound'), 404);
            }

            $role->name = $request->input('name');
            $role->save();

            $role->syncPermissions($request->input('permissions'));
            DB::commit();
            // Prepare the response data
            $data = [
                'role' => $role,
            ];

            return self::success($data, 'Role '.__('messages.updated_successfully'), 201);
        }catch (\Exception $e){

            // Log the error and return a generic response
            Log::error('Role Update Error:', ['error' => $e]);
            DB::rollBack();
            return self::error(__('messages.something_went_wrong'), 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try{

            Role::findOrfail($id)->delete();

            return self::success('', 'Role '.__('messages.deleted_successfully'), 204);
        }catch (\Exception $e){
            Log::error('Role Delete Error:', ['error' => $e]);

            return self::error(__('messages.something_went_wrong'), 500);
        }
    }

    public function getAllPermissions()
    {
        try {
            $permissions = DB::table((new PermissionHead())->getTable().' as ph')
                ->leftJoin((new Permission())->getTable().' as p', 'ph.id', '=', 'p.header_id')
                ->select(
                    'ph.id as head_id',
                    'ph.permission_title',
                    'p.id as permission_id',
                    'p.name as permission_name',
                    'p.guard_name'
                )
                ->orderBy('ph.id')
                ->orderBy('p.id')
                ->get()
                ->groupBy('head_id')
                ->map(function ($items) {
                    $head = $items->first();
                    return [
                        'id' => $head->head_id,
                        'title' => $head->permission_title,
                        'permissions' => $items->map(function ($item) {
                            return [
                                'id' => $item->permission_id,
                                'name' => $item->permission_name,
                            ];
                        })->filter()->values(), // Remove null permissions if no match
                    ];
                })
                ->values();


            return self::success($permissions, 'Success', 200);
        }catch (\Exception $e) {

            // Log the error and return a generic response
            Log::error('Permission Showing Error:', ['error' => $e]);
            return self::error(__('messages.something_went_wrong'), 500);
        }
    }
}
