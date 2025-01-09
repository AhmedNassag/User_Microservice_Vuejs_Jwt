<?php

namespace App\Repositories\User;

use DB;
use App\Models\User;
use Spatie\Permission\Models\Role;

class UserRepository implements UserInterface
{
    public function getModel()
    {
        return new User();
    }



    // Fetch data
    public function index($request)
    {
        $data  = [];
        $users = $this->getModel()
            /*->with('roles')*/
            ->when($request->name != null,function ($q) use($request){
                return $q->where('name','like','%'.$request->name.'%');
            })
            // ->paginate(10);
            ->get();

        $roles = Role::pluck('name','name')->all();

        $data['users'] = $users;
        $data['roles'] = $roles;

        return $data;
    }



    // Show  an existing data
    public function show($id)
    {
        $data = $this->getModel()/*->with('roles')*/->findOrFail($id);

        return $data;
    }



    // Store a new data
    public function store($request)
    {
        $inputs = $request->all();
        $inputs['password'] = bcrypt($inputs['password']);
        $data = $this->getModel()->create($inputs);
        $data->assignRole($inputs['roles']);

        return $data;
    }



    // Update an existing data
    public function update($request, $id)
    {
        $inputs = $request->all();
        if(!empty($inputs['password']))
        {
            $inputs['password'] = bcrypt($inputs['password']);
        }
        else
        {
            // $inputs = array_except($inputs,array('password'));
            unset($inputs['password']);
        }
        $data = $this->getModel()->findOrFail($id);
        $data->update($inputs);
        DB::table('model_has_roles')->where('model_id',$id)->delete();
        $data->assignRole($inputs['roles']);

        return $data;
    }



    // Delete a data
    public function destroy($id)
    {
        $data = $this->getModel()->findOrFail($id);
        $data->delete();
    }
}
