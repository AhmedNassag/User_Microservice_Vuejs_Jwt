<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Notifications\Notifiable;
use Tymon\JWTAuth\Contracts\JWTSubject;
// use Illuminate\Foundation\Auth\User as Authenticatable;
// use MongoDB\Laravel\Auth\User as Authenticatable;
use Jenssegers\Mongodb\Auth\User as Authenticatable;
use Spatie\Permission\Traits\HasRoles;


class User extends Authenticatable implements JWTSubject
{
    use Notifiable;
    use HasRoles;


    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];


    public function getJWTIdentifier()
    {
        return $this->getKey();
    }
    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        $roles       = $this->userRoles(); // Retrieve role names
        $permissions = $this->role(); // Retrieve permission names

        return [
            'user' => [
                'id'    => $this->id,
                'name'  => $this->name,
                'email' => $this->email,
            ],
            'roles'       => $roles, // The roles will be returned as an array of names
            'permissions' => $permissions, // The permissions will be returned as an array of names
        ];
    }


    // Define the user_roles relationship
    public function userRoles()
    {
        // Since the role_id is an array, we assume the role data is being referenced by role_id
        // This fetches roles based on the role_id array in the User model
        return $this->belongsToMany(Role::class,
            'id',        // Foreign key on the pivot table for this model
            'role_id'          // Foreign key for the Role model
        );
    }



    // Define the userPermissions relationship
    public function userPermissions()
    {
        // Get all permissions linked to the user's roles
        return $this->userRoles()->with('permissions'); // Assuming you have a 'permissions' relation in the Role model
    }
}
