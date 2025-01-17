<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Traits\ApiResponseTrait;
use App\Resources\Role\RoleResource;
use App\Repositories\Role\RoleInterface;
use App\Http\Requests\Role\StoreRequest;
use App\Http\Requests\Role\UpdateRequest;
use App\Http\Requests\Role\StorePermissionRequest;

class RoleController extends Controller
{
    use ApiResponseTrait;

    protected $role;

    function __construct(RoleInterface $role)
    {
        $this->role = $role;

        // $this->middleware('permission:read-role', ['only' => ['index']]);
        // $this->middleware('permission:show-role', ['only' => ['show']]);
        // $this->middleware('permission:create-role', ['only' => ['store']]);
        // $this->middleware('permission:update-role', ['only' => ['update']]);
        // $this->middleware('permission:delete-role', ['only' => ['destroy']]);
    }



    // Fetch data
    public function index(Request $request)
    {
        try {
            $data = $this->role->index($request);
            // return $this->apiResponse(RoleResource::collection($data), 'Data Returned Successfully', 200);
            return $this->apiResponse($data, 'Data Returned Successfully', 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }



    // Show an existing data
    public function show($id)
    {
        try {
            $data = $this->role->show($id);
            // return $this->apiResponse(new RoleResource($data), 'Data Returned Successfully', 200);
            return $this->apiResponse($data, 'Data Returned Successfully', 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }



    // Store a new data
    public function store(StoreRequest $request)
    {
        try {
            $data = $this->role->store($request);
            // return $this->apiResponse(new RoleResource($data), 'Data Stored Successfully', 200);
            return $this->apiResponse($data, 'Data Stored Successfully', 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }



    // Update an existing data
    public function update(UpdateRequest $request, $id)
    {
        try {
            $data = $this->role->update($request, $id);
            // return $this->apiResponse(new RoleResource($data), 'Data Updated Successfully', 200);
            return $this->apiResponse($data, 'Data Updated Successfully', 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }



    // Delete a data
    public function destroy($id)
    {
        try {
            $data = $this->role->destroy($id);
            return $this->apiResponse(null,'Data Deleted Sucessfully',200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }



    // Store a new data
    public function storePermission(StorePermissionRequest $request)
    {
        $data = $this->role->storePermission($request);
    }
}
