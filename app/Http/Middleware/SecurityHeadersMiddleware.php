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
            "script-src 'self' 'unsafe-inline' 'unsafe-eval' https://www.google.com https://www.gstatic.com https://www.googletagmanager.com https://webchat.qontak.com https://static.cloudflareinsights.com https://translate.google.com https://translate.googleapis.com",
            "style-src 'self' 'unsafe-inline' https://fonts.googleapis.com https://webchat.qontak.com",
            "font-src 'self' https://fonts.gstatic.com data:",
            "img-src 'self' data: https:",
            "frame-src 'self' https://www.google.com https://webchat.qontak.com",
            "connect-src 'self' https://www.google-analytics.com https://www.googletagmanager.com https://webchat.qontak.com https://static.cloudflareinsights.com https://translate.googleapis.com",
        ]));

        return $response;
    }
}
