<?php

namespace App\Http\Controllers;

use DB;
use App\Models\User;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(),
        [
            'name'     => 'required|string|min:3',
            'email'    => 'required|min:3|unique:users',
            'password' => 'required|min:6',
        ]);
        if($validator->fails())
        {
            return response()->json(['status'=>'error','errors'=>$validator->errors()]);
        }
        $user            = new User();
        $user-> name     = $request->name;
        $user-> email    = $request->email;
        $user-> password = bcrypt($request->password);
        $user-> save();
        return response()->json(['status'=>'success','data'=>$user]);
    }



    public function login()
    {
        $credentials = request(['email','password']);
        if($token = auth()->attempt($credentials))
        {
            $user = auth()->user();
            // Custom claims for roles/permissions
            /*$token = JWTAuth::fromUser($user, [
                'roles'       => $user->roles,
                'permissions' => $user->permissions,
            ]);*/
            return response()->json([
                'status' => 'success',
                'token'  => $token,
                'user'   => $user
            ]);
        }
        return response()->json(['status'=>'error']);
    }



    public function logout()
    {
        auth()->logout();
        return response()->json(['message'=>'successfully logged out']);
    }









    public function index(Request $request)
    {
        $data = User::orderBy('id','DESC')->paginate(5);
        return view('users.show_users',compact('data'))->with('i', ($request->input('page', 1) - 1) * 5);
    }



    /**
    * Show the form for creating a new resource.
    *
    * @return \Illuminate\Http\Response
    */
    public function create()
    {
        $roles = Role::pluck('name','name')->all();
        return view('users.index',compact('roles'));
    }



    /**
    * Store a newly created resource in storage.
    *
    * @param  \Illuminate\Http\Request  $request
    * @return \Illuminate\Http\Response
    */
    public function store(Request $request)
    {
        $this->validate($request, [
            'name'       => 'required',
            'email'      => 'required|email|unique:users,email',
            'password'   => 'required|same:confirm-password',
            'roles_name' => 'required'
        ]);
        $input = $request->all();
        $input['password'] = bcrypt($input['password']);
        $user = User::create($input);
        $user->assignRole($request->input('roles_name'));
        return redirect()->route('users.index')->with('success','تم اضافة المستخدم بنجاح');
    }



    /**
    * Display the specified resource.
    *
    * @param  int  $id
    * @return \Illuminate\Http\Response
    */
    public function show($id)
    {
        $user = User::find($id);
        return view('users.show',compact('user'));
    }



    /**
    * Show the form for editing the specified resource.
    *
    * @param  int  $id
    * @return \Illuminate\Http\Response
    */
    public function edit($id)
    {
        $user     = User::find($id);
        $roles    = Role::pluck('name','name')->all();
        $userRole = $user->roles->pluck('name','name')->all();
        return view('users.edit',compact('user','roles','userRole'));
    }



    /**
    * Update the specified resource in storage.
    *
    * @param  \Illuminate\Http\Request  $request
    * @param  int  $id
    * @return \Illuminate\Http\Response
    */
    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'name'     => 'required',
            'email'    => 'required|email|unique:users,email,'.$id,
            'password' => 'same:confirm-password',
            'roles'    => 'required'
        ]);
        $input = $request->all();
        if(!empty($input['password']))
        {
            $input['password'] = bcrypt($input['password']);
        }
        else
        {
            $input = array_except($input,array('password'));
        }
        $user = User::find($id);
        $user->update($input);
        DB::table('model_has_roles')->where('model_id',$id)->delete();
        $user->assignRole($request->input('roles'));
        return redirect()->route('users.index')->with('success','تم تحديث معلومات المستخدم بنجاح');
    }



    /**
    * Remove the specified resource from storage.
    *
    * @param  int  $id
    * @return \Illuminate\Http\Response
    */
    public function destroy(Request $request)
    {
        User::find($request->user_id)->delete();
        return redirect()->route('users.index')->with('success','تم حذف المستخدم بنجاح');
    }
}
