<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Laravel\Sanctum\PersonalAccessToken;
use Illuminate\Support\Facades\Log;

class AuthSanctumApi
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        // Extract token from Authorization header
        $token = $request->bearerToken();
        
        Log::debug('AuthSanctumApi - Token received:', ['token' => substr($token ?? '', 0, 20)]);
        
        if (!$token) {
            return response()->json([
                'status' => false,
                'message' => 'Missing authorization token',
            ], 401);
        }

        // Sanctum tokens are formatted as: {ID}|{TOKEN}
        if (strpos($token, '|') !== false) {
            list($id, $tokenPart) = explode('|', $token, 2);
            Log::debug('AuthSanctumApi - Token split:', ['id' => $id, 'tokenPart' => substr($tokenPart, 0, 20)]);
        } else {
            $id = null;
            $tokenPart = $token;
        }

        // Lookup the token
        if ($id) {
            $accessToken = PersonalAccessToken::find($id);
            Log::debug('AuthSanctumApi - Found token by ID:', ['id' => $id, 'found' => $accessToken ? 'yes' : 'no']);
        } else {
            $accessToken = PersonalAccessToken::where('token', hash('sha256', $tokenPart))->first();
            Log::debug('AuthSanctumApi - Found token by hash:', ['found' => $accessToken ? 'yes' : 'no']);
        }
        
        if (!$accessToken) {
            Log::warning('AuthSanctumApi - Token not found in database');
            return response()->json([
                'status' => false,
                'message' => 'Invalid authorization token - not found',
            ], 401);
        }
        
        // Verify token hash
        if (!hash_equals($accessToken->token, hash('sha256', $tokenPart))) {
            Log::warning('AuthSanctumApi - Token hash mismatch');
            return response()->json([
                'status' => false,
                'message' => 'Invalid authorization token - hash mismatch',
            ], 401);
        }

        Log::debug('AuthSanctumApi - Token verified', ['user_id' => $accessToken->tokenable->id]);

        // Set the authenticated user on the request
        $request->setUserResolver(function () use ($accessToken) {
            return $accessToken->tokenable;
        });

        return $next($request);
    }
}
