<?php

namespace App\Http\Middleware;

use App\Models\AuditLog;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AuditLogger
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Only log authenticated users and mutating requests to avoid noise
        if (auth()->check() && in_array($request->method(), ['POST','PUT','PATCH','DELETE'])) {
            try {
                $route = optional($request->route())->getName();
                $user = $request->user();

                // Remove sensitive fields
                $payload = collect($request->all())
                    ->except(['password','password_confirmation','_token'])
                    ->toArray();

                AuditLog::create([
                    'user_id'    => $user?->id,
                    'user_email' => $user?->email,
                    'method'     => $request->method(),
                    'route'      => $route,
                    'url'        => $request->fullUrl(),
                    'action'     => $route ?? $request->path(),
                    'ip_address' => $request->ip(),
                    'user_agent' => (string) $request->userAgent(),
                    'payload'    => $payload,
                ]);
            } catch (\Throwable $e) {
                // Silently ignore logging errors to not block user flow
            }
        }

        return $response;
    }
}
