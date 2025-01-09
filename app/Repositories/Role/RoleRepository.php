<?php

namespace App\Repositories\Role;

use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleRepository implements RoleInterface
{
    public function getModel()
    {
        return new Role();
    }



    // Fetch data
    public function index($request)
    {
        $data = [];
        $roles = $this->getModel()
            ->with('permissions')
            ->when($request->name != null,function ($q) use($request){
                return $q->where('name','like','%'.$request->name.'%');
            })
            // ->paginate(10);
            ->get();

        $permissions = Permission::pluck('name','name')->all();

        $data['roles']       = $roles;
        $data['permissions'] = $permissions;

        return $data;
    }



    // Show  an existing data
    public function show($id)
    {
        $role            = $this->getModel()->with('permissions')->findOrFail($id);
        $rolePermissions = Permission::join("role_has_permissions","role_has_permissions.permission_id","=","permissions._id")
            ->where("role_has_permissions.role_id",$id)
            ->get();

        $data = [];
        $data['role'] = $role;
        $data['rolePermissions'] = $rolePermissions;

        return $data;
    }



    // Store a new data
    public function store($request)
    {
        $data        = $this->getModel()->create(['name' => $request->input('name')]);
        // $permissions = Permission::whereIn('name', $request->input('permissions'))->pluck('name');
        $permissions = $request->input('permissions');
        $data->syncPermissions($permissions);
        return $data;
    }



    // Update an existing data
    public function update($request, $id)
    {
        $data = $this->getModel()->findOrFail($id);
        $data->update(['name' => $request->input('name')]);
        $data->save();
        // $permissions = Permission::whereIn('name', $request->input('permissions'))->pluck('name');
        $permissions = $request->input('permissions');
        $data->syncPermissions($permissions);

        return $data;
    }



    // Delete a data
    public function destroy($id)
    {
        $data = $this->getModel()->findOrFail($id);
        $data->delete();
    }



    // Store a new permission
    public function storePermission($request)
    {
        $data        = Permission::create([
            'name'       => $request->input('name'),
            'guard_name' => $request->input('guard_name') ?? 'api',
        ]);
    }
}
