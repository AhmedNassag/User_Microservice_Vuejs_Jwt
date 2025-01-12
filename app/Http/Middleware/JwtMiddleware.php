<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;

class JwtMiddleware
{

    public function handle(Request $request, Closure $next)
    {
        try
        {
            $token       = JWTAuth::getToken();
            $payload     = JWTAuth::getPayload($token)->toArray();
            $permissions = $payload['permissions'] ?? [];
            // Map permissions to routes 'route_name' => 'permission_name',
            $permissionMappings = [
                'roles.index'   => 'read-role',
                'roles.show'    => 'show-role',
                'roles.store'   => 'create-role',
                'roles.update'  => 'update-role',
                'roles.destroy' => 'delete-role',

                'users.index'   => 'read-user',
                'users.show'    => 'show-user',
                'users.store'   => 'create-user',
                'users.update'  => 'update-user',
                'users.destroy' => 'delete-user',
            ];
            // Get current route name
            $routeName = $request->route()->getName();
            // Check if the current route has a mapped permission
            if (isset($permissionMappings[$routeName]))
            {
                $requiredPermission = $permissionMappings[$routeName];
                // Deny access if the user lacks the required permission
                if (!in_array($requiredPermission, $permissions))
                {
                    return response()->json(['error' => 'You Does Not Have The Right Permissions', 'status' => 403], 403);
                }
            }
        }
        catch (\Tymon\JWTAuth\Exceptions\TokenExpiredException $e)
        {
            return response()->json(['message' => 'Token Is Expired', 'status' => 500], 500);
        }
        catch (\Tymon\JWTAuth\Exceptions\TokenInvalidException $e)
        {
            return response()->json(['message' => 'Token Is Invalid', 'status' => 500], 500);
        }
        catch (\Tymon\JWTAuth\Exceptions\JWTException $e)
        {
            return response()->json(['message' =>  $e->getMessage(), 'status' => 500], 500);
        }


        return $next($request);
    }

}
