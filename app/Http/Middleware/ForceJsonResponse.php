<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Memaksa semua request ke /api dianggap meminta JSON,
 * sehingga error (401, 404, 422) selalu dikembalikan dalam bentuk JSON
 * walaupun klien lupa mengirim header "Accept: application/json".
 */
class ForceJsonResponse
{
    public function handle(Request $request, Closure $next): Response
    {
        $request->headers->set('Accept', 'application/json');

        return $next($request);
    }
}