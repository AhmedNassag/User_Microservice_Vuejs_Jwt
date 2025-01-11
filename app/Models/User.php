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
        $permissions = $this->userPermissions(); // Retrieve permission names

        return [
            /*'user' => [
                'id'    => $this->id,
                'name'  => $this->name,
                'email' => $this->email,
            ],*/
            'roles'       => $roles->pluck('name'), // The roles will be returned as an array of names
            'permissions' => $permissions->pluck('name'), // The permissions will be returned as an array of names
        ];
    }


    // Define the user_roles relationship
    public function userRoles()
    {
        $role_ids = \DB::table('model_has_roles')->where('model_id', $this->id)->where('model_type', 'App\Models\User')->pluck('role_id')->all();
        $roles    = \DB::table('roles')->whereIn('_id', $role_ids)->get();

        return $roles;
        // return ['Admin'];
    }



    // Define the userPermissions relationship
    public function userPermissions()
    {
        $roles         = $this->userRoles();
        $permissionIds = [];
        foreach ($roles as $role)
        {
            if (isset($role['permission_id']))
            {
                $permissionIds = array_merge($permissionIds, $role['permission_id']);
            }
        }
        $permissionIds = array_unique($permissionIds);
        $permissions   = \DB::table('permissions')->whereIn('_id', $permissionIds)->get();

        return $permissions;
        // return ['read-files','show-files','create-files','update-files','delete-files'];
    }
}
