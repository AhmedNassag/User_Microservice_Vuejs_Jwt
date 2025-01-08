<?php

namespace App\Repositories\Role;

interface RoleInterface
{
    public function index($request);

    public function show($id);

    public function store($request);
    
    public function update($request, $id);
    
    public function destroy($id);

    public function storePermission($request);
}
