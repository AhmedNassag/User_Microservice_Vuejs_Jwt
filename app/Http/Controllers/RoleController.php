<?php

namespace App\Http\Controllers;

use DB;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\Validator;

class RoleController extends Controller
{
    function __construct()
    {
        // $this->middleware('permission:read-role', ['only' => ['index']]);
        // $this->middleware('permission:show-role', ['only' => ['index']]);
        // $this->middleware('permission:create-role', ['only' => ['create','store']]);
        // $this->middleware('permission:update-role', ['only' => ['edit','update']]);
        // $this->middleware('permission:delete-role', ['only' => ['destroy']]);
    }



    public function index(Request $request)
    {
        $data = Role::orderBy('id','DESC')
        ->when($request->name != null,function ($q) use($request){
            return $q->where('name','like','%'.$request->name.'%');
        })
        ->paginate(10);
        /*return view('dashboard.roles.index')
        ->with([
            'data'   => $data,
            'name'   => $request->name,
        ]);*/
        return response()->json(['status'=>'success','data'=>$data]);
    }



    public function create()
    {
        $permission = Permission::get();
        return view('roles.create',compact('permission'));
    }



    public function store(Request $request)
    {
        try{
            $validator = Validator::make($request->all(),[
                'name'       => 'required|unique:roles,name',
                'permission' => 'required',
            ]);
            if($validator->fails())
            {
                return response()->json(['status'=>'error','errors'=>$validator->errors()]);
            }
            $data = Role::create(['name' => $request->input('name')]);
            $data->syncPermissions($request->input('permission'));
            // session()->flash('success');
            // return redirect()->route('roles.index');
            return response()->json(['status'=>200,'data'=>$data]);
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['error' => $e->getMessage()]);
        }
    }



    public function show($id)
    {
        $role            = Role::find($id);
        $rolePermissions = Permission::join("role_has_permissions","role_has_permissions.permission_id","=","permissions.id")
            ->where("role_has_permissions.role_id",$id)
            ->get();

        $data = [];
        $data['role'] = $role;
        $data['rolePermissions'] = $rolePermissions;
        // return view('roles.show',compact('role','rolePermissions'));
        return response()->json(['status'=>'success','data'=>$data]);
    }



    public function edit($id)
    {
        $role            = Role::find($id);
        $permission      = Permission::get();
        $rolePermissions = DB::table("role_has_permissions")
            ->where("role_has_permissions.role_id",$id)
            ->pluck('role_has_permissions.permission_id','role_has_permissions.permission_id')
            ->all();
        return view('roles.edit',compact('role','permission','rolePermissions'));
    }



    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'name'       => 'required',
            'permission' => 'required',
        ]);
        $data = Role::find($id);
        $data->name = $request->input('name');
        $data->save();
        $data->syncPermissions($request->input('permission'));
        // return redirect()->route('roles.index')->with('success','Role updated successfully');
        return response()->json(['status'=>'success','data'=>$data]);
    }



    public function destroy($id)
    {
        DB::table("roles")->where('id',$id)->delete();
        // return redirect()->route('roles.index')->with('success','Role deleted successfully');
        return response()->json(['status'=>'success','data'=> 'Role deleted successfully']);
    }
}
