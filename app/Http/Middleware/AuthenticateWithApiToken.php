<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AuthenticateWithApiToken
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string $type): Response
    {
        $token = $request->bearerToken();
        
        $user = User::where('api_token', $token)->first();

        if ($user === null) {
            return response()->json(['message' => 'Unauthorized'],401);
        }

        if (!empty($type) && $user->type !== $type) {
            return response()->json(['message'=> 'Forbidden'], 403);
        }

        if (empty($user->password)) {
            return response()->json(['message'=> 'User password is empty'],403);
        }

        $request->setUserResolver(fn() => $user);

        return $next($request);
    }
}
