<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class PaginateRoute
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $page = $request->input('page', 1);
        $limit = $request->input('limit', 10);

        $request->request->set('page', $page);
        $request->request->set('page', $limit);

        return $next($request);
    }
}
