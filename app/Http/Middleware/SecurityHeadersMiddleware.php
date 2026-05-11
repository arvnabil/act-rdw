<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SecurityHeadersMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Security Headers
        $response->headers->set('X-Frame-Options', 'SAMEORIGIN');
        $response->headers->set('X-Content-Type-Options', 'nosniff');
        $response->headers->set('X-XSS-Protection', '1; mode=block');
        $response->headers->set('Referrer-Policy', 'no-referrer-when-downgrade');
        $response->headers->set('Content-Security-Policy', implode('; ', [
            "default-src 'self'",
            "script-src 'self' 'unsafe-inline' 'unsafe-eval' http://localhost:5173 https://www.google.com https://www.gstatic.com https://www.googletagmanager.com https://webchat.qontak.com https://static.cloudflareinsights.com https://translate.google.com https://translate.googleapis.com https://translate-pa.googleapis.com https://googleads.g.doubleclick.net https://*.googleadservices.com",
            "style-src 'self' 'unsafe-inline' http://localhost:5173 https://fonts.googleapis.com https://webchat.qontak.com https://www.gstatic.com",
            "font-src 'self' https://fonts.gstatic.com data:",
            "img-src 'self' data: https: http://localhost:5173",
            "frame-src 'self' https://www.google.com https://webchat.qontak.com",
            "connect-src 'self' http://localhost:5173 ws://localhost:5173 https://*.google-analytics.com https://*.analytics.google.com https://*.googletagmanager.com https://*.google.com https://*.googleadservices.com https://googleads.g.doubleclick.net https://webchat.qontak.com https://static.cloudflareinsights.com https://translate.googleapis.com https://translate-pa.googleapis.com",

        ]));


        return $response;
    }
}
