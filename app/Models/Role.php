<?php

use Jenssegers\Mongodb\Eloquent\Model as Eloquent;

class Role extends Eloquent
{
    protected $guarded = [];



    // Define the relationship to permissions (many-to-many)
    public function permissions()
    {
        return $this->belongsToMany(
            config('permission.models.permission'), // Reference to Permission model
            'role_has_permissions',                  // Pivot table
            'role_id',                               // Foreign key for Role in pivot
            'permission_id'                         // Foreign key for Permission in pivot
        );
    }
}
