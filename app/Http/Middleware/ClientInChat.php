<?php

namespace App\Http\Middleware;

use App\Exceptions\ApiException;
use App\Models\ClientsInChat;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class ClientInChat
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $unauthorized = new ApiException("You're not member of chat", 403, ApiException::FORBIDDEN);

        $group_id = $request->group_id ?? $request->route('group_id');

        Log::info("group_iddd " . $group_id);

        $chat = ClientsInChat::where('client_id', $request->user()->id)
                    ->where('group_id', $group_id);

        if (!$chat->exists()) {
            throw $unauthorized;
        }

        Log::info("chat_first " . json_encode($chat->first()));
        Log::info("chat_first2 " . json_encode($chat->first()->lastMessages()));

        $request->merge(['chat' => $chat->first()]);
        
        return $next($request);
    }
}
