<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ApiMonitor
{
    protected $channel = 'api';

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $message = sprintf(
            '%s %s %s',
            $request->getMethod(),
            $request->getRequestUri(),
            $request->server->get('SERVER_PROTOCOL')
        );
        
        $response = $next($request);

        $response->header('Content-Type', 'application/json');
        return $response;
    }
}
