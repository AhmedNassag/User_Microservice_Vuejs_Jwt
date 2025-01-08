<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class JwtMiddleware
{

    public function handle(Request $request, Closure $next)
    {
        try
        {
            // $user = \Tymon\JWTAuth\Facades\JWTAuth::parseToken()->authenticate();
            $token = \Tymon\JWTAuth\Facades\JWTAuth::getToken();
            $apy   = \Tymon\JWTAuth\Facades\JWTAuth::getPayload($token)->toArray();
        }
        catch (\Exception $e)
        {
            if ($e instanceof \Tymon\JWTAuth\Exceptions\TokenInvalidException)
            {
                return response()->json(['status' => 'Token is Invalid']);
            }
            else if ($e instanceof \Tymon\JWTAuth\Exceptions\TokenExpiredException)
            {
                return response()->json(['status' => 'Token is Expired']);
            }
            else
            {
                return response()->json(['status' => 'Token not found']);
            }
        }

        return $next($request);
    }

}
