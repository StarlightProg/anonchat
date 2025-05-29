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
        // собираем стандартную информацию по запросу
        $message = sprintf(
            '%s %s %s',
            $request->getMethod(),
            $request->getRequestUri(),
            $request->server->get('SERVER_PROTOCOL')
        );
        // логируем заголовки запроса (только важные)
        Log::channel($this->channel)->info("[REQUEST_HEADERS] $message", [
            'ip' => $request->ip(),
            'user-agent' => $request->userAgent(),
            'authorization' => $request->header('Authorization'),
            'X-TIMEZONE-OFFSET' => $request->header('X-TIMEZONE-OFFSET'),
        ]);
        // логируем переданные в запрос параметры (в query или в body)
        Log::info("[REQUEST_PARAMS] $message", $request->all());
        // пропускаем запрос дальше и ловим результат
        $response = $next($request);
        // получаем ответ от сервера
        $context = $response->getOriginalContent();
        if (is_array($context)) {
            // получаем HTTP код ответа
            $status = $response->status();
            if ($status == 200) {
                // если ответ успешный
                $message = "[RESPONSE_SUCCESS] $message";
                // запись в лог
                Log::channel($this->channel)->info($message, $context);
            } else {
                // если ошибка
                $message = "[RESPONSE_FAILURE] $message";
                // запись в лог
                Log::channel($this->channel)->error($message, $context);
            }
        }

        $response->header('Content-Type', 'application/json');
        return $response;
    }
}
