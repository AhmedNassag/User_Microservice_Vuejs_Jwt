<?php

namespace App\Http\Controllers;

use DB;
use App\Models\User;
use Tymon\JWTAuth\Facades\JWTAuth;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;



use Illuminate\Http\Request;
use App\Traits\ApiResponseTrait;
use App\Resources\User\UserResource;
use App\Repositories\User\UserInterface;
use App\Http\Requests\User\StoreRequest;
use App\Http\Requests\User\UpdateRequest;

class UserController extends Controller
{
    use ApiResponseTrait;

    protected $user;

    function __construct(UserInterface $user)
    {
        $this->user = $user;

        // $this->middleware('permission:read-user', ['only' => ['index']]);
        // $this->middleware('permission:show-user', ['only' => ['index']]);
        // $this->middleware('permission:create-user', ['only' => ['create','store']]);
        // $this->middleware('permission:update-user', ['only' => ['edit','update']]);
        // $this->middleware('permission:delete-user', ['only' => ['destroy']]);
    }



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

            return response()->json([
                'status' => 'success',
                'token'  => $token,
                'user'   => $user,
            ]);
        }
        return response()->json(['status'=>'error']);
    }



    public function logout()
    {
        auth()->logout();
        return response()->json(['message'=>'successfully logged out']);
    }










    // Fetch data
    public function index(Request $request)
    {
        try {
            $data = $this->user->index($request);
            // return $this->apiResponse(UserResource::collection($data), 'Data Returned Successfully', 200);
            return $this->apiResponse($data, 'Data Returned Successfully', 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }



    // Show an existing data
    public function show($id)
    {
        try {
            $data = $this->user->show($id);
            // return $this->apiResponse(new UserResource($data), 'Data Returned Successfully', 200);
            return $this->apiResponse($data, 'Data Returned Successfully', 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }



    // Store a new data
    public function store(StoreRequest $request)
    {
        try {
            $data = $this->user->store($request);
            // return $this->apiResponse(new UserResource($data), 'Data Stored Successfully', 200);
            return $this->apiResponse($data, 'Data Stored Successfully', 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }



    // Update an existing data
    public function update(UpdateRequest $request, $id)
    {
        try {
            $data = $this->user->update($request, $id);
            // return $this->apiResponse(new UserResource($data), 'Data Updated Successfully', 200);
            return $this->apiResponse($data, 'Data Updated Successfully', 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }



    // Delete a data
    public function destroy($id)
    {
        try {
            $data = $this->user->destroy($id);
            return $this->apiResponse(null,'Data Deleted Sucessfully',200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
